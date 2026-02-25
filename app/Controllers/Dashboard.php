<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;
<<<<<<< HEAD
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
=======
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\LeaveModel;
use App\Models\AttendanceModel;
use App\Models\SalaryModel;
>>>>>>> 24834a9814a10c33e3830d5531979d46ce3245e3

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

<<<<<<< HEAD
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
                        $data['attendance'] = $attendanceModel->where('employee_id', $employee['id'])->findAll();
                        $data['leaves'] = $leaveModel->where('employee_id', $employee['id'])->findAll();
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
=======
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
>>>>>>> 24834a9814a10c33e3830d5531979d46ce3245e3

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
