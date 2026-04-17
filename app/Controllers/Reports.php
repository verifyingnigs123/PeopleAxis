<?php

namespace App\Controllers;

class Reports extends BaseController
{
    /**
     * Generate attendance report (HR Admin only)
     */
    public function attendance()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            return view('reports/attendance');
        } catch (\Throwable $e) {
            log_message('error', 'Attendance method error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Unable to load attendance report.');
        }
    }

    /**
     * Generate attendance report (for direct URL access)
     * This method handles the route: /reports/generate/attendance
     */
    public function generateAttendance()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate attendance report data
            $reportData = $this->generateReportData('attendance');

            if (!$reportData) {
                log_message('error', 'Failed to generate attendance report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate attendance report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/attendance_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateAttendance method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate attendance report.');
        }
    }

    /**
     * Generate leave report (for direct URL access)
     * This method handles the route: /reports/generate/leave
     */
    public function generateLeave()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate leave report data
            $reportData = $this->generateReportData('leave');

            if (!$reportData) {
                log_message('error', 'Failed to generate leave report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate leave report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/leave_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateLeave method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate leave report.');
        }
    }

    /**
     * Generate salary report (for direct URL access)
     * This method handles the route: /reports/generate/salary
     */
    public function generateSalary()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate salary report data
            $reportData = $this->generateReportData('salary');

            if (!$reportData) {
                log_message('error', 'Failed to generate salary report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate salary report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/salary_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateSalary method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate salary report.');
        }
    }

    /**
     * Generate department report (for direct URL access)
     * This method handles the route: /reports/generate/department
     */
    public function generateDepartment()
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Check if user is HR Admin or Super Admin
            $roleName = session()->get('role_name');
            $role = session()->get('role');
            
            if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
                return redirect()->to('/dashboard')->with('error', 'Access denied. HR Admin only.');
            }

            // Generate department report data
            $reportData = $this->generateReportData('department');

            if (!$reportData) {
                log_message('error', 'Failed to generate department report data');
                return redirect()->to('/reports')->with('error', 'Unable to generate department report.');
            }

            // Prepare data for view
            $data = [
                'reportData' => $reportData
            ];

            return view('reports/department_display', $data);
            
        } catch (\Throwable $e) {
            log_message('error', 'GenerateDepartment method error: ' . $e->getMessage());
            return redirect()->to('/reports')->with('error', 'Unable to generate department report.');
        }
    }

    /**
     * Display available reports
     */
    public function index()
    {
        // Check if user is Super Admin or HR Admin
        $roleName = session()->get('role_name');
        $role = session()->get('role');
        
        if (!($role === 'admin' || $roleName === 'Super Admin' || in_array($roleName, ['HR Admin', 'hr']) || in_array($role, ['hr', 'hr_admin']))) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $reports = [
            [
                'id' => 'employee-summary',
                'title' => 'Employee Summary Report',
                'description' => 'Overview of all employees in the system',
                'icon' => 'fa-users'
            ],
            [
                'id' => 'attendance-report',
                'title' => 'Attendance Report',
                'description' => 'Monthly and yearly attendance statistics',
                'icon' => 'fa-calendar-check'
            ],
            [
                'id' => 'leave-report',
                'title' => 'Leave Report',
                'description' => 'Leave requests and approvals summary',
                'icon' => 'fa-calendar-times'
            ],
            [
                'id' => 'salary-report',
                'title' => 'Salary Report',
                'description' => 'Employee salary and payroll information',
                'icon' => 'fa-dollar-sign'
            ],
            [
                'id' => 'user-activity',
                'title' => 'User Activity Report',
                'description' => 'System user activities and access logs',
                'icon' => 'fa-history'
            ],
            [
                'id' => 'department-report',
                'title' => 'Department Report',
                'description' => 'Department-wise employee distribution',
                'icon' => 'fa-sitemap'
            ]
        ];

        $data['reports'] = $reports;
        return view('reports/index', $data);
    }

    /**
     * Manager-only team performance dashboard.
     */
    public function team()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (! $this->isManagerUser()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Manager only.');
        }

        $selectedMonth = (string) $this->request->getGet('month');
        if (! preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = date('Y-m');
        }

        $selectedDate = (string) $this->request->getGet('date');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = date('Y-m-d');
        }

        $dailySortBy = strtolower((string) $this->request->getGet('daily_sort_by'));
        if (! in_array($dailySortBy, ['employee_name', 'date'], true)) {
            $dailySortBy = 'date';
        }

        $dailySortDir = strtolower((string) $this->request->getGet('daily_sort_dir'));
        if (! in_array($dailySortDir, ['asc', 'desc'], true)) {
            $dailySortDir = 'desc';
        }

        $periodStart = $selectedMonth . '-01';
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        try {
            $teamContext = $this->getManagedTeamContext();
            $data = [
                'selectedMonth'      => $selectedMonth,
                'selectedDate'       => $selectedDate,
                'dailySortBy'        => $dailySortBy,
                'dailySortDir'       => $dailySortDir,
                'periodLabel'        => date('F Y', strtotime($periodStart)),
                'managedDepartments' => $teamContext['departments'],
                'summary'            => [
                    'team_members'    => count($teamContext['teamMembers']),
                    'average_score'   => 0,
                    'pending_leaves'  => 0,
                    'at_risk_members' => 0,
                ],
                'departmentBreakdown' => [],
                'performanceRows'   => [],
                'dailyAttendanceRows' => [],
                'monthlyAttendanceRows' => [],
            ];

            if ($teamContext['employeeIds'] === []) {
                return view('reports/team', $data);
            }

            $db = \Config\Database::connect();

            $attendanceRows = $db->table('attendance_logs')
                ->select('employee_id, MAX(date) AS last_attendance_date, SUM(CASE WHEN LOWER(status) = "present" THEN 1 ELSE 0 END) AS present_days, SUM(CASE WHEN LOWER(status) IN ("late", "half-day", "half day") THEN 1 ELSE 0 END) AS late_days, SUM(CASE WHEN LOWER(status) = "absent" THEN 1 ELSE 0 END) AS absent_days', false)
                ->whereIn('employee_id', $teamContext['employeeIds'])
                ->where('date >=', $periodStart)
                ->where('date <=', $periodEnd)
                ->groupBy('employee_id')
                ->get()
                ->getResultArray();

            $leaveRows = $db->table('leave_requests')
                ->select('employee_id, COUNT(*) AS leave_requests, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) AS pending_leave_requests, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) AS approved_leave_requests', false)
                ->whereIn('employee_id', $teamContext['employeeIds'])
                ->groupStart()
                    ->where('start_date <=', $periodEnd)
                    ->where('end_date >=', $periodStart)
                ->groupEnd()
                ->groupBy('employee_id')
                ->get()
                ->getResultArray();

            $attendanceMap = [];
            foreach ($attendanceRows as $row) {
                $attendanceMap[(int) ($row['employee_id'] ?? 0)] = $row;
            }

            $leaveMap = [];
            foreach ($leaveRows as $row) {
                $leaveMap[(int) ($row['employee_id'] ?? 0)] = $row;
            }

            foreach ($teamContext['departments'] as $department) {
                $departmentId = (int) ($department['id'] ?? 0);
                $data['departmentBreakdown'][$departmentId] = [
                    'name'           => $department['name'] ?? 'Unassigned',
                    'members'        => 0,
                    'total_score'    => 0,
                    'pending_leaves' => 0,
                ];
            }

            $totalScore = 0;
            $scoredMembers = 0;

            foreach ($teamContext['teamMembers'] as $member) {
                $employeeId = (int) ($member['id'] ?? 0);
                $departmentId = (int) ($member['department_id'] ?? 0);
                $attendance = $attendanceMap[$employeeId] ?? [];
                $leave = $leaveMap[$employeeId] ?? [];

                $presentDays = (int) ($attendance['present_days'] ?? 0);
                $lateDays = (int) ($attendance['late_days'] ?? 0);
                $absentDays = (int) ($attendance['absent_days'] ?? 0);
                $leaveRequests = (int) ($leave['leave_requests'] ?? 0);
                $pendingLeaveRequests = (int) ($leave['pending_leave_requests'] ?? 0);
                $trackedDays = $presentDays + $lateDays + $absentDays;
                $score = $trackedDays > 0
                    ? (int) round((($presentDays + ($lateDays * 0.6)) / $trackedDays) * 100)
                    : 0;

                if ($score >= 90) {
                    $performanceLabel = 'Strong';
                } elseif ($score >= 75) {
                    $performanceLabel = 'Stable';
                } elseif ($score >= 60) {
                    $performanceLabel = 'Watch';
                } else {
                    $performanceLabel = 'At Risk';
                }

                $isAtRisk = $score < 60 || $absentDays >= 3 || $pendingLeaveRequests > 1;

                if (! isset($data['departmentBreakdown'][$departmentId])) {
                    $data['departmentBreakdown'][$departmentId] = [
                        'name'           => $member['department_name'] ?? 'Unassigned',
                        'members'        => 0,
                        'total_score'    => 0,
                        'pending_leaves' => 0,
                    ];
                }

                $data['departmentBreakdown'][$departmentId]['members']++;
                $data['departmentBreakdown'][$departmentId]['total_score'] += $score;
                $data['departmentBreakdown'][$departmentId]['pending_leaves'] += $pendingLeaveRequests;

                $data['summary']['pending_leaves'] += $pendingLeaveRequests;
                if ($isAtRisk) {
                    $data['summary']['at_risk_members']++;
                }

                $totalScore += $score;
                $scoredMembers++;

                $data['performanceRows'][] = [
                    'employee_name'          => trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')),
                    'employee_code'          => $member['employee_id'] ?? 'N/A',
                    'department_name'        => $member['department_name'] ?? 'Unassigned',
                    'present_days'           => $presentDays,
                    'late_days'              => $lateDays,
                    'absent_days'            => $absentDays,
                    'leave_requests'         => $leaveRequests,
                    'pending_leave_requests' => $pendingLeaveRequests,
                    'score'                  => $score,
                    'performance_label'      => $performanceLabel,
                    'last_attendance_date'   => $attendance['last_attendance_date'] ?? null,
                ];
            }

            if ($scoredMembers > 0) {
                $data['summary']['average_score'] = (int) round($totalScore / $scoredMembers);
            }

            foreach ($data['departmentBreakdown'] as $departmentId => $department) {
                $members = (int) ($department['members'] ?? 0);
                $data['departmentBreakdown'][$departmentId]['average_score'] = $members > 0
                    ? (int) round(((int) $department['total_score']) / $members)
                    : 0;
            }

            $data['departmentBreakdown'] = array_values($data['departmentBreakdown']);

            usort($data['performanceRows'], static function (array $left, array $right): int {
                return $right['score'] <=> $left['score'];
            });

            if ($teamContext['employeeIds'] !== []) {
                $dailyRows = $db->table('employees')
                    ->select("employees.id AS employee_pk_id, employees.employee_id AS employee_code, employees.first_name, employees.last_name, departments.name AS department_name, MIN(attendance_logs.time_in) AS time_in, MAX(attendance_logs.time_out) AS time_out, GROUP_CONCAT(DISTINCT LOWER(attendance_logs.status)) AS statuses", false)
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->join('attendance_logs', "attendance_logs.employee_id = employees.id AND attendance_logs.date = " . $db->escape($selectedDate), 'left')
                    ->whereIn('employees.id', $teamContext['employeeIds'])
                    ->where('employees.account_status', 'approved')
                    ->groupStart()
                        ->where('employees.status', 'active')
                        ->orWhere('employees.status IS NULL', null, false)
                        ->orWhere('employees.status', '')
                    ->groupEnd()
                    ->groupBy('employees.id')
                    ->groupBy('employees.employee_id')
                    ->groupBy('employees.first_name')
                    ->groupBy('employees.last_name')
                    ->groupBy('departments.name')
                    ->get()
                    ->getResultArray();

                $employeeIds = array_map('intval', array_column($dailyRows, 'employee_pk_id'));
                $onLeaveRows = [];
                if ($employeeIds !== []) {
                    $onLeaveRows = $db->table('leave_requests')
                        ->select('employee_id')
                        ->distinct()
                        ->whereIn('employee_id', $employeeIds)
                        ->where('status', 'approved')
                        ->where('early_returned_at', null)
                        ->where('start_date <=', $selectedDate)
                        ->where('end_date >=', $selectedDate)
                        ->get()
                        ->getResultArray();
                }

                $onLeaveLookup = [];
                foreach ($onLeaveRows as $row) {
                    $employeeId = (int) ($row['employee_id'] ?? 0);
                    if ($employeeId > 0) {
                        $onLeaveLookup[$employeeId] = true;
                    }
                }

                $normalizedDailyRows = [];
                foreach ($dailyRows as $row) {
                    $employeePkId = (int) ($row['employee_pk_id'] ?? 0);
                    $statuses = array_filter(array_map('trim', explode(',', (string) ($row['statuses'] ?? ''))));
                    $statusLookup = array_fill_keys($statuses, true);
                    $isOnLeave = isset($onLeaveLookup[$employeePkId]);

                    if ($isOnLeave) {
                        $normalizedStatus = 'Leave';
                    } elseif (isset($statusLookup['absent']) || $statuses === []) {
                        $normalizedStatus = 'Absent';
                    } elseif (isset($statusLookup['late']) || isset($statusLookup['half-day']) || isset($statusLookup['half day'])) {
                        $normalizedStatus = 'Late';
                    } else {
                        $normalizedStatus = 'Present';
                    }

                    $normalizedDailyRows[] = [
                        'employee_name' => trim(((string) ($row['first_name'] ?? '')) . ' ' . ((string) ($row['last_name'] ?? ''))),
                        'department_name' => (string) ($row['department_name'] ?? 'Unassigned'),
                        'date' => $selectedDate,
                        'time_in' => (string) ($row['time_in'] ?? ''),
                        'time_out' => (string) ($row['time_out'] ?? ''),
                        'status' => $normalizedStatus,
                    ];
                }

                usort($normalizedDailyRows, static function (array $a, array $b) use ($dailySortBy, $dailySortDir): int {
                    if ($dailySortBy === 'employee_name') {
                        $compare = strcmp(strtolower((string) $a['employee_name']), strtolower((string) $b['employee_name']));
                        if ($compare === 0) {
                            $compare = strcmp(strtolower((string) $a['department_name']), strtolower((string) $b['department_name']));
                        }
                    } else {
                        $compare = strcmp((string) $a['date'], (string) $b['date']);
                        if ($compare === 0) {
                            $compare = strcmp(strtolower((string) $a['employee_name']), strtolower((string) $b['employee_name']));
                        }
                    }

                    return $dailySortDir === 'asc' ? $compare : -$compare;
                });

                $data['dailyAttendanceRows'] = $normalizedDailyRows;
            }

            $monthlyRows = $db->table('attendance_logs')
                ->select("attendance_logs.date, attendance_logs.time_in, attendance_logs.time_out, employees.employee_id AS employee_code, CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name", false)
                ->join('employees', 'employees.id = attendance_logs.employee_id', 'inner')
                ->whereIn('attendance_logs.employee_id', $teamContext['employeeIds'])
                ->where('attendance_logs.date >=', $periodStart)
                ->where('attendance_logs.date <=', $periodEnd)
                ->orderBy('attendance_logs.date', 'DESC')
                ->orderBy('employees.first_name', 'ASC')
                ->orderBy('employees.last_name', 'ASC')
                ->orderBy('attendance_logs.time_in', 'DESC')
                ->get()
                ->getResultArray();

            $normalizedMonthlyRows = [];
            foreach ($monthlyRows as $row) {
                $attendanceDate = (string) ($row['date'] ?? '');
                $timeIn = (string) ($row['time_in'] ?? '');
                $timeOut = (string) ($row['time_out'] ?? '');

                $inDate = $timeIn !== '' ? $attendanceDate : '';
                $outDate = '';

                if ($timeOut !== '') {
                    $outDate = $attendanceDate;

                    if ($timeIn !== '') {
                        $inTs = strtotime($attendanceDate . ' ' . $timeIn);
                        $outTs = strtotime($attendanceDate . ' ' . $timeOut);

                        if ($inTs !== false && $outTs !== false && $outTs < $inTs) {
                            $outDate = date('Y-m-d', strtotime($attendanceDate . ' +1 day'));
                        }
                    }
                }

                $normalizedMonthlyRows[] = [
                    'employee_id' => (string) ($row['employee_code'] ?? 'N/A'),
                    'employee_name' => trim((string) ($row['employee_name'] ?? '')),
                    'date' => $attendanceDate,
                    'in_date' => $inDate,
                    'in_time' => $timeIn,
                    'out_date' => $outDate,
                    'out_time' => $timeOut,
                ];
            }

            $data['monthlyAttendanceRows'] = $normalizedMonthlyRows;

            return view('reports/team', $data);
        } catch (\Exception $e) {
            log_message('error', 'Manager performance dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load team performance.');
        }
    }

    /**
     * Generate specific report (for AJAX/API calls)
     */
    public function generate($reportType = null)
    {
        try {
            // Enable error reporting in development
            if (ENVIRONMENT === 'development') {
                ini_set('display_errors', 1);
                error_reporting(E_ALL);
            }
            
            // Get report type from URL parameter first, then from POST data
            if (!$reportType) {
                $reportType = $this->request->getPost('report_type');
            }
            
            $format = $this->request->getPost('format') ?? 'json'; // json, pdf, csv, excel

            if (!$reportType) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Report type is required'
                ]);
            }

            // Check if user is authenticated (allow both admin and hr_admin for attendance reports)
            $userRole = session()->get('role');
            $roleName = session()->get('role_name');
            
            // For attendance reports, allow HR Admin access as well
            $isAuthorized = ($userRole === 'admin' || 
                           $roleName === 'Super Admin' || 
                           in_array($roleName, ['HR Admin', 'hr']) || 
                           in_array($userRole, ['hr', 'hr_admin']));
            
            if (!$isAuthorized) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Access denied. Admin or HR Admin access required.'
                ]);
            }

            // Generate report based on type
            $reportData = $this->generateReportData($reportType);

            if (!$reportData) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Report type not found'
                ]);
            }

            // Ensure data is not null
            if (empty($reportData)) {
                $reportData = ['data' => []];
            }

            // Check if this is an AJAX request or direct URL access
            $isAjax = $this->request->isAJAX() || $this->request->getPost('format') !== null;
            
            if ($isAjax) {
                // Return JSON for AJAX calls
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Report generated successfully',
                    'data' => $reportData ?? []
                ]);
            } else {
                // Return view for direct URL access
                if ($reportType === 'attendance' || $reportType === 'attendance-report') {
                    $data['reportData'] = $reportData;
                    return view('reports/attendance_display', $data);
                } else {
                    // For other report types, return a generic view or redirect
                    return redirect()->to('/reports')->with('error', 'Report type not supported for direct viewing');
                }
            }
            
        } catch (\Throwable $e) {
            // Log the error
            log_message('error', 'Exception in generate method: ' . $e->getMessage());
            
            // Return standardized error response
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Internal server error occurred',
                'data' => [],
                'error' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Generate Employee Report (for direct URL access)
     */
    public function generateEmployee()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        // Generate employee report data
        $reportData = $this->generateReportData('employee');

        if (!$reportData) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Employee report not found");
        }

        // Prepare data for view
        $data = [
            'title' => $reportData['title'],
            'generated_at' => $reportData['generated_at'],
            'employees' => $reportData['employees'],
            'total_employees' => $reportData['total_employees'],
            'by_department' => $reportData['by_department']
        ];

        return view('superadmin/reports/employee_report', $data);
    }

    /**
     * View a specific report
     */
    public function view($reportId)
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        $reportData = $this->generateReportData($reportId);

        if (!$reportData) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Report not found");
        }

        $data['reportId'] = $reportId;
        $data['reportData'] = $reportData;

        return view('reports/view', $data);
    }

    /**
     * Generate report data
     */
    private function generateReportData($reportType)
    {
        try {
            $db = \Config\Database::connect();
            
            // Check database connection
            if (!$db) {
                log_message('error', 'Database connection failed');
                return null;
            }
            
            switch ($reportType) {
                case 'employee':
                case 'employee-summary':
                    // Get actual employee data
                    $employees = $db->table('employees')
                        ->select('employees.*, departments.name as department_name, users.email as user_email')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->join('users', 'users.id = employees.user_id', 'left')
                        ->where('employees.account_status', 'approved')
                        ->orderBy('employees.first_name', 'ASC')
                        ->get()
                        ->getResultArray();
                    
                    // Get department statistics
                    $departmentStats = $db->table('employees')
                        ->select('departments.name, COUNT(*) as count')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->where('employees.account_status', 'approved')
                        ->groupBy('departments.id', 'departments.name')
                        ->orderBy('count', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Employee Summary Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'total_employees' => count($employees),
                        'by_department' => $departmentStats,
                        'employees' => $employees ?? []
                    ];
                    
                case 'attendance':
                case 'attendance-report':
                    $currentMonth = date('Y-m');
                    
                    // Enhanced query with better error handling
                    $attendanceData = $db->table('attendance_logs')
                        ->select('attendance_logs.*, employees.first_name, employees.last_name, employees.employee_id as emp_code, departments.name as department_name')
                        ->join('employees', 'employees.id = attendance_logs.employee_id', 'inner')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->where('attendance_logs.date >=', $currentMonth . '-01')
                        ->where('attendance_logs.date <=', $currentMonth . '-31')
                        ->orderBy('attendance_logs.date', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Attendance Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'period' => $currentMonth,
                        'data' => $attendanceData ?? []
                    ];
                
                case 'leave':
                case 'leave-report':
                    $leaveData = $db->table('leave_requests')
                        ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_id as emp_code, departments.name as department_name')
                        ->join('employees', 'employees.id = leave_requests.employee_id', 'inner')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->orderBy('leave_requests.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Leave Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'period' => date('Y'),
                        'data' => $leaveData ?? []
                    ];
                    
                case 'salary':
                case 'salary-report':
                    $salaryData = $db->table('employees')
                        ->select('employees.*, departments.name as department_name, users.email as user_email')
                        ->join('departments', 'departments.id = employees.department_id', 'left')
                        ->join('users', 'users.id = employees.user_id', 'left')
                        ->where('employees.account_status', 'approved')
                        ->where('employees.rate IS NOT NULL')
                        ->where('employees.rate >', 0)
                        ->orderBy('employees.rate', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Salary Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'period' => date('Y-m'),
                        'data' => $salaryData ?? []
                    ];
                    
                case 'department':
                case 'department-report':
                    $departmentData = $db->table('departments')
                        ->select('MIN(departments.id) as id, departments.name, departments.description, departments.is_active, COUNT(CASE WHEN employees.account_status = "approved" THEN employees.id END) as employee_count')
                        ->join('employees', 'employees.department_id = departments.id', 'left')
                        ->groupBy(['departments.name', 'departments.description', 'departments.is_active'])
                        ->orderBy('employee_count', 'DESC')
                        ->get()
                        ->getResultArray();
                    
                    return [
                        'title' => 'Department Report',
                        'generated_at' => date('Y-m-d H:i:s'),
                        'data' => $departmentData ?? []
                    ];
                    
                default:
                    log_message('error', 'Unknown report type: ' . $reportType);
                    return null;
                    
            }
            
        } catch (\Throwable $e) {
            log_message('error', 'Exception in generateReportData: ' . $e->getMessage());
            return null;
        }
    }
}
