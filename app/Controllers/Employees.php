<?php

namespace App\Controllers;

use App\Models\EmployeeModel;

class Employees extends BaseController
{
    protected $employeeModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        // Require login
        if (! session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        try {
            $employees = $this->employeeModel->orderBy('created_at', 'DESC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to load employees: ' . $e->getMessage());
            $employees = [];
        }

        $data = [
            'employees' => $employees,
            'currentUserId' => session()->get('user_id'),
        ];

        return view('hrms/employees/index', $data);
    }
}
