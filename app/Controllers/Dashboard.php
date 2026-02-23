<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\LeaveModel;
use App\Models\AttendanceModel;
use App\Models\SalaryModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        // Initialize models
        $userModel = new UserModel();
        $employeeModel = new EmployeeModel();
        $departmentModel = new DepartmentModel();
        $positionModel = new PositionModel();
        $leaveModel = new LeaveModel();
        $attendanceModel = new AttendanceModel();
        $salaryModel = new SalaryModel();

        // Base data for all users
        $data = [
            'user' => session()->get(),
            'userRole' => $userRole,
        ];

        // Role-based data
        if ($userRole === 'admin') {
            // Admin gets full system analytics
            $data['totalUsers'] = $userModel->countAll();
            $data['adminCount'] = $userModel->where('role', 'admin')->countAllResults();
            $data['activeUsers'] = $userModel->where('is_active', 1)->countAllResults();
            $data['inactiveUsers'] = $userModel->where('is_active', 0)->countAllResults();
            
            // Employee statistics
            $data['totalEmployees'] = $employeeModel->countAll();
            $data['activeEmployees'] = $employeeModel->where('status', 'active')->countAllResults();
            $data['inactiveEmployees'] = $employeeModel->where('status', 'inactive')->countAllResults();
            
            // Department and position statistics
            $data['totalDepartments'] = $departmentModel->countAll();
            $data['totalPositions'] = $positionModel->countAll();
            
            // Leave statistics
            $data['pendingLeaves'] = $leaveModel->where('status', 'pending')->countAllResults();
            $data['approvedLeaves'] = $leaveModel->where('status', 'approved')->countAllResults();
            $data['rejectedLeaves'] = $leaveModel->where('status', 'rejected')->countAllResults();
            
            // Attendance statistics (current month)
            $currentMonth = date('Y-m');
            $data['presentThisMonth'] = $attendanceModel
                ->like('attendance_date', $currentMonth)
                ->where('status', 'present')
                ->countAllResults();
            $data['absentThisMonth'] = $attendanceModel
                ->like('attendance_date', $currentMonth)
                ->where('status', 'absent')
                ->countAllResults();
            
            // Recent activities
            $data['recentUsers'] = $userModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
            $data['recentEmployees'] = $employeeModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
            
        } elseif ($userRole === 'user') {
            // Regular user gets limited data
            $data['totalEmployees'] = $employeeModel->countAll();
            $data['totalDepartments'] = $departmentModel->countAll();
            
            // User's own attendance summary
            $data['myAttendance'] = $attendanceModel
                ->where('employee_id', $userId)
                ->orderBy('attendance_date', 'DESC')
                ->limit(10)
                ->findAll();
            
            // User's leave requests
            $data['myLeaves'] = $leaveModel
                ->where('employee_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();
            
            // User's leave statistics
            $data['myPendingLeaves'] = $leaveModel
                ->where('employee_id', $userId)
                ->where('status', 'pending')
                ->countAllResults();
            $data['myApprovedLeaves'] = $leaveModel
                ->where('employee_id', $userId)
                ->where('status', 'approved')
                ->countAllResults();
        }

        return view('dashboard/index', $data);
    }

    /**
     * API endpoint for real-time dashboard statistics
     */
    public function getStats()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        
        // Initialize models
        $userModel = new UserModel();
        $employeeModel = new EmployeeModel();
        $leaveModel = new LeaveModel();
        $attendanceModel = new AttendanceModel();

        $stats = [];

        if ($userRole === 'admin') {
            $stats = [
                'users' => [
                    'total' => $userModel->countAll(),
                    'active' => $userModel->where('is_active', 1)->countAllResults(),
                    'inactive' => $userModel->where('is_active', 0)->countAllResults(),
                ],
                'employees' => [
                    'total' => $employeeModel->countAll(),
                    'active' => $employeeModel->where('status', 'active')->countAllResults(),
                ],
                'leaves' => [
                    'pending' => $leaveModel->where('status', 'pending')->countAllResults(),
                    'approved' => $leaveModel->where('status', 'approved')->countAllResults(),
                ],
            ];
        }

        return $this->response->setJSON(['success' => true, 'data' => $stats]);
    }
}
