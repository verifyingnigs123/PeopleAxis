<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->employeeModel = new EmployeeModel();
    }

    /**
     * Display current user's attendance
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if ($this->isManagerUser()) {
            return redirect()->to('/attendance/team');
        }

        $selectedMonth = trim((string) $this->request->getGet('month'));
        if (! preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = date('Y-m');
        }

        try {
            $employee = $this->getCurrentEmployeeRecord();
            $periodStart = $selectedMonth . '-01';
            $periodEnd = date('Y-m-t', strtotime($periodStart));
            $stats = [
                'total_days'      => 0,
                'present_days'    => 0,
                'late_days'       => 0,
                'absent_days'     => 0,
                'worked_hours'    => 0,
                'attendance_rate' => 0,
            ];
            $records = [];
            $latestRecord = null;

            if ($employee) {
                $employeeId = (int) $employee->id;
                $db = \Config\Database::connect();

                $records = $this->attendanceModel
                    ->where('employee_id', $employeeId)
                    ->where('date >=', $periodStart)
                    ->where('date <=', $periodEnd)
                    ->orderBy('date', 'DESC')
                    ->orderBy('time_in', 'DESC')
                    ->paginate(20);

                $monthlyRecords = $db->table('attendance_logs')
                    ->select('date, time_in, time_out, status')
                    ->where('employee_id', $employeeId)
                    ->where('date >=', $periodStart)
                    ->where('date <=', $periodEnd)
                    ->get()
                    ->getResult();

                $stats['total_days'] = count($monthlyRecords);

                foreach ($monthlyRecords as $record) {
                    $status = strtolower((string) ($record->status ?? ''));

                    if ($status === 'absent') {
                        $stats['absent_days']++;
                    } elseif (in_array($status, ['late', 'half-day', 'half day'], true)) {
                        $stats['late_days']++;
                    } else {
                        $stats['present_days']++;
                    }

                    if (! empty($record->time_in) && ! empty($record->time_out)) {
                        $workedHours = max((strtotime((string) $record->time_out) - strtotime((string) $record->time_in)) / 3600, 0);
                        $stats['worked_hours'] += $workedHours;
                    }
                }

                if ($stats['total_days'] > 0) {
                    $stats['attendance_rate'] = (int) round((($stats['present_days'] + $stats['late_days']) / $stats['total_days']) * 100);
                }

                $stats['worked_hours'] = round($stats['worked_hours'], 1);

                $latestRecord = $db->table('attendance_logs')
                    ->select('date, time_in, time_out, status')
                    ->where('employee_id', $employeeId)
                    ->orderBy('date', 'DESC')
                    ->orderBy('time_in', 'DESC')
                    ->get(1)
                    ->getRow();
            } else {
                // No linked employee profile yet; keep the page visible with empty data.
            }

            $data = [
                'employee'      => $employee,
                'selectedMonth' => $selectedMonth,
                'monthLabel'    => date('F Y', strtotime($periodStart)),
                'stats'         => $stats,
                'latestRecord'  => $latestRecord,
                'records'       => $records,
                'pager'         => $this->attendanceModel->pager,
            ];

            return view('attendance/view', $data);
        } catch (\Exception $e) {
            log_message('error', 'Attendance view error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load attendance records');
        }
    }

    /**
     * Display attendance logs (Super Admin only)
     */
    public function logs()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        try {
            $attendance = $this->attendanceModel
                ->select("attendance.*, CONCAT(employees.first_name, ' ', employees.last_name) as name, employees.employee_id")
                ->join('employees', 'employees.id = attendance.employee_id', 'left')
                ->orderBy('attendance.date', 'DESC')
                ->orderBy('attendance.time_in', 'DESC')
                ->paginate(50);

            $data['attendance'] = $attendance;
            $data['pager'] = $this->attendanceModel->pager;

            return view('attendance/logs', $data);
        } catch (\Exception $e) {
            log_message('error', 'Attendance logs error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load attendance logs');
        }
    }

    /**
     * Display team attendance (Manager only)
     */
    public function team()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Check if user is Manager
        if (! $this->isManagerUser()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Manager only.');
        }

        $selectedDate = (string) $this->request->getGet('date');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = date('Y-m-d');
        }

        try {
            $teamContext = $this->getManagedTeamContext();
            $db = \Config\Database::connect();

            $data = [
                'selectedDate'      => $selectedDate,
                'managedDepartments' => $teamContext['departments'],
                'teamCount'         => count($teamContext['teamMembers']),
                'attendanceSummary' => [
                    'present'  => 0,
                    'late'     => 0,
                    'absent'   => 0,
                    'on_leave' => 0,
                ],
                'departmentSummary' => [],
                'missingMembers'    => [],
                'attendanceRecords' => [],
                'pager'             => null,
            ];

            if ($teamContext['employeeIds'] === []) {
                return view('attendance/team', $data);
            }

            $rawAttendance = $db->table('attendance_logs')
                ->select('employee_id, status')
                ->whereIn('employee_id', $teamContext['employeeIds'])
                ->where('date', $selectedDate)
                ->get()
                ->getResultArray();

            $attendanceByEmployee = [];
            foreach ($rawAttendance as $row) {
                $attendanceByEmployee[(int) ($row['employee_id'] ?? 0)] = strtolower((string) ($row['status'] ?? ''));
            }

            $leaveRows = $db->table('leave_requests')
                ->select('employee_id')
                ->distinct()
                ->whereIn('employee_id', $teamContext['employeeIds'])
                ->whereIn('status', ['approved', 'manager_approved'])
                ->where('start_date <=', $selectedDate)
                ->where('end_date >=', $selectedDate)
                ->get()
                ->getResultArray();

            $onLeaveIds = array_map('intval', array_column($leaveRows, 'employee_id'));
            $onLeaveLookup = array_fill_keys($onLeaveIds, true);
            $data['attendanceSummary']['on_leave'] = count($onLeaveLookup);

            foreach ($teamContext['departments'] as $department) {
                $departmentId = (int) ($department['id'] ?? 0);
                $data['departmentSummary'][$departmentId] = [
                    'name'     => $department['name'] ?? 'Unassigned',
                    'members'  => 0,
                    'recorded' => 0,
                    'missing'  => 0,
                    'on_leave' => 0,
                ];
            }

            foreach ($teamContext['teamMembers'] as $member) {
                $employeeId = (int) ($member['id'] ?? 0);
                $departmentId = (int) ($member['department_id'] ?? 0);
                $departmentName = $member['department_name'] ?? 'Unassigned';
                $status = $attendanceByEmployee[$employeeId] ?? null;

                if (! isset($data['departmentSummary'][$departmentId])) {
                    $data['departmentSummary'][$departmentId] = [
                        'name'     => $departmentName,
                        'members'  => 0,
                        'recorded' => 0,
                        'missing'  => 0,
                        'on_leave' => 0,
                    ];
                }

                $data['departmentSummary'][$departmentId]['members']++;

                if ($status !== null) {
                    $data['departmentSummary'][$departmentId]['recorded']++;
                }

                if (isset($onLeaveLookup[$employeeId])) {
                    $data['departmentSummary'][$departmentId]['on_leave']++;
                }

                if ($status === null && ! isset($onLeaveLookup[$employeeId])) {
                    $data['attendanceSummary']['absent']++;
                    $data['departmentSummary'][$departmentId]['missing']++;
                    $data['missingMembers'][] = [
                        'name'       => trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')),
                        'department' => $departmentName,
                    ];
                    continue;
                }

                if ($status === 'absent') {
                    $data['attendanceSummary']['absent']++;
                } elseif (in_array($status, ['late', 'half-day', 'half day'], true)) {
                    $data['attendanceSummary']['late']++;
                } elseif ($status !== null) {
                    $data['attendanceSummary']['present']++;
                }
            }

            $data['departmentSummary'] = array_values($data['departmentSummary']);

            $data['attendanceRecords'] = $this->attendanceModel
                ->select("attendance_logs.*, CONCAT(employees.first_name, ' ', employees.last_name) as employee_name, employees.employee_id as staff_code, departments.name as department_name")
                ->join('employees', 'employees.id = attendance_logs.employee_id', 'left')
                ->join('departments', 'departments.id = employees.department_id', 'left')
                ->whereIn('attendance_logs.employee_id', $teamContext['employeeIds'])
                ->where('attendance_logs.date', $selectedDate)
                ->orderBy('attendance_logs.time_in', 'DESC')
                ->orderBy('attendance_logs.created_at', 'DESC')
                ->paginate(25);
            $data['pager'] = $this->attendanceModel->pager;

            return view('attendance/team', $data);
        } catch (\Exception $e) {
            log_message('error', 'Team attendance error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load team attendance');
        }
    }
}
