<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\SalaryModel;

class Employees extends BaseController
{
    protected $employeeModel;
    protected $salaryModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->salaryModel = new SalaryModel();
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

    /**
     * Display salary management page
     */
    public function salary()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        try {
            $salaries = $this->salaryModel
                ->select('salaries.*, employees.name, employees.employee_id')
                ->join('employees', 'employees.id = salaries.employee_id', 'left')
                ->orderBy('employees.name', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to load salaries: ' . $e->getMessage());
            $salaries = [];
        }

        $data['salaries'] = $salaries;
        return view('salary/manage', $data);
    }

    /**
     * Update employee salary
     */
    public function updateSalary()
    {
        // Check if user is Super Admin
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        $salaryId = $this->request->getPost('salary_id');
        $baseSalary = $this->request->getPost('base_salary');
        $allowances = $this->request->getPost('allowances') ?? 0;
        $deductions = $this->request->getPost('deductions') ?? 0;

        if (!$salaryId || !$baseSalary) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Salary ID and base salary are required'
            ]);
        }

        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Salary record not found'
            ]);
        }

        $grossSalary = (float)$baseSalary + (float)$allowances - (float)$deductions;

        $this->salaryModel->update($salaryId, [
            'base_salary' => $baseSalary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'gross_salary' => $grossSalary,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Salary updated successfully'
        ]);
    }
}
