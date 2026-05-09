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

        // Super Admin and HR Admin should view the global attendance logs instead
        $roleName = strtolower(session()->get('role_name') ?? session()->get('role') ?? '');
        if (in_array($roleName, ['super admin', 'admin', 'hr admin', 'hr'])) {
            return redirect()->to('/attendance/logs');
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
     * Display attendance logs (Super Admin & HR Admin)
     */
    public function logs()
    {
        $roleName = strtolower(session()->get('role_name') ?? session()->get('role') ?? '');
        $isSuperAdmin = in_array($roleName, ['super admin', 'admin']);
        $isHRAdmin = in_array($roleName, ['hr admin', 'hr']);

        // Check if user is Super Admin or HR Admin
        if (!$isSuperAdmin && !$isHRAdmin) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $db = \Config\Database::connect();
            $builder = $this->attendanceModel
                ->select("attendance_logs.*, CONCAT(employees.first_name, ' ', employees.last_name) as employee_name, employees.employee_id")
                ->join('employees', 'employees.id = attendance_logs.employee_id', 'left')
                ->orderBy('attendance_logs.date', 'DESC')
                ->orderBy('attendance_logs.time_in', 'DESC');
                
            // HR Admin only sees Employees and Managers (exclude Super Admin and other HR Admins)
            if ($isHRAdmin && !$isSuperAdmin) {
                $builder->join('users', 'users.email = employees.email AND users.deleted_at IS NULL AND users.is_active = 1', 'left')
                        ->join('roles', 'roles.id = users.role_id AND roles.deleted_at IS NULL', 'left')
                        ->whereIn('roles.name', ['Employee', 'Manager', 'User', 'employee', 'manager', 'user']);
            }
            
            $logs = $builder->paginate(50);
            $total = $this->attendanceModel->countAllResults();

            $data['logs'] = $logs;
            $data['total'] = $total;
            $data['pager'] = $this->attendanceModel->pager;

            return view('attendance/logs', $data);
        } catch (\Exception $e) {
            log_message('error', 'Attendance logs error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load attendance logs');
        }
    }

    /**
     * RFID attendance scanner page.
     */
    public function scanner()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('attendance/rfid_scanner');
    }

    /**
     * Process an RFID scan and record attendance.
     */
    public function scan()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/attendance/scanner');
        }

        $rfidNumber = trim((string) $this->request->getPost('rfid_number'));
        if ($rfidNumber === '') {
            return redirect()->to('/attendance/scanner')->with('error', 'RFID not recognized. Please contact HR.');
        }

        $employee = $this->employeeModel
            ->where('rfid_number', $rfidNumber)
            ->first();

        if (! $employee) {
            return redirect()->to('/attendance/scanner')->with('error', 'RFID not recognized. Please contact HR.');
        }

        $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');
        $currentHour = (int) $now->format('H');
        $currentMinute = (int) $now->format('i');
        $currentTime = $currentHour * 60 + $currentMinute; // Convert to minutes for easier comparison

        $morningCheckInStart = 8 * 60;          // 8:00 AM
        $morningCheckInEnd = 8 * 60;            // 8:00 AM
        $morningLateStart = (8 * 60) + 1;       // 8:01 AM
        $morningLateEnd = (10 * 60) + 59;       // 10:59 AM
        $morningCheckOutStart = 11 * 60;        // 11:00 AM
        $morningCheckOutEnd = (11 * 60) + 59;   // 11:59 AM
        $afternoonCheckInStart = 12 * 60;       // 12:00 PM
        $afternoonCheckInEnd = (12 * 60) + 59;  // 12:59 PM
        $afternoonLateStart = 13 * 60;          // 1:00 PM
        $afternoonLateEnd = (15 * 60) + 59;     // 3:59 PM
        $afternoonCheckOutStart = 16 * 60;      // 4:00 PM
        $afternoonCheckOutEnd = (16 * 60) + 59; // 4:59 PM

        $todayRecords = $this->attendanceModel
            ->where('employee_id', $employee->id)
            ->where('date', $date)
            ->orderBy('id', 'ASC')
            ->findAll();

        $latest = ! empty($todayRecords) ? end($todayRecords) : null;

        $isWithin = static function (int $current, int $start, int $end): bool {
            return $current >= $start && $current <= $end;
        };

        // If there is no record yet for today, allow either morning or afternoon check-in window.
        if (! $latest) {
            $isMorningCheckInWindow = $isWithin($currentTime, $morningCheckInStart, $morningCheckInEnd);
            $isMorningLateWindow = $isWithin($currentTime, $morningLateStart, $morningLateEnd);
            $isAfternoonCheckInWindow = $isWithin($currentTime, $afternoonCheckInStart, $afternoonCheckInEnd);
            $isAfternoonLateWindow = $isWithin($currentTime, $afternoonLateStart, $afternoonLateEnd);

            if (! $isMorningCheckInWindow && ! $isMorningLateWindow && ! $isAfternoonCheckInWindow && ! $isAfternoonLateWindow) {
                return redirect()->to('/attendance/scanner')->with('error', 'Check-in is only available from 8:00-10:59 AM or 12:00-3:59 PM.');
            }

            $status = ($isMorningCheckInWindow || $isAfternoonCheckInWindow) ? 'Present' : 'Late';

            $this->attendanceModel->insert([
                'employee_id' => $employee->id,
                'rfid_number' => $rfidNumber,
                'date'        => $date,
                'time_in'     => $time,
                'time_out'    => null,
                'status'      => $status,
            ]);
        } elseif (empty($latest->time_out)) {
            // The most recent session is still open; determine if it is morning or afternoon session.
            $timeInParts = explode(':', (string) $latest->time_in);
            $timeInHour = isset($timeInParts[0]) ? (int) $timeInParts[0] : 0;
            $timeInMinute = isset($timeInParts[1]) ? (int) $timeInParts[1] : 0;
            $timeInMinutes = ($timeInHour * 60) + $timeInMinute;

            $isMorningSession = $isWithin($timeInMinutes, $morningCheckInStart, $morningCheckInEnd);
            $isAfternoonSession = $isWithin($timeInMinutes, $afternoonCheckInStart, $afternoonCheckInEnd);
            $isLateMorningSession = $isWithin($timeInMinutes, $morningLateStart, $morningLateEnd);
            $isLateAfternoonSession = $isWithin($timeInMinutes, $afternoonLateStart, $afternoonLateEnd);

            if ($isMorningSession || $isLateMorningSession) {
                if (! $isWithin($currentTime, $morningCheckOutStart, $morningCheckOutEnd)) {
                    return redirect()->to('/attendance/scanner')->with('error', 'Morning check-out is only available from 11:00-11:59 AM.');
                }
            } elseif ($isAfternoonSession || $isLateAfternoonSession) {
                if (! $isWithin($currentTime, $afternoonCheckOutStart, $afternoonCheckOutEnd)) {
                    return redirect()->to('/attendance/scanner')->with('error', 'Afternoon check-out is only available from 4:00-4:59 PM.');
                }
            } else {
                return redirect()->to('/attendance/scanner')->with('error', 'Unable to determine session type for check-out.');
            }

            $this->attendanceModel->update($latest->id, [
                'rfid_number' => $rfidNumber,
                'time_out'    => $time,
            ]);
        } else {
            // Last session is complete; allow only one additional session (afternoon) within check-in window.
            if (count($todayRecords) >= 2) {
                return redirect()->to('/attendance/scanner')->with('error', 'Attendance already completed for today.');
            }

            $isAfternoonCheckInWindow = $isWithin($currentTime, $afternoonCheckInStart, $afternoonCheckInEnd);
            $isAfternoonLateWindow = $isWithin($currentTime, $afternoonLateStart, $afternoonLateEnd);

            if (! $isAfternoonCheckInWindow && ! $isAfternoonLateWindow) {
                return redirect()->to('/attendance/scanner')->with('error', 'Afternoon check-in is only available from 12:00-3:59 PM.');
            }

            $status = $isAfternoonCheckInWindow ? 'Present' : 'Late';

            $this->attendanceModel->insert([
                'employee_id' => $employee->id,
                'rfid_number' => $rfidNumber,
                'date'        => $date,
                'time_in'     => $time,
                'time_out'    => null,
                'status'      => $status,
            ]);
        }

        return redirect()->to('/attendance/scanner')->with('success', 'Attendance Recorded Successfully');
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
            $managerId = (int) session()->get('user_id');
            $db = \Config\Database::connect();

            // Get only the manager's departments
            $departments = $db->table('departments')
                ->select('id, name')
                ->where('manager_id', $managerId)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            $departmentIds = array_map('intval', array_column($departments, 'id'));

            // Get only employees from manager's departments
            $teamMembers = [];
            $employeeIds = [];
            
            if (!empty($departmentIds)) {
                $teamMembers = $db->table('employees')
                    ->select('employees.id, employees.employee_id, employees.first_name, employees.last_name, employees.email, employees.department_id, employees.status, employees.account_status, departments.name as department_name')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->join('users', 'users.email = employees.email', 'left')
                    ->join('roles', 'roles.id = users.role_id', 'left')
                    ->where('employees.account_status', 'approved')
                    ->groupStart()
                        ->where('employees.status', 'active')
                        ->orWhere('employees.status IS NULL', null, false)
                        ->orWhere('employees.status', '')
                    ->groupEnd()
                    ->whereIn('employees.department_id', $departmentIds)
                    ->groupStart()
                        ->whereNotIn('LOWER(roles.name)', ['super admin', 'hr admin'])
                        ->orWhere('roles.name IS NULL', null, false)
                    ->groupEnd()
                    ->orderBy('employees.first_name', 'ASC')
                    ->orderBy('employees.last_name', 'ASC')
                    ->get()
                    ->getResultArray();

                $employeeIds = array_map('intval', array_column($teamMembers, 'id'));
            }

            $data = [
                'selectedDate'      => $selectedDate,
                'managedDepartments' => $departments,
                'teamCount'         => count($teamMembers),
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

            if ($employeeIds === []) {
                return view('attendance/team', $data);
            }

            $rawAttendance = $db->table('attendance_logs')
                ->select('employee_id, status')
                ->whereIn('employee_id', $employeeIds)
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
                ->whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->where('start_date <=', $selectedDate)
                ->where('end_date >=', $selectedDate)
                ->get()
                ->getResultArray();

            $onLeaveIds = array_map('intval', array_column($leaveRows, 'employee_id'));
            $onLeaveLookup = array_fill_keys($onLeaveIds, true);
            $data['attendanceSummary']['on_leave'] = count($onLeaveLookup);

            foreach ($departments as $department) {
                $departmentId = (int) ($department['id'] ?? 0);
                $data['departmentSummary'][$departmentId] = [
                    'name'     => $department['name'] ?? 'Unassigned',
                    'members'  => 0,
                    'recorded' => 0,
                    'missing'  => 0,
                    'on_leave' => 0,
                ];
            }

            foreach ($teamMembers as $member) {
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

            // Build query for attendance records with search and filtering
            $query = $this->attendanceModel
                ->select("attendance_logs.*, CONCAT(employees.first_name, ' ', employees.last_name) as employee_name, employees.employee_id as staff_code, departments.name as department_name")
                ->join('employees', 'employees.id = attendance_logs.employee_id', 'left')
                ->join('departments', 'departments.id = employees.department_id', 'left')
                ->whereIn('attendance_logs.employee_id', $employeeIds)
                ->where('attendance_logs.date', $selectedDate);

            // Apply search filter
            $search = trim((string) $this->request->getGet('search'));
            if ($search !== '') {
                $query->groupStart()
                    ->like('employees.first_name', $search)
                    ->orLike('employees.last_name', $search)
                    ->orLike('employees.employee_id', $search)
                    ->groupEnd();
            }

            // Apply status filter
            $statusFilter = trim((string) $this->request->getGet('status'));
            if ($statusFilter !== '') {
                if ($statusFilter === 'absent') {
                    $query->where('attendance_logs.status', null);
                } else {
                    $query->where('attendance_logs.status', $statusFilter);
                }
            }

            $data['attendanceRecords'] = $query
                ->orderBy('attendance_logs.time_in', 'DESC')
                ->orderBy('attendance_logs.created_at', 'DESC')
                ->paginate(25);
            $data['pager'] = $this->attendanceModel->pager;
            $data['searchQuery'] = $search;
            $data['statusFilter'] = $statusFilter;

            return view('attendance/team', $data);
        } catch (\Exception $e) {
            log_message('error', 'Team attendance error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load team attendance');
        }
    }

    /**
     * Record attendance via RFID scan (AJAX endpoint for dashboard)
     */
    public function recordRfid()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        $input = $this->request->getJSON(true);
        $rfidNumber = trim((string) ($input['rfid_number'] ?? ''));

        if ($rfidNumber === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'RFID not recognized']);
        }

        try {
            $employee = $this->employeeModel
                ->where('rfid_number', $rfidNumber)
                ->first();

            if (!$employee) {
                return $this->response->setJSON(['success' => false, 'message' => 'Employee not found']);
            }

            $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
            $date = $now->format('Y-m-d');
            $time = $now->format('H:i:s');
            $currentHour = (int) $now->format('H');
            $currentMinute = (int) $now->format('i');
            $currentTime = $currentHour * 60 + $currentMinute;

            $morningCheckInStart = 8 * 60;          // 8:00 AM
            $morningCheckInEnd = 8 * 60;            // 8:00 AM
            $morningLateStart = (8 * 60) + 1;       // 8:01 AM
            $morningLateEnd = (10 * 60) + 59;       // 10:59 AM
            $morningCheckOutStart = 11 * 60;        // 11:00 AM
            $morningCheckOutEnd = (11 * 60) + 59;   // 11:59 AM

            $afternoonCheckInStart = 12 * 60;       // 12:00 PM
            $afternoonCheckInEnd = (12 * 60) + 59;  // 12:59 PM
            $afternoonLateStart = 13 * 60;          // 1:00 PM
            $afternoonLateEnd = (14 * 60) + 59;     // 2:59 PM
            $afternoonCheckOutStart = 15 * 60;      // 3:00 PM
            $afternoonCheckOutEnd = (17 * 60) + 59; // 5:59 PM

            // Check if already checked in today
            $todayRecord = $this->attendanceModel
                ->where('employee_id', $employee->id)
                ->where('date', $date)
                ->first();

            $isMorningCheckInWindow = $currentTime >= $morningCheckInStart && $currentTime <= $morningCheckInEnd;
            $isMorningLateWindow = $currentTime >= $morningLateStart && $currentTime <= $morningLateEnd;
            $isMorningCheckOutWindow = $currentTime >= $morningCheckOutStart && $currentTime <= $morningCheckOutEnd;

            $isAfternoonCheckInWindow = $currentTime >= $afternoonCheckInStart && $currentTime <= $afternoonCheckInEnd;
            $isAfternoonLateWindow = $currentTime >= $afternoonLateStart && $currentTime <= $afternoonLateEnd;
            $isAfternoonCheckOutWindow = $currentTime >= $afternoonCheckOutStart && $currentTime <= $afternoonCheckOutEnd;

            if ($todayRecord) {
                if ($todayRecord->time_out === null) {
                    // Check out
                    $this->attendanceModel->update($todayRecord->id, ['time_out' => $time]);
                    return $this->response->setJSON([
                        'success' => true, 
                        'message' => 'Check out successful',
                        'action' => 'checkout'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false, 
                        'message' => 'Already checked out today'
                    ]);
                }
            } else {
                // Check in
                $status = 'Present';
                if ($isMorningLateWindow || $isAfternoonLateWindow) {
                    $status = 'Late';
                }

                $this->attendanceModel->insert([
                    'employee_id' => $employee->id,
                    'rfid_number' => $rfidNumber,
                    'date'        => $date,
                    'time_in'     => $time,
                    'time_out'    => null,
                    'status'      => $status,
                ]);

                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Check in successful',
                    'action' => 'checkin'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'RFID recording error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error recording attendance'
            ]);
        }
    }

    /**
     * Quick attendance actions page
     */
    public function now()
    {
        try {
            $employee = null;
            $todayRecords = [];
            
            // If user is logged in, get their employee record
            if (session()->get('logged_in')) {
                $employee = $this->getCurrentEmployeeRecord();
                $today = date('Y-m-d');

                if ($employee) {
                    $todayRecords = $this->attendanceModel
                        ->where('employee_id', $employee->id)
                        ->where('date', $today)
                        ->orderBy('time_in', 'ASC')
                        ->findAll();
                }
            }

            $data = [
                'employee'      => $employee,
                'todayRecords'  => $todayRecords,
                'currentTime'   => date('H:i:s'),
                'isLoggedIn'    => session()->get('logged_in') ? true : false,
            ];

            return view('attendance/now', $data);
        } catch (\Exception $e) {
            log_message('error', 'Quick attendance error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load quick attendance');
        }
    }

    /**
     * Check-in action
     */
    public function checkIn()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        try {
            $employee = $this->getCurrentEmployeeRecord();
            if (!$employee) {
                return redirect()->to('/attendance/now')->with('error', 'No employee profile found');
            }

            $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            // Check if already checked in today
            $existingRecord = $this->attendanceModel
                ->where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

            if ($existingRecord) {
                return redirect()->to('/attendance/now')->with('warning', 'You already have an attendance record for today');
            }

            $status = 'Present';
            $hour = (int) $now->format('H');
            $minute = (int) $now->format('i');

            // Mark as late if after 8:00 AM and before 11:00 AM (morning)
            if (($hour === 8 && $minute > 0) || ($hour >= 9 && $hour < 11)) {
                $status = 'Late';
            }

            $this->attendanceModel->insert([
                'employee_id' => $employee->id,
                'rfid_number' => $employee->rfid_number ?? '',
                'date'        => $today,
                'time_in'     => $currentTime,
                'time_out'    => null,
                'status'      => $status,
            ]);

            return redirect()->to('/attendance/now')->with('success', 'Check-in recorded successfully at ' . date('h:i A', strtotime($currentTime)));
        } catch (\Exception $e) {
            log_message('error', 'Check-in error: ' . $e->getMessage());
            return redirect()->to('/attendance/now')->with('error', 'Error recording check-in');
        }
    }

    /**
     * Check-out action
     */
    public function checkOut()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        try {
            $employee = $this->getCurrentEmployeeRecord();
            if (!$employee) {
                return redirect()->to('/attendance/now')->with('error', 'No employee profile found');
            }

            $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            // Find today's last open record
            $lastRecord = $this->attendanceModel
                ->where('employee_id', $employee->id)
                ->where('date', $today)
                ->where('time_out', null)
                ->orderBy('time_in', 'DESC')
                ->first();

            if (!$lastRecord) {
                return redirect()->to('/attendance/now')->with('warning', 'No open attendance record to check out from');
            }

            $this->attendanceModel->update($lastRecord->id, [
                'time_out' => $currentTime,
            ]);

            return redirect()->to('/attendance/now')->with('success', 'Check-out recorded successfully at ' . date('h:i A', strtotime($currentTime)));
        } catch (\Exception $e) {
            log_message('error', 'Check-out error: ' . $e->getMessage());
            return redirect()->to('/attendance/now')->with('error', 'Error recording check-out');
        }
    }

    /**
     * Break-out action (mark break start)
     */
    public function breakOut()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        try {
            $employee = $this->getCurrentEmployeeRecord();
            if (!$employee) {
                return redirect()->to('/attendance/now')->with('error', 'No employee profile found');
            }

            $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            // Find today's last open record without break_out
            $lastRecord = $this->attendanceModel
                ->where('employee_id', $employee->id)
                ->where('date', $today)
                ->where('time_out', null)
                ->orderBy('time_in', 'DESC')
                ->first();

            if (!$lastRecord) {
                return redirect()->to('/attendance/now')->with('warning', 'You need to check in first');
            }

            // Check if break_out already recorded
            $db = \Config\Database::connect();
            $breakOut = $db->table('attendance_logs')
                ->where('id', $lastRecord->id)
                ->where('break_out IS NOT NULL', null, false)
                ->get()
                ->getFirstRow();

            if ($breakOut) {
                return redirect()->to('/attendance/now')->with('warning', 'Break already marked');
            }

            // Update break_out time
            $this->attendanceModel->update($lastRecord->id, [
                'break_out' => $currentTime,
            ]);

            return redirect()->to('/attendance/now')->with('success', 'Break started at ' . date('h:i A', strtotime($currentTime)));
        } catch (\Exception $e) {
            log_message('error', 'Break-out error: ' . $e->getMessage());
            return redirect()->to('/attendance/now')->with('error', 'Error recording break');
        }
    }

    /**
     * Break-in action (mark break end)
     */
    public function breakIn()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        try {
            $employee = $this->getCurrentEmployeeRecord();
            if (!$employee) {
                return redirect()->to('/attendance/now')->with('error', 'No employee profile found');
            }

            $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            // Find today's last open record with break_out but without break_in
            $db = \Config\Database::connect();
            $lastRecord = $db->table('attendance_logs')
                ->where('employee_id', $employee->id)
                ->where('date', $today)
                ->where('time_out', null)
                ->where('break_out IS NOT NULL', null, false)
                ->where('break_in IS NULL', null, false)
                ->orderBy('time_in', 'DESC')
                ->get()
                ->getFirstRow();

            if (!$lastRecord) {
                return redirect()->to('/attendance/now')->with('warning', 'No active break to resume from');
            }

            // Update break_in time
            $this->attendanceModel->update($lastRecord->id, [
                'break_in' => $currentTime,
            ]);

            return redirect()->to('/attendance/now')->with('success', 'Break ended at ' . date('h:i A', strtotime($currentTime)));
        } catch (\Exception $e) {
            log_message('error', 'Break-in error: ' . $e->getMessage());
            return redirect()->to('/attendance/now')->with('error', 'Error recording break resumption');
        }
    }

    /**
     * Process RFID attendance (AJAX endpoint)
     */
    public function rfidProcess()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $input = $this->request->getJSON(true);
            $rfidNumber = trim((string) ($input['rfid_number'] ?? ''));

            if ($rfidNumber === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid RFID']);
            }

            $employee = $this->employeeModel
                ->where('rfid_number', $rfidNumber)
                ->first();

            if (!$employee) {
                return $this->response->setJSON(['success' => false, 'message' => 'RFID not registered']);
            }

            $now = new \DateTimeImmutable('now', new \DateTimeZone(app_timezone()));
            $today = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            // Get today's records for this employee
            $db = \Config\Database::connect();
            $todayRecords = $db->table('attendance_logs')
                ->where('employee_id', $employee->id)
                ->where('date', $today)
                ->orderBy('time_in', 'ASC')
                ->get()
                ->getResultArray();

            $lastRecord = !empty($todayRecords) ? end($todayRecords) : null;

            // Determine action based on current state
            $action = '';
            $actionType = '';
            
            if (!$lastRecord) {
                // No record yet - Check In
                $status = 'Present';
                $hour = (int) $now->format('H');
                $minute = (int) $now->format('i');

                // Mark as late if after 8:00 AM and before 11:00 AM (morning) or after 1:00 PM and before 4:00 PM (afternoon)
                if (($hour >= 8 && $hour < 11) || ($hour >= 13 && $hour < 16)) {
                    if (!($hour === 8 && $minute === 0) && !($hour === 13 && $minute === 0)) {
                        $status = 'Late';
                    }
                }

                $this->attendanceModel->insert([
                    'employee_id' => $employee->id,
                    'rfid_number' => $rfidNumber,
                    'date'        => $today,
                    'time_in'     => $currentTime,
                    'time_out'    => null,
                    'break_out'   => null,
                    'break_in'    => null,
                    'status'      => $status,
                ]);

                $action = $status === 'Late' ? 'CHECK IN (LATE)' : 'CHECK IN';
                $actionType = 'check-in';
            } elseif (($lastRecord['time_out'] ?? null) === null && ($lastRecord['break_out'] ?? null) === null) {
                // Open record without break - Mark Break Out
                $db = \Config\Database::connect();
                $db->table('attendance_logs')->update(
                    ['break_out' => $currentTime],
                    ['id' => $lastRecord['id']]
                );

                $action = 'BREAK OUT';
                $actionType = 'break-out';
            } elseif (($lastRecord['time_out'] ?? null) === null && ($lastRecord['break_out'] ?? null) !== null && ($lastRecord['break_in'] ?? null) === null) {
                // Record in break - Mark Break In
                $db = \Config\Database::connect();
                $db->table('attendance_logs')->update(
                    ['break_in' => $currentTime],
                    ['id' => $lastRecord['id']]
                );

                // Check if break-in is after 1:00 PM (13:00)
                $breakHour = (int) $now->format('H');
                $breakMinute = (int) $now->format('i');
                $breakTimeInMinutes = $breakHour * 60 + $breakMinute;
                $breakInOverLimit = $breakTimeInMinutes > (13 * 60); // After 1:00 PM
                $action = $breakInOverLimit ? 'BREAK IN (OVER BREAK)' : 'BREAK IN';
                $actionType = $breakInOverLimit ? 'break-in-over' : 'break-in';
            } elseif (($lastRecord['time_out'] ?? null) === null) {
                // Open record with break completed - Check Out
                $db = \Config\Database::connect();
                $db->table('attendance_logs')->update(
                    ['time_out' => $currentTime],
                    ['id' => $lastRecord['id']]
                );

                $action = 'CHECK OUT';
                $actionType = 'check-out';
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Attendance already completed for today'
                ]);
            }

            $profileImage = $this->resolveEmployeeProfilePhoto($employee);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Attendance recorded successfully',
                'action' => $action,
                'actionType' => $actionType,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'employee_id' => $employee->employee_id ?? '',
                    'department' => $employee->department ?? '',
                    'email' => $employee->email ?? '',
                    'phone' => $employee->phone ?? '',
                    'profile_photo' => $profileImage,
                    'designation' => $employee->designation ?? '',
                ],
                'timestamp' => date('h:i A'),
                'date' => date('M d, Y'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'RFID processing error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error processing RFID: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Resolve the best available profile photo URL for an employee.
     */
    protected function resolveEmployeeProfilePhoto(object $employee): ?string
    {
        $db = \Config\Database::connect();
        $userId = (int) ($employee->user_id ?? 0);
        $email = trim((string) ($employee->email ?? ''));

        $resolveUrl = static function (?string $path): ?string {
            $path = trim((string) $path);

            if ($path === '') {
                return null;
            }

            if (preg_match('#^https?://#i', $path) === 1) {
                return $path;
            }

            return base_url($path);
        };

        $userPhotoPath = null;

        if ($userId > 0) {
            $userRow = $db->table('users')
                ->select('id, profile_photo')
                ->where('id', $userId)
                ->where('deleted_at', null)
                ->get()
                ->getRow();

            if ($userRow && !empty($userRow->profile_photo)) {
                $userPhotoPath = $userRow->profile_photo;
            }

            if ($userPhotoPath === null) {
                $latestPhoto = $db->table('profile_photos')
                    ->select('file_path')
                    ->where('user_id', $userId)
                    ->where('deleted_at', null)
                    ->orderBy('uploaded_at', 'DESC')
                    ->get(1)
                    ->getRow();

                if ($latestPhoto && !empty($latestPhoto->file_path)) {
                    $userPhotoPath = $latestPhoto->file_path;
                }
            }
        }

        if ($userPhotoPath === null && $email !== '') {
            $userRow = $db->table('users')
                ->select('id, profile_photo')
                ->where('email', $email)
                ->where('deleted_at', null)
                ->get()
                ->getRow();

            if ($userRow) {
                $userId = (int) $userRow->id;

                if (!empty($userRow->profile_photo)) {
                    $userPhotoPath = $userRow->profile_photo;
                } else {
                    $latestPhoto = $db->table('profile_photos')
                        ->select('file_path')
                        ->where('user_id', $userId)
                        ->where('deleted_at', null)
                        ->orderBy('uploaded_at', 'DESC')
                        ->get(1)
                        ->getRow();

                    if ($latestPhoto && !empty($latestPhoto->file_path)) {
                        $userPhotoPath = $latestPhoto->file_path;
                    }
                }
            }
        }

        if ($userPhotoPath === null && !empty($employee->profile_photo)) {
            $userPhotoPath = $employee->profile_photo;
        }

        return $resolveUrl($userPhotoPath);
    }

}
