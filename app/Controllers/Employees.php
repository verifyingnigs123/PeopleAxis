<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\SalaryModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

class Employees extends BaseController
{
    protected $employeeModel;
    protected $salaryModel;
    protected $departmentModel;
    protected $positionModel;
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->salaryModel = new SalaryModel();
        $this->departmentModel = new DepartmentModel();
        $this->positionModel = new PositionModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
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
            
            // Get pending employees for Super Admin approval
            $pendingEmployees = [];
            $isSuperAdmin = $role === 'Super Admin' || $role === 'admin';
            if ($isSuperAdmin) {
                $pendingEmployees = $this->employeeModel->getPendingEmployees();
            }
            
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
            $pendingEmployees = [];
        }

        $data = [
            'employees' => $employees,
            'departments' => $departments,
            'positions' => $positions,
            'positionMap' => $positionMap,
            'departmentMap' => $departmentMap,
            'pendingEmployees' => $pendingEmployees,
            'isSuperAdmin' => $isSuperAdmin,
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
            'account_status' => 'pending',  // New employee account is pending approval
        ];

        try {
            if ($this->employeeModel->insert($data)) {
                $newEmployeeId = $this->employeeModel->getInsertID();
                $firstName = $this->request->getPost('first_name');
                $lastName = $this->request->getPost('last_name');
                
                // Send notification to all Super Admins
                // First get the Super Admin role ID
                $db = \Config\Database::connect();
                $superAdminRole = $db->table('roles')->where('name', 'Super Admin')->get()->getRow();
                
                $superAdmins = [];
                if ($superAdminRole) {
                    $superAdmins = $this->userModel
                        ->where('role_id', $superAdminRole->id)
                        ->where('is_active', 1)
                        ->findAll();
                }

                foreach ($superAdmins as $superAdmin) {
                    $this->notificationModel->insert([
                        'user_id' => $superAdmin->id,
                        'title' => 'New Employee Awaiting Approval',
                        'message' => "A new employee '{$firstName} {$lastName}' (ID: {$employeeId}) has been added and is waiting for account creation and approval.",
                        'type' => 'warning',
                        'icon' => 'fas fa-user-check',
                        'link' => '/employee/review/' . $newEmployeeId,
                        'is_read' => false,
                    ]);
                }

                // Check if it's an AJAX request
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Employee created successfully. Super Admin notification sent.',
                        'employee_id' => $employeeId
                    ]);
                }
                return redirect()->to('/employee')->with('success', 'Employee created successfully. Super Admin will be notified to create an account.');
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

    /**
     * Review employee details for account approval (Super Admin only)
     */
    public function review($employeeId)
    {
        // Check access: Super Admin only
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        try {
            $employee = $this->employeeModel->find($employeeId);
            if (!$employee) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }

            // Load related information
            $department = $employee->department_id ? $this->departmentModel->find($employee->department_id) : null;
            $position = $employee->position_id ? $this->positionModel->find($employee->position_id) : null;

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => $employee->id,
                    'employee_id' => $employee->employee_id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'date_of_birth' => $employee->date_of_birth,
                    'date_of_joining' => $employee->date_of_joining,
                    'department' => $department ? $department->name : 'N/A',
                    'position' => $position ? $position->name : 'N/A',
                    'account_status' => $employee->account_status,
                    'created_at' => $employee->created_at,
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to review employee: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error retrieving employee details'
            ]);
        }
    }

    /**
     * Approve employee and create system account (Super Admin only)
     */
    public function approveAccount($employeeId)
    {
        // Check access: Super Admin only
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        try {
            $employee = $this->employeeModel->find($employeeId);
            if (!$employee) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }

            // Generate default credentials
            $username = strtolower($employee->first_name[0] . $employee->last_name);
            $password = $this->generatePassword();
            
            // Get HR Admin role
            $db = \Config\Database::connect();
            $hrAdminRole = $db->table('roles')
                ->where('name', 'HR Admin')
                ->first();

            // Create user account
            $userData = [
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $employee->email,
                'password' => $password,
                'role_id' => $hrAdminRole ? $hrAdminRole->id : 3,  // Default to HR Admin role
                'is_active' => 1,
            ];

            if (!$this->userModel->insert($userData)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to create user account'
                ]);
            }

            $newUserId = $this->userModel->getInsertID();

            // Update employee with user_id and approved status
            $this->employeeModel->update($employeeId, [
                'user_id' => $newUserId,
                'account_status' => 'approved',
            ]);

            // Send email with credentials
            $this->sendCredentialsEmail($employee->email, $username, $password, $employee->first_name);

            // Notify HR Admin
            $hrAdmins = $this->userModel
                ->where('role_id', $hrAdminRole->id)
                ->where('is_active', 1)
                ->findAll();

            foreach ($hrAdmins as $hrAdmin) {
                $this->notificationModel->insert([
                    'user_id' => $hrAdmin->id,
                    'title' => 'Employee Account Approved',
                    'message' => "The employee account for '{$employee->first_name} {$employee->last_name}' has been approved by Super Admin. Account credentials have been sent to their email.",
                    'type' => 'success',
                    'icon' => 'fas fa-check-circle',
                    'link' => '/employee/' . $employeeId,
                    'is_read' => false,
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Employee account approved! Credentials sent to ' . $employee->email,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to approve employee account: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error approving employee account: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject employee account (Super Admin only)
     */
    public function rejectAccount($employeeId)
    {
        // Check access: Super Admin only
        if (session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        try {
            $employee = $this->employeeModel->find($employeeId);
            if (!$employee) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }

            $rejectionNotes = $this->request->getPost('rejection_notes') ?? 'No reason provided';

            // Update employee with rejection status
            $this->employeeModel->update($employeeId, [
                'account_status' => 'rejected',
                'approval_notes' => $rejectionNotes,
            ]);

            // Notify HR Admin about rejection
            $db = \Config\Database::connect();
            $hrAdminRole = $db->table('roles')->where('name', 'HR Admin')->first();
            
            $hrAdmins = $this->userModel
                ->where('role_id', $hrAdminRole->id)
                ->where('is_active', 1)
                ->findAll();

            foreach ($hrAdmins as $hrAdmin) {
                $this->notificationModel->insert([
                    'user_id' => $hrAdmin->id,
                    'title' => 'Employee Account Rejected',
                    'message' => "The employee account for '{$employee->first_name} {$employee->last_name}' (ID: {$employee->employee_id}) has been rejected by Super Admin. Reason: {$rejectionNotes}",
                    'type' => 'danger',
                    'icon' => 'fas fa-times-circle',
                    'link' => '/employee/' . $employeeId,
                    'is_read' => false,
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Employee account rejected. HR Admin has been notified.',
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to reject employee account: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error rejecting employee account'
            ]);
        }
    }

    /**
     * Generate a random password
     */
    private function generatePassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    /**
     * Send credentials email to employee
     */
    private function sendCredentialsEmail($email, $username, $password, $firstName)
    {
        try {
            $emailService = \Config\Services::email();
            $emailService->setFrom(env('email.fromEmail'), env('email.fromName', 'PeopleAxis HR System'));
            $emailService->setTo($email);
            $emailService->setSubject('Welcome to PeopleAxis HR System - Your Account Credentials');

            $htmlBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                        .content { padding: 20px; }
                        .credentials { background: #f8f9fa; padding: 15px; border-left: 4px solid #2a5298; margin: 20px 0; }
                        .credentials p { margin: 10px 0; }
                        .label { font-weight: bold; color: #2a5298; }
                        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Welcome to PeopleAxis HR System</h2>
                        </div>
                        <div class='content'>
                            <p>Dear {$firstName},</p>
                            <p>Your employee account has been approved and is now active in the PeopleAxis HR System. Please use the credentials below to log in:</p>
                            <div class='credentials'>
                                <p><span class='label'>Email:</span> {$email}</p>
                                <p><span class='label'>Password:</span> {$password}</p>
                            </div>
                            <p><strong>Important:</strong> Please change your password upon first login for security purposes.</p>
                            <p>If you have any questions or issues, please contact the HR department.</p>
                            <p>Best regards,<br>PeopleAxis HR System</p>
                        </div>
                        <div class='footer'>
                            <p>This is an automated email. Please do not reply to this message.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $emailService->setMessage($htmlBody);
            $emailService->send();

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to send credentials email: ' . $e->getMessage());
            return false;
        }
    }
}
