<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\SalaryModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;

class Employees extends BaseController
{
    protected $employeeModel;
    protected $salaryModel;
    protected $departmentModel;
    protected $positionModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->salaryModel = new SalaryModel();
        $this->departmentModel = new DepartmentModel();
        $this->positionModel = new PositionModel();
    }

    /**
     * List all employees
     */
    public function index()
    {
        // Require login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Check access: HR Admin or Super Admin
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $employees = $this->employeeModel->orderBy('created_at', 'DESC')->findAll();
            $departments = $this->departmentModel->getActiveDepartments();
            $positions = $this->positionModel->getActivePositions();
            
            // Create position lookup array for quick access
            $positionMap = [];
            foreach ($positions as $pos) {
                $positionMap[$pos->id] = $pos->name;
            }
            
            // Create department lookup array for quick access
            $departmentMap = [];
            foreach ($departments as $dept) {
                $departmentMap[$dept->id] = $dept->name;
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to load employees: ' . $e->getMessage());
            $employees = [];
            $departments = [];
            $positions = [];
            $positionMap = [];
            $departmentMap = [];
        }

        $data = [
            'employees' => $employees,
            'departments' => $departments,
            'positions' => $positions,
            'positionMap' => $positionMap,
            'departmentMap' => $departmentMap,
            'currentUserId' => session()->get('user_id'),
        ];

        return view('employee/index', $data);
    }

    /**
     * Show employee creation form
     */
    public function create()
    {
        // Require login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Check access: HR Admin or Super Admin
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $departments = $this->departmentModel->getActiveDepartments();
            $positions = $this->positionModel->getActivePositions();
        } catch (\Exception $e) {
            log_message('error', 'Failed to load departments/positions: ' . $e->getMessage());
            $departments = [];
            $positions = [];
        }

        $data = [
            'departments' => $departments,
            'positions' => $positions,
        ];

        return view('employee/create', $data);
    }

    /**
     * Store a newly created employee
     */
    public function store()
    {
        // Check if POST request
        if (!$this->request->is('post')) {
            return redirect()->to('/employee');
        }

        // Check access: HR Admin or Super Admin
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[employees.email]',
            'phone' => 'permit_empty',
            'department_id' => 'permit_empty|integer',
            'position_id' => 'permit_empty|integer',
            'date_of_birth' => 'permit_empty|valid_date',
            'date_of_joining' => 'required|valid_date',
            'status' => 'permit_empty|in_list[active,inactive,suspended]',
        ];

        if (!$this->validate($rules)) {
            // Check if it's an AJAX request
            if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Auto-generate Employee ID in format PPA-00001
        $lastEmployee = $this->employeeModel->orderBy('id', 'DESC')->first();
        $nextNumber = 1;
        
        if ($lastEmployee) {
            // Extract the number from the last employee ID
            if (preg_match('/PPA-(\d+)/', $lastEmployee->employee_id, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }
        
        $employeeId = 'PPA-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $data = [
            'employee_id' => $employeeId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'department_id' => $this->request->getPost('department_id'),
            'position_id' => $this->request->getPost('position_id'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'date_of_joining' => $this->request->getPost('date_of_joining'),
            'status' => $this->request->getPost('status') ?? 'active',
        ];

        try {
            if ($this->employeeModel->insert($data)) {
                // Check if it's an AJAX request
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Employee created successfully',
                        'employee_id' => $employeeId
                    ]);
                }
                return redirect()->to('/employee')->with('success', 'Employee created successfully');
            } else {
                // Check if it's an AJAX request
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to create employee'
                    ]);
                }
                return redirect()->back()->with('error', 'Failed to create employee');
            }
        } catch (\Exception $e) {
            log_message('error', 'Employee creation failed: ' . $e->getMessage());
            // Check if it's an AJAX request
            if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while creating the employee'
                ]);
            }
            return redirect()->back()->with('error', 'An error occurred while creating the employee');
        }
    }

    /**
     * Show employee details
     */
    public function show($id)
    {
        // Require login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Check access: HR Admin or Super Admin
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $employee = $this->employeeModel->find($id);
            if (!$employee) {
                return redirect()->to('/employee')->with('error', 'Employee not found');
            }

            // Load related department and position information
            $department = null;
            $position = null;
            
            if ($employee->department_id) {
                $department = $this->departmentModel->find($employee->department_id);
            }
            
            if ($employee->position_id) {
                $position = $this->positionModel->find($employee->position_id);
            }

            $data = [
                'employee' => $employee,
                'department' => $department,
                'position' => $position,
            ];

            return view('employee/show', $data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to load employee: ' . $e->getMessage());
            return redirect()->to('/employee')->with('error', 'Failed to load employee details');
        }
    }

    /**
     * Show employee edit form
     */
    public function edit($id)
    {
        // Require login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Check access: HR Admin or Super Admin
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $employee = $this->employeeModel->find($id);
            if (!$employee) {
                return redirect()->to('/employee')->with('error', 'Employee not found');
            }

            $departments = $this->departmentModel->getActiveDepartments();
            $positions = $this->positionModel->getActivePositions();

            $data = [
                'employee' => $employee,
                'departments' => $departments,
                'positions' => $positions,
            ];

            return view('employee/edit', $data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to load employee for edit: ' . $e->getMessage());
            return redirect()->to('/employee')->with('error', 'Failed to load employee');
        }
    }

    /**
     * Update an employee
     */
    public function update($id)
    {
        // Check if POST request
        if (!$this->request->is('post')) {
            return redirect()->to('/employee');
        }

        // Check access: HR Admin or Super Admin
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/employee')->with('error', 'Employee not found');
        }

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[employees.email,id,' . $id . ']',
            'phone' => 'permit_empty',
            'department_id' => 'permit_empty|integer',
            'position_id' => 'permit_empty|integer',
            'date_of_birth' => 'permit_empty|valid_date',
            'date_of_joining' => 'required|valid_date',
            'status' => 'permit_empty|in_list[active,inactive,suspended]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'department_id' => $this->request->getPost('department_id'),
            'position_id' => $this->request->getPost('position_id'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'date_of_joining' => $this->request->getPost('date_of_joining'),
            'status' => $this->request->getPost('status') ?? 'active',
        ];

        try {
            if ($this->employeeModel->update($id, $data)) {
                return redirect()->to('/employee')->with('success', 'Employee updated successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to update employee');
            }
        } catch (\Exception $e) {
            log_message('error', 'Employee update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the employee');
        }
    }

    /**
     * Delete an employee
     */
    public function delete($id)
    {
        // Check if POST request
        if (!$this->request->is('post')) {
            return redirect()->to('/employee');
        }

        // Check access: Super Admin only for deletion
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'admin'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Employee not found'
            ]);
        }

        try {
            $this->employeeModel->delete($id);
            return redirect()->to('/employee')->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            log_message('error', 'Employee deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete employee');
        }
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
