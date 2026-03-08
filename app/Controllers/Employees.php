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

        // Super Admin must not see the full employee table — redirect to their own approvals page
        $role = session()->get('role_name') ?? session()->get('role');
        if (in_array($role, ['Super Admin', 'admin'])) {
            return redirect()->to('/employee/pending-approvals');
        }

        // Only HR Admin beyond this point
        if (!in_array($role, ['HR Admin', 'hr'])) {
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

            // Build a map of user emails → role name from the users/roles tables,
            // restricted to active users only.
            //   "Active"  → employee email exists as an active 'Employee' role user account
            //   "Pending" → employee has no user account yet
            // Employees linked to non-Employee roles (HR Admin, Super Admin, etc.)
            // are excluded from the HR Admin employees table entirely.
            $db = \Config\Database::connect();
            $userRows = $db->table('users')
                ->select('users.email, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.is_active', 1)
                ->where('users.deleted_at IS NULL')
                ->get()
                ->getResultArray();

            // Build two sets: emails with Employee role, emails with any other role
            $employeeRoleEmailSet = [];  // email → true  (Employee role users)
            $nonEmployeeEmailSet  = [];  // email → true  (HR Admin, Super Admin, Manager, etc.)
            foreach ($userRows as $usr) {
                if (strtolower($usr['role_name']) === 'employee') {
                    $employeeRoleEmailSet[$usr['email']] = true;
                } else {
                    $nonEmployeeEmailSet[$usr['email']] = true;
                }
            }

            // Only include employees that are either:
            //   (a) not linked to any user account yet (pending), OR
            //   (b) linked to a user with the 'Employee' role
            $employees = array_filter($employees, function ($emp) use ($nonEmployeeEmailSet) {
                return !isset($nonEmployeeEmailSet[$emp->email]);
            });
            $employees = array_values($employees);

            // $userEmailSet = emails of active Employee-role users (for the badge in the view)
            $userEmailSet = $employeeRoleEmailSet;

            // Sync account_status in DB for each visible employee so it stays accurate
            // IMPORTANT: never overwrite an already-rejected or approved status
            foreach ($employees as $emp) {
                $current = $emp->account_status ?? 'pending';
                // Only auto-sync pending employees; leave rejected/approved untouched
                if (in_array($current, ['rejected', 'approved'])) {
                    continue;
                }
                $newAccountStatus = isset($userEmailSet[$emp->email]) ? 'active' : 'pending';
                if ($current !== $newAccountStatus) {
                    $this->employeeModel->skipValidation(true)->update($emp->id, ['account_status' => $newAccountStatus]);
                    $emp->account_status = $newAccountStatus;
                }
            }

        } catch (\Exception $e) {
            log_message('error', 'Failed to load employees: ' . $e->getMessage());
            $employees = [];
            $departments = [];
            $positions = [];
            $positionMap = [];
            $departmentMap = [];
            $pendingEmployees = [];
            $userEmailSet = [];
        }

        $data = [
            'employees'     => $employees,
            'departments'   => $departments,
            'positions'     => $positions,
            'positionMap'   => $positionMap,
            'departmentMap' => $departmentMap,
            'pendingEmployees' => [],
            'isSuperAdmin'  => false,
            'currentUserId' => session()->get('user_id'),
            'userEmailSet'  => $userEmailSet ?? [],
        ];

        return view('employee/index', $data);
    }

    /**
     * Pending-approvals dashboard for Super Admin only
     */
    public function pendingApprovals()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        try {
            $pending  = $this->employeeModel
                ->where('account_status', 'pending')
                ->orderBy('created_at', 'DESC')
                ->findAll();

            $rejected = $this->employeeModel
                ->where('account_status', 'rejected')
                ->orderBy('updated_at', 'DESC')
                ->findAll();

            $departments = $this->departmentModel->getActiveDepartments();
            $positions   = $this->positionModel->getActivePositions();

            $positionMap   = [];
            foreach ($positions   as $p) { $positionMap[$p->id]   = $p->name; }
            $departmentMap = [];
            foreach ($departments as $d) { $departmentMap[$d->id] = $d->name; }

        } catch (\Exception $e) {
            log_message('error', 'pendingApprovals error: ' . $e->getMessage());
            $pending  = [];
            $rejected = [];
            $positionMap   = [];
            $departmentMap = [];
        }

        return view('employee/pending_approvals', [
            'pending'      => $pending,
            'rejected'     => $rejected,
            'positionMap'  => $positionMap,
            'departmentMap'=> $departmentMap,
        ]);
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

        // Birthdate validation: must not be 2026 or later, and employee must be at least 18 years old
        $dateOfBirth = trim($this->request->getPost('date_of_birth') ?? '');
        if ($dateOfBirth !== '') {
            $dob = \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                $isAjaxCheck = $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
                $err = ['date_of_birth' => 'Please enter a valid date of birth (YYYY-MM-DD).'];
                if ($isAjaxCheck) return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $err]);
                return redirect()->back()->withInput()->with('errors', $err);
            }
            $today = new \DateTime();
            $age   = $today->diff($dob)->y;
            if ((int)$dob->format('Y') >= 2026) {
                $err = ['date_of_birth' => 'Date of birth cannot be in the year 2026 or later.'];
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $err]);
                }
                return redirect()->back()->withInput()->with('errors', $err);
            }
            if ($age < 18) {
                $err = ['date_of_birth' => 'Employee must be at least 18 years old.'];
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $err]);
                }
                return redirect()->back()->withInput()->with('errors', $err);
            }
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
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name' => trim($this->request->getPost('last_name')),
            'email' => trim($this->request->getPost('email')),
            'phone' => $this->request->getPost('phone'),
            'department_id' => $this->request->getPost('department_id'),
            'position_id' => $this->request->getPost('position_id'),
            'date_of_birth' => $dateOfBirth ?: null,
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
                        'link' => site_url('employee/review/' . $newEmployeeId),
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
                'employee'    => $employee,
                'department'  => $department,
                'position'    => $position,
                'departments' => $this->departmentModel->getActiveDepartments(),
                'positions'   => $this->positionModel->getActivePositions(),
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

        // Check access: HR Admin only
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'hr'])) {
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
    /**
     * Return a single employee as JSON (for modal population)
     */
    public function getEmployee($id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthenticated']);
        }
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'hr'])) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Access denied']);
        }
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Not found']);
        }
        return $this->response->setJSON(['success' => true, 'employee' => $employee]);
    }

    public function update($id)
    {
        $isAjax = $this->request->hasHeader('X-Requested-With') &&
                  strtolower($this->request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';

        // Check if POST request
        if (!$this->request->is('post')) {
            return redirect()->to('/employee');
        }

        // Check access: HR Admin only
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'hr'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            if ($isAjax) return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Employee not found']);
            return redirect()->to('/employee')->with('error', 'Employee not found');
        }

        $rules = [
            'first_name'     => ['label' => 'First Name',     'rules' => 'required|min_length[2]|max_length[100]'],
            'last_name'      => ['label' => 'Last Name',      'rules' => 'required|min_length[2]|max_length[100]'],
            'email'          => ['label' => 'Email',          'rules' => 'required|valid_email|is_unique[employees.email,id,' . $id . ']'],
            'phone'          => ['label' => 'Phone',          'rules' => 'permit_empty|max_length[20]'],
            'department_id'  => ['label' => 'Department',     'rules' => 'permit_empty|integer'],
            'position_id'    => ['label' => 'Position',       'rules' => 'permit_empty|integer'],
            'date_of_birth'  => ['label' => 'Date of Birth',  'rules' => 'permit_empty|valid_date'],
            'date_of_joining'=> ['label' => 'Date of Joining','rules' => 'required|valid_date'],
            'status'         => ['label' => 'Status',         'rules' => 'permit_empty|in_list[active,inactive,suspended]'],
        ];

        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success'   => false,
                    'errors'    => $this->validator->getErrors(),
                    'csrf_hash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Manual special-character checks (regex_match in CI4 rule strings
        // breaks when the pattern itself contains [ ] characters)
        $specialCharErrors = [];
        $firstName = trim($this->request->getPost('first_name'));
        $lastName  = trim($this->request->getPost('last_name'));
        $phone     = trim($this->request->getPost('phone') ?? '');

        if (!preg_match("/^[A-Za-z\s\-']+$/u", $firstName)) {
            $specialCharErrors['first_name'] = 'First name must contain only letters, spaces, hyphens or apostrophes — no special characters.';
        }
        if (!preg_match("/^[A-Za-z\s\-']+$/u", $lastName)) {
            $specialCharErrors['last_name'] = 'Last name must contain only letters, spaces, hyphens or apostrophes — no special characters.';
        }
        if ($phone !== '' && !preg_match('/^[0-9\s\+\-\(\)]+$/u', $phone)) {
            $specialCharErrors['phone'] = 'Phone must contain only digits, spaces, +, -, or parentheses — no special characters.';
        }

        if (!empty($specialCharErrors)) {
            if ($isAjax) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success'   => false,
                    'errors'    => $specialCharErrors,
                    'csrf_hash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $specialCharErrors);
        }

        // Birthdate validation: must not be 2026 or later, and employee must be at least 18 years old
        $dateOfBirth = trim($this->request->getPost('date_of_birth') ?? '');
        if ($dateOfBirth !== '') {
            $dob = \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                $dobErrors = ['date_of_birth' => 'Please enter a valid date of birth (YYYY-MM-DD).'];
                if ($isAjax) return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $dobErrors, 'csrf_hash' => csrf_hash()]);
                return redirect()->back()->withInput()->with('errors', $dobErrors);
            }
            $today = new \DateTime();
            $age   = $today->diff($dob)->y;
            if ((int)$dob->format('Y') >= 2026) {
                $dobErrors = ['date_of_birth' => 'Date of birth cannot be in the year 2026 or later.'];
                if ($isAjax) {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $dobErrors, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('errors', $dobErrors);
            }
            if ($age < 18) {
                $dobErrors = ['date_of_birth' => 'Employee must be at least 18 years old.'];
                if ($isAjax) {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $dobErrors, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('errors', $dobErrors);
            }
        }

        $data = [
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'email'           => trim($this->request->getPost('email')),
            'phone'           => $phone ?: null,
            'department_id'   => $this->request->getPost('department_id') ?: null,
            'position_id'     => $this->request->getPost('position_id') ?: null,
            'date_of_birth'   => $dateOfBirth ?: null,
            'date_of_joining' => $this->request->getPost('date_of_joining'),
            'status'          => $this->request->getPost('status') ?? 'active',
        ];

        try {
            $this->employeeModel->skipValidation(true)->update($id, $data);

            if ($isAjax) {
                return $this->response->setJSON(['success' => true, 'message' => 'Employee updated successfully', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->to('/employee')->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            log_message('error', 'Employee update failed: ' . $e->getMessage());
            if ($isAjax) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'An error occurred while updating the employee', 'csrf_hash' => csrf_hash()]);
            }
            return redirect()->back()->with('error', 'An error occurred while updating the employee');
        }
    }

    /**
     * Re-apply a rejected employee for approval.
     * Updates employee details and resets account_status to 'pending',
     * then notifies all Super Admins.
     */
    public function reApply($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/employee');
        }

        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'hr'])) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Employee not found.', 'csrf_hash' => csrf_hash()]);
        }

        // Basic validation
        $rules = [
            'first_name'      => ['label' => 'First Name',     'rules' => 'required|min_length[2]|max_length[100]'],
            'last_name'       => ['label' => 'Last Name',      'rules' => 'required|min_length[2]|max_length[100]'],
            'email'           => ['label' => 'Email',          'rules' => 'required|valid_email|is_unique[employees.email,id,' . $id . ']'],
            'phone'           => ['label' => 'Phone',          'rules' => 'permit_empty|max_length[20]'],
            'department_id'   => ['label' => 'Department',     'rules' => 'permit_empty|integer'],
            'position_id'     => ['label' => 'Position',       'rules' => 'permit_empty|integer'],
            'date_of_birth'   => ['label' => 'Date of Birth',  'rules' => 'permit_empty|valid_date'],
            'date_of_joining' => ['label' => 'Date of Joining','rules' => 'required|valid_date'],
            'status'          => ['label' => 'Status',         'rules' => 'permit_empty|in_list[active,inactive,suspended]'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'   => false,
                'errors'    => $this->validator->getErrors(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Special-character checks
        $specialErrors = [];
        $firstName = trim($this->request->getPost('first_name'));
        $lastName  = trim($this->request->getPost('last_name'));
        $phone     = trim($this->request->getPost('phone') ?? '');

        if (!preg_match("/^[A-Za-z\s\-']+$/u", $firstName)) {
            $specialErrors['first_name'] = 'First name must contain only letters, spaces, hyphens or apostrophes.';
        }
        if (!preg_match("/^[A-Za-z\s\-']+$/u", $lastName)) {
            $specialErrors['last_name'] = 'Last name must contain only letters, spaces, hyphens or apostrophes.';
        }
        if ($phone !== '' && !preg_match('/^[0-9\s\+\-\(\)]+$/u', $phone)) {
            $specialErrors['phone'] = 'Phone must contain only digits, spaces, +, -, or parentheses.';
        }
        if (!empty($specialErrors)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $specialErrors, 'csrf_hash' => csrf_hash()]);
        }

        // Birthdate validation: must not be 2026 or later, and employee must be at least 18 years old
        $dateOfBirth = trim($this->request->getPost('date_of_birth') ?? '');
        if ($dateOfBirth !== '') {
            $dob = \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['date_of_birth' => 'Please enter a valid date of birth (YYYY-MM-DD).'], 'csrf_hash' => csrf_hash()]);
            }
            $today = new \DateTime();
            $age   = $today->diff($dob)->y;
            if ((int)$dob->format('Y') >= 2026) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['date_of_birth' => 'Date of birth cannot be in the year 2026 or later.'], 'csrf_hash' => csrf_hash()]);
            }
            if ($age < 18) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['date_of_birth' => 'Employee must be at least 18 years old.'], 'csrf_hash' => csrf_hash()]);
            }
        }

        $data = [
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'email'           => trim($this->request->getPost('email')),
            'phone'           => $phone ?: null,
            'department_id'   => $this->request->getPost('department_id') ?: null,
            'position_id'     => $this->request->getPost('position_id') ?: null,
            'date_of_birth'   => $dateOfBirth ?: null,
            'date_of_joining' => $this->request->getPost('date_of_joining'),
            'status'          => $this->request->getPost('status') ?? 'active',
            'account_status'  => 'pending',
            'approval_notes'  => null,
        ];

        try {
            $this->employeeModel->skipValidation(true)->update($id, $data);

            // Notify all Super Admins
            $db = \Config\Database::connect();
            $superAdminRole = $db->table('roles')->where('name', 'Super Admin')->get()->getRow();
            if ($superAdminRole) {
                $superAdmins = $this->userModel->where('role_id', $superAdminRole->id)->where('is_active', 1)->findAll();
                foreach ($superAdmins as $sa) {
                    $this->notificationModel->insert([
                        'user_id' => $sa->id,
                        'title'   => 'Re-application for Approval',
                        'message' => "{$firstName} {$lastName} has re-applied after rejection and is awaiting your review.",
                        'type'    => 'warning',
                        'icon'    => 'fas fa-redo',
                        'link'    => site_url('employee/review/' . $id),
                        'is_read' => false,
                    ]);
                }
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Re-application submitted successfully.',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'reApply failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'An error occurred. Please try again.', 'csrf_hash' => csrf_hash()]);
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

        // Check access: Super Admin can delete anyone; HR Admin can only delete rejected employees
        $role = session()->get('role_name') ?? session()->get('role');
        $isSuperAdmin = in_array($role, ['Super Admin', 'admin']);
        $isHRAdmin    = in_array($role, ['HR Admin', 'hr']);

        if (!$isSuperAdmin && !$isHRAdmin) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Employee not found'
            ]);
        }

        // HR Admin may only delete employees whose account was rejected
        if ($isHRAdmin && !$isSuperAdmin && ($employee->account_status ?? '') !== 'rejected') {
            return redirect()->to('/employee')->with('error', 'HR Admin can only delete rejected employee records.');
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
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }

        try {
            $employee = $this->employeeModel->find($employeeId);
            if (!$employee) {
                return redirect()->to('/employee')->with('error', 'Employee not found.');
            }

            // Mark the notification as read (first notification linking here for this user)
            $userId = session()->get('user_id');
            if ($userId) {
                $db = \Config\Database::connect();
                $db->table('notifications')
                    ->where('user_id', $userId)
                    ->like('link', 'employee/review/' . $employeeId, 'both')
                    ->where('is_read', 0)
                    ->update(['is_read' => 1]);
            }

            // Load related information
            $department = $employee->department_id ? $this->departmentModel->find($employee->department_id) : null;
            $position   = $employee->position_id   ? $this->positionModel->find($employee->position_id)   : null;

            return view('employee/review', [
                'employee'   => $employee,
                'department' => $department,
                'position'   => $position,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to review employee: ' . $e->getMessage());
            return redirect()->to('/employee')->with('error', 'Error retrieving employee details.');
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

            // Get Employee role (employees get the 'Employee' role, not HR Admin)
            $db = \Config\Database::connect();
            $employeeRole = $db->table('roles')->where('name', 'Employee')->get()->getRow();
            // Fallback to any role with id=4, or the lowest-privilege role available
            $roleId = $employeeRole ? $employeeRole->id : 4;

            // Get HR Admin role (for notification purposes)
            $hrAdminRole = $db->table('roles')->where('name', 'HR Admin')->get()->getRow();

            // Create user account
            $userData = [
                'name'      => $employee->first_name . ' ' . $employee->last_name,
                'email'     => $employee->email,
                'password'  => $password,
                'role_id'   => $roleId,
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
            if ($hrAdminRole) {
                $hrAdmins = $this->userModel
                    ->where('role_id', $hrAdminRole->id)
                    ->where('is_active', 1)
                    ->findAll();

                foreach ($hrAdmins as $hrAdmin) {
                    $this->notificationModel->insert([
                        'user_id' => $hrAdmin->id,
                        'title'   => 'Employee Account Approved',
                        'message' => "The employee account for '{$employee->first_name} {$employee->last_name}' has been approved by Super Admin. Account credentials have been sent to their email.",
                        'type'    => 'success',
                        'icon'    => 'fas fa-check-circle',
                        'link'    => site_url('employee/show/' . $employeeId),
                        'is_read' => false,
                    ]);
                }
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Employee account approved! Credentials sent to ' . $employee->email,
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

            // Send rejection email to the employee
            $this->sendRejectionEmail($employee->email, $employee->first_name, $rejectionNotes);

            // Notify HR Admin about rejection
            $db = \Config\Database::connect();
            $hrAdminRole = $db->table('roles')->where('name', 'HR Admin')->get()->getRow();

            if ($hrAdminRole) {
                $hrAdmins = $this->userModel
                    ->where('role_id', $hrAdminRole->id)
                    ->where('is_active', 1)
                    ->findAll();

                foreach ($hrAdmins as $hrAdmin) {
                    $this->notificationModel->insert([
                        'user_id' => $hrAdmin->id,
                        'title'   => 'Employee Account Rejected',
                        'message' => "The employee account for '{$employee->first_name} {$employee->last_name}' (ID: {$employee->employee_id}) has been rejected by Super Admin. Reason: {$rejectionNotes}",
                        'type'    => 'danger',
                        'icon'    => 'fas fa-times-circle',
                        'link'    => site_url('employee/show/' . $employeeId),
                        'is_read' => false,
                    ]);
                }
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Employee application rejected. Rejection email sent to ' . $employee->email . ' and HR Admin notified.',
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
     * Send rejection email to employee
     */
    private function sendRejectionEmail($email, $firstName, $reason)
    {
        try {
            $emailService = \Config\Services::email();
            $emailService->setFrom(env('email.fromEmail'), env('email.fromName', 'PeopleAxis HR System'));
            $emailService->setTo($email);
            $emailService->setSubject('PeopleAxis HR System - Employment Application Update');

            $htmlBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                        .header { background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                        .content { padding: 20px; }
                        .reason-box { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; border-radius: 4px; }
                        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Employment Application Update</h2>
                        </div>
                        <div class='content'>
                            <p>Dear {$firstName},</p>
                            <p>We regret to inform you that your employee account application has been reviewed and unfortunately could not be approved at this time.</p>
                            <div class='reason-box'>
                                <strong>Reason:</strong><br>{$reason}
                            </div>
                            <p>If you believe this is an error or would like further clarification, please contact your HR department.</p>
                            <p>We appreciate your interest and thank you for your patience.</p>
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
            log_message('error', 'Failed to send rejection email: ' . $e->getMessage());
            return false;
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
