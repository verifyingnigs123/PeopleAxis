<?php

namespace App\Libraries;

class TeamPerformanceAnalytics
{
    public function build(array $teamContext, array $filters = []): array
    {
        $db = \Config\Database::connect();

        $selectedMonth = preg_match('/^\d{4}-\d{2}$/', (string) ($filters['month'] ?? ''))
            ? (string) $filters['month']
            : date('Y-m');

        $defaultFocusStart = $selectedMonth . '-01';
        $defaultFocusEnd = date('Y-m-t', strtotime($defaultFocusStart));

        $focusStart = $this->normalizeDate((string) ($filters['start_date'] ?? ''), $defaultFocusStart);
        $focusEnd = $this->normalizeDate((string) ($filters['end_date'] ?? ''), $defaultFocusEnd);

        if (strtotime($focusStart) > strtotime($focusEnd)) {
            [$focusStart, $focusEnd] = [$focusEnd, $focusStart];
        }

        $focusDays = max((int) floor((strtotime($focusEnd) - strtotime($focusStart)) / 86400) + 1, 1);
        $previousEnd = date('Y-m-d', strtotime($focusStart . ' -1 day'));
        $previousStart = date('Y-m-d', strtotime($previousEnd . ' -' . ($focusDays - 1) . ' day'));

        $teamMembers = $this->fetchTeamMembers($db, $teamContext['employeeIds'] ?? []);
        $managedDepartments = $teamContext['departments'] ?? [];

        if ($teamMembers === []) {
            return $this->emptyPayload($managedDepartments, $selectedMonth, $focusStart, $focusEnd, $previousStart, $previousEnd, $filters);
        }

        $employeeIds = array_map('intval', array_column($teamMembers, 'id'));
        $analysisStart = $previousStart;
        $analysisEnd = $focusEnd;

        $attendanceRows = $db->table('attendance_logs')
            ->select('employee_id, date, time_in, time_out, status')
            ->whereIn('employee_id', $employeeIds)
            ->where('date >=', $analysisStart)
            ->where('date <=', $analysisEnd)
            ->get()
            ->getResultArray();

        $leaveRows = $db->table('leave_requests')
            ->select('employee_id, start_date, end_date')
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'approved')
            ->where('early_returned_at', null)
            ->where('end_date >=', $analysisStart)
            ->where('start_date <=', $analysisEnd)
            ->get()
            ->getResultArray();

        $attendanceIndex = $this->buildAttendanceIndex($attendanceRows);
        $leaveIndex = $this->buildLeaveIndex($leaveRows, $analysisStart, $analysisEnd);

        $focusWorkdays = $this->getWorkdays($focusStart, $focusEnd);
        $previousWorkdays = $this->getWorkdays($previousStart, $previousEnd);

        $employeeRows = [];
        foreach ($teamMembers as $member) {
            $current = $this->calculateEmployeeMetrics($member, $focusWorkdays, $attendanceIndex, $leaveIndex);
            $previous = $this->calculateEmployeeMetrics($member, $previousWorkdays, $attendanceIndex, $leaveIndex);

            $employeeRows[] = array_merge($member, $current, [
                'performance_delta'  => round($current['performance_score'] - $previous['performance_score'], 1),
                'attendance_delta'   => round($current['attendance_rate'] - $previous['attendance_rate'], 1),
                'productivity_delta' => round($current['productivity_score'] - $previous['productivity_score'], 1),
                'tasks_delta'        => (int) $current['completed_tasks'] - (int) $previous['completed_tasks'],
            ]);
        }

        usort($employeeRows, static function (array $left, array $right): int {
            $scoreCompare = $right['performance_score'] <=> $left['performance_score'];
            if ($scoreCompare !== 0) {
                return $scoreCompare;
            }

            return $right['attendance_rate'] <=> $left['attendance_rate'];
        });

        $previousEmployeeRows = [];
        foreach ($teamMembers as $member) {
            $previousEmployeeRows[] = $this->calculateEmployeeMetrics($member, $previousWorkdays, $attendanceIndex, $leaveIndex);
        }

        $currentPerformanceAverage = $this->averageValue(array_column($employeeRows, 'performance_score'));
        $currentAttendanceAverage = $this->averageValue(array_column($employeeRows, 'attendance_rate'));
        $currentProductivityAverage = $this->averageValue(array_column($employeeRows, 'productivity_score'));
        $previousPerformanceAverage = $this->averageValue(array_column($previousEmployeeRows, 'performance_score'));
        $previousAttendanceAverage = $this->averageValue(array_column($previousEmployeeRows, 'attendance_rate'));
        $previousProductivityAverage = $this->averageValue(array_column($previousEmployeeRows, 'productivity_score'));

        $currentTasksCompleted = array_sum(array_map('intval', array_column($employeeRows, 'completed_tasks')));
        $currentPendingTasks = array_sum(array_map('intval', array_column($employeeRows, 'pending_tasks')));
        $previousTasksCompleted = array_sum(array_map('intval', array_column($previousEmployeeRows, 'completed_tasks')));
        $previousPendingTasks = array_sum(array_map('intval', array_column($previousEmployeeRows, 'pending_tasks')));

        $currentLeaveEmployees = [];
        $previousLeaveEmployees = [];
        $currentWorkedHours = 0.0;
        $currentOvertimeHours = 0.0;
        foreach ($employeeRows as $row) {
            if (! empty($row['leave_days'])) {
                $currentLeaveEmployees[$row['id']] = true;
            }
            $currentWorkedHours += (float) $row['worked_hours'];
            $currentOvertimeHours += (float) $row['overtime_hours'];
        }
        foreach ($previousEmployeeRows as $row) {
            if (! empty($row['leave_days'])) {
                $previousLeaveEmployees[$row['id']] = true;
            }
        }

        $currentAttendanceOnTime = array_sum(array_map(static fn (array $row): int => (int) $row['present_days'] + (int) $row['late_days'], $employeeRows));
        $currentAttendanceRate = count($focusWorkdays) * max(count($employeeRows), 1) > 0
            ? round(($currentAttendanceOnTime / (count($focusWorkdays) * max(count($employeeRows), 1))) * 100, 1)
            : 0.0;

        $trendData = [
            'daily'   => $this->buildDailySeries($teamMembers, $attendanceIndex, $leaveIndex, $focusStart, $focusEnd, $previousStart, $previousEnd),
            'weekly'  => $this->buildWeeklySeries($teamMembers, $attendanceIndex, $leaveIndex, $focusStart, $focusEnd, $previousStart, $previousEnd),
            'monthly' => $this->buildMonthlySeries($teamMembers, $attendanceIndex, $leaveIndex, $focusEnd),
        ];

        $distribution = [
            'Excellent' => 0,
            'Good' => 0,
            'Average' => 0,
            'Needs Improvement' => 0,
        ];
        $attendanceCorrelation = [];
        $productivityBars = [];
        foreach ($employeeRows as $row) {
            $distribution[$row['performance_category']]++;
            $attendanceCorrelation[] = [
                'x' => (float) $row['attendance_rate'],
                'y' => (float) $row['performance_score'],
                'label' => $row['employee_name'],
            ];
            $productivityBars[] = [
                'label' => $row['employee_name'],
                'value' => (float) $row['productivity_score'],
            ];
        }

        $departmentInsights = $this->buildDepartmentInsights($employeeRows);
        $aiInsights = $this->buildInsights(
            $employeeRows,
            $currentPerformanceAverage,
            $previousPerformanceAverage,
            $currentAttendanceAverage,
            $previousAttendanceAverage,
            $currentProductivityAverage,
            $previousProductivityAverage,
            $departmentInsights['highest'] ?? null,
            $departmentInsights['lowest'] ?? null
        );
        $recommendations = $this->buildRecommendations($employeeRows);

        $summaryCards = $this->buildSummaryCards(
            count($employeeRows),
            count($employeeRows),
            $currentPerformanceAverage,
            $currentAttendanceRate,
            $currentProductivityAverage,
            $currentTasksCompleted,
            $currentPendingTasks,
            count($currentLeaveEmployees),
            $previousPerformanceAverage,
            $previousAttendanceAverage,
            $previousProductivityAverage,
            $previousTasksCompleted,
            $previousPendingTasks,
            count($previousLeaveEmployees)
        );

        $filterOptions = [
            'departments' => $this->uniqueOptions(array_map(static fn (array $row): array => [
                'value' => (string) ($row['department_id'] ?? ''),
                'label' => (string) ($row['department_name'] ?? 'Unassigned'),
            ], $employeeRows)),
            'teams' => $this->uniqueOptions(array_map(static fn (array $row): array => [
                'value' => (string) ($row['position_name'] ?? 'Unassigned'),
                'label' => (string) ($row['position_name'] ?? 'Unassigned'),
            ], $employeeRows)),
            'attendance_statuses' => [
                ['value' => '', 'label' => 'All'],
                ['value' => 'excellent', 'label' => 'Excellent'],
                ['value' => 'good', 'label' => 'Good'],
                ['value' => 'average', 'label' => 'Average'],
                ['value' => 'watch', 'label' => 'Watch'],
            ],
            'performance_categories' => [
                ['value' => '', 'label' => 'All'],
                ['value' => 'Excellent', 'label' => 'Excellent'],
                ['value' => 'Good', 'label' => 'Good'],
                ['value' => 'Average', 'label' => 'Average'],
                ['value' => 'Needs Improvement', 'label' => 'Needs Improvement'],
            ],
        ];

        $search = strtolower(trim((string) ($filters['employee_name'] ?? '')));
        $departmentFilter = trim((string) ($filters['department_id'] ?? ''));
        $teamFilter = trim((string) ($filters['team'] ?? ''));
        $attendanceStatusFilter = trim((string) ($filters['attendance_status'] ?? ''));
        $performanceCategoryFilter = trim((string) ($filters['performance_category'] ?? ''));

        if ($search !== '' || $departmentFilter !== '' || $teamFilter !== '' || $attendanceStatusFilter !== '' || $performanceCategoryFilter !== '') {
            $employeeRows = array_values(array_filter($employeeRows, static function (array $row) use ($search, $departmentFilter, $teamFilter, $attendanceStatusFilter, $performanceCategoryFilter): bool {
                if ($search !== '') {
                    $haystack = strtolower(trim((string) ($row['employee_name'] ?? '') . ' ' . ($row['employee_code'] ?? '') . ' ' . ($row['department_name'] ?? '') . ' ' . ($row['position_name'] ?? '')));
                    if (strpos($haystack, $search) === false) {
                        return false;
                    }
                }

                if ($departmentFilter !== '' && (string) ($row['department_id'] ?? '') !== $departmentFilter) {
                    return false;
                }

                if ($teamFilter !== '' && strtolower((string) ($row['position_name'] ?? '')) !== strtolower($teamFilter)) {
                    return false;
                }

                if ($attendanceStatusFilter !== '' && strtolower((string) ($row['attendance_status'] ?? '')) !== strtolower($attendanceStatusFilter)) {
                    return false;
                }

                if ($performanceCategoryFilter !== '' && (string) ($row['performance_category'] ?? '') !== $performanceCategoryFilter) {
                    return false;
                }

                return true;
            }));
        }

        foreach ($employeeRows as $index => &$row) {
            $row['rank'] = $index + 1;
            $badges = [];
            if ($index === 0) {
                $badges[] = 'Top Performer';
                if ((float) $row['performance_score'] >= 92) {
                    $badges[] = 'Employee of the Month';
                }
            }
            if ((float) $row['performance_delta'] > 0 && abs((float) $row['performance_delta']) >= 4) {
                $badges[] = 'Most Improved';
            }
            if ((float) $row['performance_score'] >= 90) {
                $badges[] = 'Excellent Employee';
            }
            $row['badge_list'] = array_values(array_unique($badges));
        }
        unset($row);

        $mostImprovedEmployee = null;
        foreach ($employeeRows as $row) {
            if ($mostImprovedEmployee === null || ($row['performance_delta'] ?? 0) > ($mostImprovedEmployee['performance_delta'] ?? 0)) {
                $mostImprovedEmployee = $row;
            }
        }

        if ($mostImprovedEmployee !== null && (($mostImprovedEmployee['performance_delta'] ?? 0) > 0)) {
            $aiInsights[] = sprintf('%s posted the biggest improvement at +%s points.', $mostImprovedEmployee['employee_name'], number_format((float) $mostImprovedEmployee['performance_delta'], 1));
        }

        $attendanceChange = round($currentAttendanceAverage - $previousAttendanceAverage, 1);
        $productivityChange = round($currentProductivityAverage - $previousProductivityAverage, 1);

        return [
            'periodLabel' => date('F j, Y', strtotime($focusStart)) . ' to ' . date('F j, Y', strtotime($focusEnd)),
            'selectedMonth' => $selectedMonth,
            'selectedDate' => $focusEnd,
            'focusStart' => $focusStart,
            'focusEnd' => $focusEnd,
            'previousStart' => $previousStart,
            'previousEnd' => $previousEnd,
            'managedDepartments' => $managedDepartments,
            'teamCount' => count($teamMembers),
            'activeEmployees' => count($teamMembers),
            'summaryCards' => $summaryCards,
            'trendCharts' => $trendData,
            'performanceDistribution' => [
                'labels' => array_keys($distribution),
                'values' => array_values($distribution),
            ],
            'attendanceAnalytics' => [
                'attendance_rate' => $currentAttendanceRate,
                'absences' => array_sum(array_map(static fn (array $row): int => (int) ($row['explicit_absent_days'] ?? 0), $employeeRows)),
                'late_arrivals' => array_sum(array_map(static fn (array $row): int => (int) $row['late_days'], $employeeRows)),
                'overtime_hours' => round($currentOvertimeHours, 1),
                'attendance_change' => $attendanceChange,
                'attendance_trend' => $this->trendDirection($currentAttendanceRate, $previousAttendanceAverage),
                'correlation' => $attendanceCorrelation,
            ],
            'productivityMetrics' => [
                'assigned_tasks' => array_sum(array_map(static fn (array $row): int => (int) $row['assigned_tasks'], $employeeRows)),
                'completed_tasks' => $currentTasksCompleted,
                'completion_rate' => $this->completionRate($currentTasksCompleted, array_sum(array_map(static fn (array $row): int => (int) $row['assigned_tasks'], $employeeRows))),
                'average_completion_time' => $this->averageCompletionTime($currentWorkedHours, $currentTasksCompleted),
                'productivity_score' => round($currentProductivityAverage, 1),
                'productivity_change' => $productivityChange,
                'bar_chart' => array_slice($productivityBars, 0, 12),
            ],
            'departmentInsights' => $departmentInsights,
            'aiInsights' => $aiInsights,
            'recommendations' => $recommendations,
            'leaderboard' => array_slice($employeeRows, 0, 10),
            'performanceRows' => $employeeRows,
            'filterOptions' => $filterOptions,
            'filters' => [
                'month' => $selectedMonth,
                'start_date' => $focusStart,
                'end_date' => $focusEnd,
                'employee_name' => (string) ($filters['employee_name'] ?? ''),
                'department_id' => $departmentFilter,
                'team' => $teamFilter,
                'attendance_status' => $attendanceStatusFilter,
                'performance_category' => $performanceCategoryFilter,
            ],
            'exportData' => [
                'summary' => $summaryCards,
                'leaderboard' => array_slice($employeeRows, 0, 10),
                'employees' => $employeeRows,
                'charts' => $trendData,
                'attendance' => [
                    'correlation' => $attendanceCorrelation,
                ],
                'departmentInsights' => $departmentInsights,
                'recommendations' => $recommendations,
            ],
        ];
    }

    private function emptyPayload(array $managedDepartments, string $selectedMonth, string $focusStart, string $focusEnd, string $previousStart, string $previousEnd, array $filters): array
    {
        return [
            'periodLabel' => date('F j, Y', strtotime($focusStart)) . ' to ' . date('F j, Y', strtotime($focusEnd)),
            'selectedMonth' => $selectedMonth,
            'selectedDate' => $focusEnd,
            'focusStart' => $focusStart,
            'focusEnd' => $focusEnd,
            'previousStart' => $previousStart,
            'previousEnd' => $previousEnd,
            'managedDepartments' => $managedDepartments,
            'teamCount' => 0,
            'activeEmployees' => 0,
            'summaryCards' => [],
            'trendCharts' => ['daily' => ['labels' => [], 'current' => [], 'previous' => []], 'weekly' => ['labels' => [], 'current' => [], 'previous' => []], 'monthly' => ['labels' => [], 'current' => [], 'previous' => []]],
            'performanceDistribution' => ['labels' => ['Excellent', 'Good', 'Average', 'Needs Improvement'], 'values' => [0, 0, 0, 0]],
            'attendanceAnalytics' => ['attendance_rate' => 0, 'absences' => 0, 'late_arrivals' => 0, 'overtime_hours' => 0, 'attendance_change' => 0, 'attendance_trend' => 'stable', 'correlation' => []],
            'productivityMetrics' => ['assigned_tasks' => 0, 'completed_tasks' => 0, 'completion_rate' => 0, 'average_completion_time' => 0, 'productivity_score' => 0, 'productivity_change' => 0, 'bar_chart' => []],
            'departmentInsights' => ['rows' => [], 'highest' => null, 'lowest' => null],
            'aiInsights' => ['No team members are assigned to this manager yet.'],
            'recommendations' => ['promotion' => [], 'coaching' => [], 'attendance' => [], 'training' => []],
            'leaderboard' => [],
            'performanceRows' => [],
            'filterOptions' => [
                'departments' => [],
                'teams' => [],
                'attendance_statuses' => [],
                'performance_categories' => [],
            ],
            'filters' => [
                'month' => $selectedMonth,
                'start_date' => $focusStart,
                'end_date' => $focusEnd,
                'employee_name' => (string) ($filters['employee_name'] ?? ''),
                'department_id' => (string) ($filters['department_id'] ?? ''),
                'team' => (string) ($filters['team'] ?? ''),
                'attendance_status' => (string) ($filters['attendance_status'] ?? ''),
                'performance_category' => (string) ($filters['performance_category'] ?? ''),
            ],
            'exportData' => [
                'summary' => [],
                'leaderboard' => [],
                'employees' => [],
                'charts' => [],
                'attendance' => ['correlation' => []],
                'departmentInsights' => ['rows' => [], 'highest' => null, 'lowest' => null],
                'recommendations' => ['promotion' => [], 'coaching' => [], 'attendance' => [], 'training' => []],
            ],
        ];
    }

    private function fetchTeamMembers($db, array $employeeIds): array
    {
        if ($employeeIds === []) {
            return [];
        }

        return $db->table('employees')
            ->select('employees.id, employees.employee_id, employees.first_name, employees.last_name, employees.email, employees.department_id, employees.position_id, employees.status, employees.account_status, departments.name as department_name, positions.name as position_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->whereIn('employees.id', $employeeIds)
            ->orderBy('departments.name', 'ASC')
            ->orderBy('employees.first_name', 'ASC')
            ->orderBy('employees.last_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function buildAttendanceIndex(array $attendanceRows): array
    {
        $index = [];
        foreach ($attendanceRows as $row) {
            $employeeId = (int) ($row['employee_id'] ?? 0);
            $date = (string) ($row['date'] ?? '');
            if ($employeeId <= 0 || $date === '') {
                continue;
            }

            $index[$employeeId][$date] = $row;
        }

        return $index;
    }

    private function buildLeaveIndex(array $leaveRows, string $analysisStart, string $analysisEnd): array
    {
        $index = [];
        foreach ($leaveRows as $row) {
            $employeeId = (int) ($row['employee_id'] ?? 0);
            $start = max($this->normalizeDate((string) ($row['start_date'] ?? ''), $analysisStart), $analysisStart);
            $end = min($this->normalizeDate((string) ($row['end_date'] ?? ''), $analysisEnd), $analysisEnd);

            if ($employeeId <= 0 || strtotime($start) > strtotime($end)) {
                continue;
            }

            $cursor = new \DateTimeImmutable($start);
            $endDate = new \DateTimeImmutable($end);
            while ($cursor <= $endDate) {
                $index[$employeeId][$cursor->format('Y-m-d')] = true;
                $cursor = $cursor->modify('+1 day');
            }
        }

        return $index;
    }

    private function calculateEmployeeMetrics(array $employee, array $workdays, array $attendanceIndex, array $leaveIndex): array
    {
        $employeeId = (int) ($employee['id'] ?? 0);
        $presentDays = 0;
        $lateDays = 0;
        $absentDays = 0;
        $explicitAbsentDays = 0;
        $leaveDays = 0;
        $workedHours = 0.0;
        $overtimeHours = 0.0;
        $lastAttendanceDate = null;

        foreach ($workdays as $date) {
            $isLeave = isset($leaveIndex[$employeeId][$date]);
            $record = $attendanceIndex[$employeeId][$date] ?? null;

            if ($isLeave) {
                $leaveDays++;
                continue;
            }

            if ($record === null) {
                $absentDays++;
                continue;
            }

            $lastAttendanceDate = $date;
            $status = strtolower((string) ($record['status'] ?? ''));

            if ($status === 'absent') {
                $absentDays++;
                $explicitAbsentDays++;
                continue;
            }

            if (in_array($status, ['late', 'half-day', 'half day'], true)) {
                $lateDays++;
            } else {
                $presentDays++;
            }

            $timeIn = (string) ($record['time_in'] ?? '');
            $timeOut = (string) ($record['time_out'] ?? '');

            if ($timeIn !== '' && $timeOut !== '') {
                $hours = max((strtotime('1970-01-01 ' . $timeOut) - strtotime('1970-01-01 ' . $timeIn)) / 3600, 0);
                $workedHours += $hours;
                $overtimeHours += max($hours - 8, 0);
            }
        }

        $scheduledDays = max(count($workdays), 1);
        $attendanceDays = $presentDays + $lateDays;
        $attendanceRate = round(($attendanceDays / $scheduledDays) * 100, 1);
        $punctualityRate = round(($presentDays / max($attendanceDays, 1)) * 100, 1);
        $efficiencyRate = round(min(($workedHours / max($scheduledDays * 8, 1)) * 100, 130), 1);
        $leavePenalty = max(100 - (($leaveDays / $scheduledDays) * 100), 0);
        $productivityScore = round(min(max(($efficiencyRate * 0.85) + (min($overtimeHours / max($scheduledDays, 1) * 10, 100) * 0.15), 0), 100), 1);
        $performanceScore = round(min(max(($attendanceRate * 0.42) + ($punctualityRate * 0.18) + ($productivityScore * 0.30) + ($leavePenalty * 0.10), 0), 100), 1);
        $assignedTasks = max($scheduledDays * 4, 1);
        $completedTasks = (int) round(min(($attendanceDays * 4) + $overtimeHours, $assignedTasks));

        // If the employee has no attendance and no worked hours recorded,
        // treat pending tasks as 0 so the dashboard doesn't show unstarted work.
        if ($attendanceDays === 0 && (int) round($workedHours) === 0) {
            $pendingTasks = 0;
        } else {
            $pendingTasks = max($assignedTasks - $completedTasks, 0);
        }
        $averageCompletionTime = $attendanceDays > 0 ? round($workedHours / $attendanceDays, 2) : 0.0;

        $performanceCategory = 'Needs Improvement';
        if ($performanceScore >= 90) {
            $performanceCategory = 'Excellent';
        } elseif ($performanceScore >= 75) {
            $performanceCategory = 'Good';
        } elseif ($performanceScore >= 60) {
            $performanceCategory = 'Average';
        }

        $attendanceStatus = 'Watch';
        if ($attendanceRate >= 90) {
            $attendanceStatus = 'Excellent';
        } elseif ($attendanceRate >= 80) {
            $attendanceStatus = 'Good';
        } elseif ($attendanceRate < 65) {
            $attendanceStatus = 'At Risk';
        }

        return [
            'id' => $employeeId,
            'employee_name' => trim((string) ($employee['first_name'] ?? '') . ' ' . (string) ($employee['last_name'] ?? '')),
            'employee_code' => (string) ($employee['employee_id'] ?? ''),
            'department_id' => (int) ($employee['department_id'] ?? 0),
            'department_name' => (string) ($employee['department_name'] ?? 'Unassigned'),
            'position_name' => (string) ($employee['position_name'] ?? 'Unassigned'),
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'explicit_absent_days' => $explicitAbsentDays,
            'leave_days' => $leaveDays,
            'worked_hours' => round($workedHours, 1),
            'overtime_hours' => round($overtimeHours, 1),
            'attendance_rate' => $attendanceRate,
            'punctuality_rate' => $punctualityRate,
            'efficiency_rate' => $efficiencyRate,
            'assigned_tasks' => $assignedTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'average_completion_time' => $averageCompletionTime,
            'productivity_score' => $productivityScore,
            'performance_score' => $performanceScore,
            'performance_category' => $performanceCategory,
            'attendance_status' => $attendanceStatus,
            'status_class' => $this->statusClass($performanceCategory),
            'last_attendance_date' => $lastAttendanceDate,
        ];
    }

    private function buildDailySeries(array $teamMembers, array $attendanceIndex, array $leaveIndex, string $focusStart, string $focusEnd, string $previousStart, string $previousEnd): array
    {
        $currentDates = $this->dateRange($focusStart, $focusEnd);
        $previousDates = $this->dateRange($previousStart, $previousEnd);

        return [
            'labels' => array_map(static fn (string $date): string => date('M j', strtotime($date)), $currentDates),
            'current' => $this->scoresForDates($teamMembers, $attendanceIndex, $leaveIndex, $currentDates),
            'previous' => $this->scoresForDates($teamMembers, $attendanceIndex, $leaveIndex, $previousDates),
        ];
    }

    private function buildWeeklySeries(array $teamMembers, array $attendanceIndex, array $leaveIndex, string $focusStart, string $focusEnd, string $previousStart, string $previousEnd): array
    {
        $currentBuckets = $this->bucketScores($teamMembers, $attendanceIndex, $leaveIndex, $focusStart, $focusEnd);
        $previousBuckets = $this->bucketScores($teamMembers, $attendanceIndex, $leaveIndex, $previousStart, $previousEnd);

        $labels = [];
        foreach ($currentBuckets as $index => $_) {
            $labels[] = 'Week ' . ($index + 1);
        }

        return [
            'labels' => $labels,
            'current' => array_values($currentBuckets),
            'previous' => array_values($previousBuckets),
        ];
    }

    private function buildMonthlySeries(array $teamMembers, array $attendanceIndex, array $leaveIndex, string $focusEnd): array
    {
        $currentMonths = [];
        $previousMonths = [];
        $monthAnchor = new \DateTimeImmutable(date('Y-m-01', strtotime($focusEnd)));

        for ($i = 5; $i >= 0; $i--) {
            $currentMonths[] = $monthAnchor->modify('-' . $i . ' month')->format('Y-m');
        }

        $previousAnchor = $monthAnchor->modify('-6 month');
        for ($i = 5; $i >= 0; $i--) {
            $previousMonths[] = $previousAnchor->modify('-' . $i . ' month')->format('Y-m');
        }

        $labels = array_map(static fn (string $month): string => date('M Y', strtotime($month . '-01')), $currentMonths);

        return [
            'labels' => $labels,
            'current' => $this->scoresForMonths($teamMembers, $attendanceIndex, $leaveIndex, $currentMonths),
            'previous' => $this->scoresForMonths($teamMembers, $attendanceIndex, $leaveIndex, $previousMonths),
        ];
    }

    private function bucketScores(array $teamMembers, array $attendanceIndex, array $leaveIndex, string $start, string $end): array
    {
        $dates = $this->dateRange($start, $end);
        $bucketCount = max((int) ceil(count($dates) / 7), 1);
        $scores = array_fill(0, $bucketCount, []);

        foreach ($dates as $index => $date) {
            $bucket = min((int) floor($index / 7), $bucketCount - 1);
            $scores[$bucket][] = $this->scoreForDate($teamMembers, $attendanceIndex, $leaveIndex, $date);
        }

        return array_map([$this, 'averageValue'], $scores);
    }

    private function scoresForDates(array $teamMembers, array $attendanceIndex, array $leaveIndex, array $dates): array
    {
        $scores = [];
        foreach ($dates as $date) {
            $scores[] = $this->scoreForDate($teamMembers, $attendanceIndex, $leaveIndex, $date);
        }

        return $scores;
    }

    private function scoresForMonths(array $teamMembers, array $attendanceIndex, array $leaveIndex, array $months): array
    {
        $scores = [];
        foreach ($months as $month) {
            $start = $month . '-01';
            $end = date('Y-m-t', strtotime($start));
            $scores[] = $this->averageValue($this->scoresForDates($teamMembers, $attendanceIndex, $leaveIndex, $this->dateRange($start, $end)));
        }

        return $scores;
    }

    private function scoreForDate(array $teamMembers, array $attendanceIndex, array $leaveIndex, string $date): float
    {
        $scores = [];
        foreach ($teamMembers as $member) {
            $employeeId = (int) ($member['id'] ?? 0);

            if (isset($leaveIndex[$employeeId][$date])) {
                $scores[] = 88;
                continue;
            }

            $record = $attendanceIndex[$employeeId][$date] ?? null;
            if ($record === null) {
                $scores[] = 35;
                continue;
            }

            $status = strtolower((string) ($record['status'] ?? ''));
            if ($status === 'absent') {
                $score = 40;
            } elseif (in_array($status, ['late', 'half-day', 'half day'], true)) {
                $score = 82;
            } else {
                $score = 95;
            }

            $timeIn = (string) ($record['time_in'] ?? '');
            $timeOut = (string) ($record['time_out'] ?? '');
            if ($timeIn !== '' && $timeOut !== '') {
                $hours = max((strtotime('1970-01-01 ' . $timeOut) - strtotime('1970-01-01 ' . $timeIn)) / 3600, 0);
                $score += min(($hours / 8) * 8, 8);
            }

            $scores[] = min(max($score, 0), 100);
        }

        return round($this->averageValue($scores), 1);
    }

    private function buildDepartmentInsights(array $employeeRows): array
    {
        $departments = [];
        foreach ($employeeRows as $row) {
            $departmentName = (string) ($row['department_name'] ?? 'Unassigned');
            if (! isset($departments[$departmentName])) {
                $departments[$departmentName] = [
                    'name' => $departmentName,
                    'members' => 0,
                    'performance_total' => 0.0,
                    'productivity_total' => 0.0,
                    'attendance_total' => 0.0,
                ];
            }

            $departments[$departmentName]['members']++;
            $departments[$departmentName]['performance_total'] += (float) $row['performance_score'];
            $departments[$departmentName]['productivity_total'] += (float) $row['productivity_score'];
            $departments[$departmentName]['attendance_total'] += (float) $row['attendance_rate'];
        }

        $rows = [];
        foreach ($departments as $department) {
            $averageScore = $department['members'] > 0 ? round($department['performance_total'] / $department['members'], 1) : 0;
            $averageProductivity = $department['members'] > 0 ? round($department['productivity_total'] / $department['members'], 1) : 0;
            $averageAttendance = $department['members'] > 0 ? round($department['attendance_total'] / $department['members'], 1) : 0;

            $rows[] = [
                'name' => $department['name'],
                'members' => $department['members'],
                'average_score' => $averageScore,
                'average_productivity' => $averageProductivity,
                'average_attendance' => $averageAttendance,
                'status' => $averageScore >= 85 ? 'green' : ($averageScore >= 70 ? 'yellow' : 'red'),
            ];
        }

        usort($rows, static fn (array $left, array $right): int => $right['average_score'] <=> $left['average_score']);

        return [
            'rows' => $rows,
            'highest' => $rows[0] ?? null,
            'lowest' => $rows[count($rows) - 1] ?? null,
        ];
    }

    private function buildInsights(array $employeeRows, float $currentPerformanceAverage, float $previousPerformanceAverage, float $currentAttendanceAverage, float $previousAttendanceAverage, float $currentProductivityAverage, float $previousProductivityAverage, ?array $topDepartment, ?array $lowDepartment): array
    {
        $insights = [];
        $performanceDelta = round($currentPerformanceAverage - $previousPerformanceAverage, 1);
        $attendanceDelta = round($currentAttendanceAverage - $previousAttendanceAverage, 1);
        $productivityDelta = round($currentProductivityAverage - $previousProductivityAverage, 1);

        if (($employeeRows[0] ?? null) !== null) {
            $insights[] = sprintf('%s leads the team with a %s score.', $employeeRows[0]['employee_name'], number_format((float) $employeeRows[0]['performance_score'], 1));
        }

        $insights[] = sprintf('Performance trend is %s%s compared with the previous period.', $performanceDelta >= 0 ? '+' : '', number_format($performanceDelta, 1));
        $insights[] = sprintf('Attendance shifted by %s%s versus the previous period.', $attendanceDelta >= 0 ? '+' : '', number_format($attendanceDelta, 1));
        $insights[] = sprintf('Productivity moved by %s%s against the previous period.', $productivityDelta >= 0 ? '+' : '', number_format($productivityDelta, 1));

        $needsAttention = array_values(array_filter($employeeRows, static fn (array $row): bool => (float) $row['performance_score'] < 60));
        if ($needsAttention !== []) {
            $names = array_slice(array_map(static fn (array $row): string => $row['employee_name'], $needsAttention), 0, 3);
            $insights[] = 'Employees requiring attention: ' . implode(', ', $names) . '.';
        }

        if ($topDepartment !== null) {
            $insights[] = sprintf('%s is the strongest department at %.1f%%.', $topDepartment['name'], (float) $topDepartment['average_score']);
        }

        if ($lowDepartment !== null) {
            $insights[] = sprintf('%s is the lowest performing department at %.1f%% and should receive coaching support.', $lowDepartment['name'], (float) $lowDepartment['average_score']);
        }

        return $insights;
    }

    private function buildRecommendations(array $employeeRows): array
    {
        $promotion = [];
        $coaching = [];
        $attendance = [];
        $training = [];

        foreach ($employeeRows as $row) {
            $score = (float) $row['performance_score'];
            $attendanceRate = (float) $row['attendance_rate'];
            $productivity = (float) $row['productivity_score'];

            $summary = [
                'name' => $row['employee_name'],
                'score' => $score,
                'attendance_rate' => $attendanceRate,
                'productivity_score' => $productivity,
                'reason' => $this->buildRecommendationReason($row),
            ];

            if ($score >= 90 && $attendanceRate >= 95) {
                $promotion[] = $summary;
            }

            if ($score < 60) {
                $coaching[] = $summary;
            }

            if ($attendanceRate < 80 || (int) $row['late_days'] >= 3) {
                $attendance[] = $summary;
            }

            if (($score >= 60 && $score < 85) || $productivity < 75) {
                $training[] = $summary;
            }
        }

        return [
            'promotion' => array_slice($promotion, 0, 4),
            'coaching' => array_slice($coaching, 0, 4),
            'attendance' => array_slice($attendance, 0, 4),
            'training' => array_slice($training, 0, 4),
        ];
    }

    private function buildRecommendationReason(array $row): string
    {
        $parts = [];
        if ((float) $row['performance_score'] >= 90) {
            $parts[] = 'high performance';
        }
        if ((float) $row['attendance_rate'] >= 95) {
            $parts[] = 'excellent attendance';
        }
        if ((float) $row['productivity_score'] >= 90) {
            $parts[] = 'strong productivity';
        }
        if ((int) $row['late_days'] >= 3) {
            $parts[] = 'late arrivals need review';
        }

        return $parts === [] ? 'Review performance metrics' : implode(', ', $parts);
    }

    private function buildSummaryCards(int $teamCount, int $activeEmployees, float $currentPerformanceAverage, float $currentAttendanceRate, float $currentProductivityAverage, int $currentTasksCompleted, int $currentPendingTasks, int $currentLeaveCount, float $previousPerformanceAverage, float $previousAttendanceAverage, float $previousProductivityAverage, int $previousTasksCompleted, int $previousPendingTasks, int $previousLeaveCount): array
    {
        return [
            ['label' => 'Total Team Members', 'value' => number_format($teamCount), 'delta' => 0, 'delta_label' => 'Stable', 'icon' => 'fa-users', 'tone' => 'primary'],
            ['label' => 'Active Employees', 'value' => number_format($activeEmployees), 'delta' => 0, 'delta_label' => 'Stable', 'icon' => 'fa-user-check', 'tone' => 'success'],
            ['label' => 'Average Performance Score', 'value' => number_format($currentPerformanceAverage, 1) . '%', 'delta' => round($currentPerformanceAverage - $previousPerformanceAverage, 1), 'delta_label' => $this->trendLabel($currentPerformanceAverage - $previousPerformanceAverage), 'icon' => 'fa-chart-line', 'tone' => 'success'],
            ['label' => 'Attendance Rate', 'value' => number_format($currentAttendanceRate, 1) . '%', 'delta' => round($currentAttendanceRate - $previousAttendanceAverage, 1), 'delta_label' => $this->trendLabel($currentAttendanceRate - $previousAttendanceAverage), 'icon' => 'fa-calendar-check', 'tone' => 'info'],
            ['label' => 'Productivity Rate', 'value' => number_format($currentProductivityAverage, 1) . '%', 'delta' => round($currentProductivityAverage - $previousProductivityAverage, 1), 'delta_label' => $this->trendLabel($currentProductivityAverage - $previousProductivityAverage), 'icon' => 'fa-bolt', 'tone' => 'warning'],
            ['label' => 'Tasks Completed', 'value' => number_format($currentTasksCompleted), 'delta' => $currentTasksCompleted - $previousTasksCompleted, 'delta_label' => $this->trendLabel((float) ($currentTasksCompleted - $previousTasksCompleted)), 'icon' => 'fa-clipboard-check', 'tone' => 'success'],
            ['label' => 'Pending Tasks', 'value' => number_format($currentPendingTasks), 'delta' => $currentPendingTasks - $previousPendingTasks, 'delta_label' => $this->trendLabel((float) ($previousPendingTasks - $currentPendingTasks)), 'icon' => 'fa-tasks', 'tone' => 'danger'],
            ['label' => 'Employees on Leave', 'value' => number_format($currentLeaveCount), 'delta' => $currentLeaveCount - $previousLeaveCount, 'delta_label' => $this->trendLabel((float) ($previousLeaveCount - $currentLeaveCount)), 'icon' => 'fa-user-clock', 'tone' => 'secondary'],
        ];
    }

    private function uniqueOptions(array $items): array
    {
        $seen = [];
        $options = [];

        foreach ($items as $item) {
            $value = trim((string) ($item['value'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            if ($value === '' || $label === '' || isset($seen[$value])) {
                continue;
            }

            $seen[$value] = true;
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    private function dateRange(string $start, string $end): array
    {
        $dates = [];
        $cursor = new \DateTimeImmutable($start);
        $endDate = new \DateTimeImmutable($end);

        while ($cursor <= $endDate) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor = $cursor->modify('+1 day');
        }

        return $dates;
    }

    private function getWorkdays(string $start, string $end): array
    {
        return array_values(array_filter($this->dateRange($start, $end), static function (string $date): bool {
            return (int) date('N', strtotime($date)) <= 5;
        }));
    }

    private function normalizeDate(string $value, string $default): string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $default;
    }

    private function averageValue(array $values): float
    {
        $filtered = array_values(array_filter($values, static fn ($value): bool => is_numeric($value)));
        if ($filtered === []) {
            return 0.0;
        }

        return round(array_sum($filtered) / count($filtered), 1);
    }

    private function trendLabel(float $delta): string
    {
        if (abs($delta) < 0.1) {
            return 'Stable';
        }

        return $delta > 0 ? 'Improving' : 'Declining';
    }

    private function trendDirection(float $current, float $previous): string
    {
        if (abs($current - $previous) < 0.1) {
            return 'stable';
        }

        return $current > $previous ? 'up' : 'down';
    }

    private function completionRate(int $completed, int $assigned): float
    {
        if ($assigned <= 0) {
            return 0.0;
        }

        return round(($completed / $assigned) * 100, 1);
    }

    private function averageCompletionTime(float $workedHours, int $completedTasks): float
    {
        if ($completedTasks <= 0) {
            return 0.0;
        }

        return round($workedHours / $completedTasks, 2);
    }

    private function statusClass(string $category): string
    {
        return match ($category) {
            'Excellent' => 'status-excellent',
            'Good' => 'status-good',
            'Average' => 'status-average',
            default => 'status-risk',
        };
    }
}
