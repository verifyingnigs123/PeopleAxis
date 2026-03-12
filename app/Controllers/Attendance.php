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

        try {
            $employee = $this->employeeModel->where('user_id', session()->get('user_id'))->first();
            
            if (!$employee) {
                return redirect()->back()->with('error', 'Employee profile not found');
            }

            $attendance = $this->attendanceModel
                ->where('employee_id', $employee['id'])
                ->orderBy('date', 'DESC')
                ->paginate(20);

            $data['attendance'] = $attendance;
            $data['pager'] = $this->attendanceModel->pager;

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
                ->select('attendance.*, employees.name, employees.employee_id')
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
        // Check if user is Manager
        if (session()->get('role') !== 'manager') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Manager only.');
        }

        try {
            $db = \Config\Database::connect();
            $managerId = session()->get('user_id');

            // Get departments managed by this user
            $departmentIds = $db->table('departments')
                ->where('manager_id', $managerId)
                ->select('id')
                ->get()
                ->getResultArray();

            if (empty($departmentIds)) {
                $data['attendance'] = [];
                return view('attendance/team', $data);
            }

            $deptIds = array_column($departmentIds, 'id');

            // Get team members' attendance
            $attendance = $this->attendanceModel
                ->select('attendance.*, employees.name, employees.employee_id, departments.name as department')
                ->join('employees', 'employees.id = attendance.employee_id', 'left')
                ->join('departments', 'departments.id = employees.department_id', 'left')
                ->whereIn('employees.department_id', $deptIds)
                ->orderBy('attendance.date', 'DESC')
                ->paginate(50);

            $data['attendance'] = $attendance;
            $data['pager'] = $this->attendanceModel->pager;

            return view('attendance/team', $data);
        } catch (\Exception $e) {
            log_message('error', 'Team attendance error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load team attendance');
        }
    }
}
