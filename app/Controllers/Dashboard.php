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
                // Show team metrics — assume departments have manager_id field
                $managerId = $session->get('user_id');
                try {
                    // Get departments managed by this user
                    $db = \Config\Database::connect();
                    $departmentIds = $db->table('departments')
                        ->where('manager_id', $managerId)
                        ->select('id')
                        ->get()
                        ->getResultArray();
                    
                    // Get employees in those departments
                    if (!empty($departmentIds)) {
                        $deptIds = array_column($departmentIds, 'id');
                        $teamEmployees = $employeeModel->whereIn('department_id', $deptIds)->findAll();
                        $data['teamCount'] = count($teamEmployees);
                        $data['teamAttendance'] = $attendanceModel->getTeamAttendance($teamEmployees);
                    } else {
                        $data['teamCount'] = 0;
                        $data['teamAttendance'] = [];
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Manager Dashboard Error: ' . $e->getMessage());
                    $data['teamCount'] = 0;
                    $data['teamAttendance'] = [];
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
