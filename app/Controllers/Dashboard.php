<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use Config\Database;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $employeeModel = new EmployeeModel();
        $attendanceModel = new AttendanceModel();
        $leaveModel = new LeaveModel();

        $session = session();
        $role = $session->get('role_name') ?? 'Employee';

        $data = ['user' => $session->get()];

        switch ($role) {
            case 'Super Admin':
                $data['totalUsers'] = $userModel->countAllResults();
                $data['totalEmployees'] = $employeeModel->countAllResults();
                $data['auditCount'] = model('App\\Models\\AuditModel')->countAllResults();
                $data['attendanceSummary'] = $attendanceModel->getSummary();
                break;

            case 'HR Admin':
                $data['totalEmployees'] = $employeeModel->countAllResults();
                $data['pendingLeaves'] = $leaveModel->where('status', 'pending')->countAllResults();
                $data['attendanceSummary'] = $attendanceModel->getSummary();
                $statusCounts = [
                    'pending'  => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ];

                $statusRows = Database::connect()
                    ->table('employees')
                    ->select('LOWER(account_status) as account_status, COUNT(*) as total', false)
                    ->whereIn('account_status', ['pending', 'approved', 'rejected'])
                    ->groupBy('account_status')
                    ->get()
                    ->getResultArray();

                foreach ($statusRows as $row) {
                    $status = strtolower((string) ($row['account_status'] ?? ''));
                    if (array_key_exists($status, $statusCounts)) {
                        $statusCounts[$status] = (int) ($row['total'] ?? 0);
                    }
                }

                $data['employeeAccountStatusCounts'] = $statusCounts;
                break;

            case 'Manager':
                try {
                    $teamContext = $this->getManagedTeamContext();
                    $selectedDailyAttendanceDate = (string) $this->request->getGet('team_attendance_date');
                    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDailyAttendanceDate)) {
                        $selectedDailyAttendanceDate = date('Y-m-d');
                    }

                    $sortBy = strtolower((string) $this->request->getGet('team_attendance_sort_by'));
                    if (! in_array($sortBy, ['employee_name', 'date'], true)) {
                        $sortBy = 'date';
                    }

                    $sortDir = strtolower((string) $this->request->getGet('team_attendance_sort_dir'));
                    if (! in_array($sortDir, ['asc', 'desc'], true)) {
                        $sortDir = 'desc';
                    }

                    $data['managedDepartments'] = $teamContext['departments'];
                    $data['managedDepartmentCount'] = count($teamContext['departments']);
                    $data['teamCount'] = count($teamContext['teamMembers']);
                    $data['pendingTeamLeaves'] = 0;
                    $data['teamAttendance'] = [
                        'present' => 0,
                        'late'    => 0,
                        'absent'  => 0,
                        'leave'   => 0,
                    ];
                    $data['teamDailyAttendanceDate'] = $selectedDailyAttendanceDate;
                    $data['teamDailyAttendanceSortBy'] = $sortBy;
                    $data['teamDailyAttendanceSortDir'] = $sortDir;
                    $data['teamDailyAttendanceRecords'] = [];

                    if ($teamContext['employeeIds'] !== []) {
                        $db = \Config\Database::connect();
                        $today = date('Y-m-d');

                        $todayAttendance = $db->table('attendance_logs')
                            ->select('employee_id, status')
                            ->whereIn('employee_id', $teamContext['employeeIds'])
                            ->where('date', $today)
                            ->get()
                            ->getResultArray();

                        $recordedEmployees = [];
                        $presentCount = 0;
                        $lateCount = 0;
                        $explicitAbsentCount = 0;

                        foreach ($todayAttendance as $row) {
                            $employeeId = (int) ($row['employee_id'] ?? 0);
                            $status = strtolower((string) ($row['status'] ?? ''));

                            $recordedEmployees[$employeeId] = true;

                            if (in_array($status, ['late', 'half-day', 'half day'], true)) {
                                $lateCount++;
                            } elseif ($status === 'absent') {
                                $explicitAbsentCount++;
                            } else {
                                $presentCount++;
                            }
                        }

                        $leaveCount = (int) ($db->table('leave_requests')
                            ->select('COUNT(DISTINCT employee_id) AS total', false)
                            ->whereIn('employee_id', $teamContext['employeeIds'])
                            ->where('status', 'approved')
                            ->where('early_returned_at', null)
                            ->where('start_date <=', $today)
                            ->where('end_date >=', $today)
                            ->get()
                            ->getRow('total') ?? 0);

                        $missingCount = max($data['teamCount'] - count($recordedEmployees) - $leaveCount, 0);

                        $data['teamAttendance'] = [
                            'present' => $presentCount,
                            'late'    => $lateCount,
                            'absent'  => $explicitAbsentCount + $missingCount,
                            'leave'   => $leaveCount,
                        ];

                        $data['pendingTeamLeaves'] = $db->table('leave_requests')
                            ->whereIn('employee_id', $teamContext['employeeIds'])
                            ->where('status', 'pending')
                            ->countAllResults();
                    }

                    if ($teamContext['employeeIds'] !== []) {
                        $db = \Config\Database::connect();

                        $dailyRows = $db->table('employees')
                            ->select("employees.id AS employee_pk_id, employees.employee_id AS employee_code, employees.first_name, employees.last_name, departments.name AS department_name, MIN(attendance_logs.time_in) AS time_in, MAX(attendance_logs.time_out) AS time_out, GROUP_CONCAT(DISTINCT LOWER(attendance_logs.status)) AS statuses", false)
                            ->join('departments', 'departments.id = employees.department_id', 'left')
                            ->join('attendance_logs', "attendance_logs.employee_id = employees.id AND attendance_logs.date = " . $db->escape($selectedDailyAttendanceDate), 'left')
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

                        $onLeaveRows = $db->table('leave_requests')
                            ->select('employee_id')
                            ->distinct()
                            ->whereIn('employee_id', array_map('intval', array_column($dailyRows, 'employee_pk_id')))
                            ->where('status', 'approved')
                            ->where('early_returned_at', null)
                            ->where('start_date <=', $selectedDailyAttendanceDate)
                            ->where('end_date >=', $selectedDailyAttendanceDate)
                            ->get()
                            ->getResultArray();

                        $onLeaveLookup = [];
                        foreach ($onLeaveRows as $row) {
                            $employeeId = (int) ($row['employee_id'] ?? 0);
                            if ($employeeId > 0) {
                                $onLeaveLookup[$employeeId] = true;
                            }
                        }

                        $normalizedRows = [];
                        foreach ($dailyRows as $row) {
                            $employeePkId = (int) ($row['employee_pk_id'] ?? 0);
                            $statuses = array_filter(array_map('trim', explode(',', (string) ($row['statuses'] ?? ''))));
                            $statusLookup = array_fill_keys($statuses, true);
                            $isOnLeave = isset($onLeaveLookup[$employeePkId]);

                            if ($isOnLeave) {
                                $normalizedStatus = 'Leave';
                            } elseif (isset($statusLookup['absent'])) {
                                $normalizedStatus = 'Absent';
                            } elseif (isset($statusLookup['late']) || isset($statusLookup['half-day']) || isset($statusLookup['half day'])) {
                                $normalizedStatus = 'Late';
                            } elseif ($statuses !== []) {
                                $normalizedStatus = 'Present';
                            } else {
                                $normalizedStatus = 'Absent';
                            }

                            $timeIn = $row['time_in'] ?? null;
                            $timeOut = $row['time_out'] ?? null;
                            $hasAnyLog = ! empty($timeIn) || ! empty($timeOut);

                            $normalizedRows[] = [
                                'employee_name'   => trim(((string) ($row['first_name'] ?? '')) . ' ' . ((string) ($row['last_name'] ?? ''))),
                                'department_name' => (string) ($row['department_name'] ?? 'Unassigned'),
                                'date'            => $selectedDailyAttendanceDate,
                                'time_in'         => $timeIn,
                                'time_out'        => $timeOut,
                                'status'          => $normalizedStatus,
                                'has_log'         => $hasAnyLog,
                                'employee_code'   => (string) ($row['employee_code'] ?? ''),
                            ];
                        }

                        usort($normalizedRows, static function (array $a, array $b) use ($sortBy, $sortDir): int {
                            if ($sortBy === 'employee_name') {
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

                            return $sortDir === 'asc' ? $compare : -$compare;
                        });

                        $data['teamDailyAttendanceRecords'] = $normalizedRows;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Manager Dashboard Error: ' . $e->getMessage());
                    $data['managedDepartments'] = [];
                    $data['managedDepartmentCount'] = 0;
                    $data['teamCount'] = 0;
                    $data['pendingTeamLeaves'] = 0;
                    $data['teamAttendance'] = [
                        'present' => 0,
                        'late'    => 0,
                        'absent'  => 0,
                        'leave'   => 0,
                    ];
                    $data['teamDailyAttendanceDate'] = date('Y-m-d');
                    $data['teamDailyAttendanceSortBy'] = 'date';
                    $data['teamDailyAttendanceSortDir'] = 'desc';
                    $data['teamDailyAttendanceRecords'] = [];
                }
                break;

            default:
                // Employee — get employee record linked to user
                try {
                    $employee = $employeeModel->where('user_id', $session->get('user_id'))->first();
                    if ($employee) {
                        $data['employee'] = $employee;
                        $db = Database::connect();
                        $data['attendanceCount'] = (int) $db->table('attendance_logs')
                            ->where('employee_id', $employee->id)
                            ->countAllResults();
                        $data['attendance'] = $attendanceModel
                            ->where('employee_id', $employee->id)
                            ->orderBy('date', 'DESC')
                            ->orderBy('time_in', 'DESC')
                            ->findAll(10);
                        $data['leaves'] = $leaveModel->where('employee_id', $employee->id)->findAll();
                    } else {
                        // No employee record exists yet - create placeholder data
                        $data['employee'] = null;
                        $data['attendanceCount'] = 0;
                        $data['attendance'] = [];
                        $data['leaves'] = [];
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Dashboard Employee Error: ' . $e->getMessage());
                    $data['employee'] = null;
                    $data['attendanceCount'] = 0;
                    $data['attendance'] = [];
                    $data['leaves'] = [];
                    $data['error'] = 'Unable to load employee data.';
                }
                break;
        }

        return view('auth/dashboard', $data);
    }

    /**
     * Display user profile
     */
    public function profile()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');

        $data['user'] = $userModel->find($userId);

        return view('profile/view', $data);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password) && strlen($password >= 6)) {
            $data['password'] = $password;
        }

        $userModel->update($userId, $data);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
