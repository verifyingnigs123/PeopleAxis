<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;

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
                break;

            case 'Manager':
                try {
                    $teamContext = $this->getManagedTeamContext();

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
                            ->whereIn('status', ['manager_approved', 'approved'])
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
                }
                break;

            default:
                // Employee — get employee record linked to user
                try {
                    $employee = $employeeModel->where('user_id', $session->get('user_id'))->first();
                    if ($employee) {
                        $data['employee'] = $employee;
                        $data['attendance'] = $attendanceModel->where('employee_id', $employee->id)->findAll();
                        $data['leaves'] = $leaveModel->where('employee_id', $employee->id)->findAll();
                    } else {
                        // No employee record exists yet - create placeholder data
                        $data['employee'] = null;
                        $data['attendance'] = [];
                        $data['leaves'] = [];
                        $data['warning'] = 'Employee profile not yet created. Please contact HR.';
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Dashboard Employee Error: ' . $e->getMessage());
                    $data['employee'] = null;
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
