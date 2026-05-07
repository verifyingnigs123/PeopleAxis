<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\SalaryModel;
use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\PositionModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Libraries\PhDeductions;
use App\Controllers\Audit;

class Employees extends BaseController
{
    protected $employeeModel;
    protected $salaryModel;
    protected $departmentModel;
    protected $roleModel;
    protected $positionModel;
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->salaryModel = new SalaryModel();
        $this->departmentModel = new DepartmentModel();
        $this->roleModel = new RoleModel();
        $this->positionModel = new PositionModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
    }

    private function resolvePositionId(string $positionName): ?int
    {
        $positionName = trim($positionName);
        if ($positionName === '') {
            return null;
        }

        $position = $this->positionModel->where('name', $positionName)->first();
        if ($position) {
            return (int) $position->id;
        }

        $insertId = $this->positionModel->skipValidation(true)->insert([
            'name'        => $positionName,
            'description' => $positionName,
            'is_active'   => 1,
        ]);

        return $insertId ? (int) $insertId : null;
    }

    private function resolveRoleId(string $roleName): ?int
    {
        $roleName = trim($roleName);
        if ($roleName === '') {
            return null;
        }

        $role = $this->roleModel->where('name', $roleName)->first();
        if ($role) {
            return (int) $role->id;
        }

        return null;
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
            // Fetch employees with their role name joined from users → roles via email match.
            // COALESCE falls back to the role set directly on the employee record (employees.role_id)
            // so that employees pending a user account still show the correct position (e.g. Manager).
            $db = \Config\Database::connect();
            $employeeRows = $db->table('employees')
                ->select('employees.*, COALESCE(user_roles.name, emp_roles.name) AS user_role_name')
                ->join('users',       'users.email = employees.email AND users.deleted_at IS NULL AND users.is_active = 1', 'left')
                ->join('roles AS user_roles', 'user_roles.id = users.role_id AND user_roles.deleted_at IS NULL', 'left')
                ->join('roles AS emp_roles',  'emp_roles.id = employees.role_id AND emp_roles.deleted_at IS NULL', 'left')
                ->orderBy('employees.created_at', 'DESC')
                ->get()->getResult();
            $employees = $employeeRows;

            $departments = $this->departmentModel->getActiveDepartments();
            $roles = $this->roleModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
            
            // Get pending employees for Super Admin approval
            $pendingEmployees = [];
            $isSuperAdmin = $role === 'Super Admin' || $role === 'admin';
            if ($isSuperAdmin) {
                $pendingEmployees = $this->employeeModel->getPendingEmployees();
            }
            
            // Create role lookup array for quick access
            $roleMap = [];
            foreach ($roles as $r) {
                $roleMap[$r->id] = $r->name;
            }
            
            // Create department lookup array for quick access
            $departmentMap = [];
            foreach ($departments as $dept) {
                $departmentMap[$dept->id] = $dept->name;
            }

            // Build a map of user emails → role name from the users/roles tables,
            // restricted to active users only.
            $userRows = $db->table('users')
                ->select('users.email, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.is_active', 1)
                ->where('users.deleted_at IS NULL')
                ->get()
                ->getResultArray();

            // Build sets based on role:
            //   $adminOnlyEmailSet  → Super Admin / HR Admin accounts (excluded from HR view)
            //   $userEmailSet       → Employee and Manager-role users (shown as Active badge)
            $adminOnlyEmailSet = [];  // email → true  (Super Admin, HR Admin)
            $userEmailSet      = [];  // email → true  (Employee, Manager — shown as Active)
            foreach ($userRows as $usr) {
                $rn = strtolower($usr['role_name']);
                if (in_array($rn, ['super admin', 'hr admin'])) {
                    $adminOnlyEmailSet[$usr['email']] = true;
                } else {
                    // Employee, Manager, or any other non-admin role
                    $userEmailSet[$usr['email']] = true;
                }
            }

            // Build a set of emails whose user accounts have been soft-deleted by
            // Super Admin (is_active = 0).  These employees should be hidden from
            // the HR Admin table; they reappear automatically once the account is
            // restored (is_active restored to 1).
            $deletedAccountRows = $db->table('users')
                ->select('email')
                ->where('is_active', 0)
                ->get()
                ->getResultArray();
            $deletedAccountEmailSet = [];
            foreach ($deletedAccountRows as $du) {
                $deletedAccountEmailSet[$du['email']] = true;
            }

            // Exclude Super Admin / HR Admin accounts AND employees whose user
            // account has been deleted.  Employees with no account at all are
            // still shown (they were never deleted, just not registered yet).
            $employees = array_filter($employees, function ($emp) use ($adminOnlyEmailSet, $deletedAccountEmailSet) {
                // Never show Super Admin / HR Admin accounts in the HR view.
                if (isset($adminOnlyEmailSet[$emp->email])) {
                    return false;
                }
                // Always show rejected employees so HR Admin can see the count,
                // resubmit, or delete them — even if a deactivated user account
                // happens to share the same email address.
                if (strtolower((string)($emp->account_status ?? '')) === 'rejected') {
                    return true;
                }
                return !isset($deletedAccountEmailSet[$emp->email]);
            });
            $employees = array_values($employees);

            // Sync account_status in DB for each visible employee so it stays accurate.
            // IMPORTANT: never auto-promote a pending employee to active based only on
            // user-account existence. Pending must remain pending until Super Admin
            // explicitly approves.
            // For legacy rows without account_status, infer approved if an active
            // user account exists, otherwise pending.
            // Also reset status=inactive for any pending employee that somehow has status=active
            // (e.g. records created before the inactive-by-default rule was enforced).
            $toApproved = [];
            $toPending  = [];
            $toInactive = [];
            foreach ($employees as $emp) {
                $current = strtolower((string) ($emp->account_status ?? ''));
                if (in_array($current, ['rejected', 'approved'])) {
                    continue;
                }

                // Preserve explicit pending status until Super Admin action.
                if ($current === 'pending') {
                    $newAccountStatus = 'pending';
                } else {
                    $newAccountStatus = isset($userEmailSet[$emp->email]) ? 'approved' : 'pending';
                }

                if ($newAccountStatus === 'pending' && ($emp->status ?? 'inactive') !== 'inactive') {
                    $toInactive[] = $emp->id;
                    $emp->status = 'inactive';
                }

                $emp->account_status = $newAccountStatus;
            }
            if (!empty($toApproved)) {
                $db->table('employees')->whereIn('id', $toApproved)->update(['account_status' => 'approved']);
            }
            if (!empty($toPending)) {
                $db->table('employees')->whereIn('id', $toPending)->update(['account_status' => 'pending']);
            }
            if (!empty($toInactive)) {
                $db->table('employees')->whereIn('id', $toInactive)->update(['status' => 'inactive']);
            }

        } catch (\Exception $e) {
            log_message('error', 'Failed to load employees: ' . $e->getMessage());
            $employees = [];
            $departments = [];
            $roles = [];
            $roleMap = [];
            $departmentMap = [];
            $pendingEmployees = [];
            $userEmailSet = [];
        }
        $data = [
            'employees'     => $employees,
            'departments'   => $departments,
            'roles'         => $roles ?? [],
            'positions'     => $roles ?? [],
            'positionMap'   => $roleMap ?? [],
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
            $roles       = $this->roleModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();

            $roleMap = [];
            foreach ($roles as $r) { $roleMap[$r->id] = $r->name; }
            $departmentMap = [];
            foreach ($departments as $d) { $departmentMap[$d->id] = $d->name; }

        } catch (\Exception $e) {
            log_message('error', 'pendingApprovals error: ' . $e->getMessage());
            $pending  = [];
            $rejected = [];
            $roleMap       = [];
            $departmentMap = [];
        }

        return view('employee/pending_approvals', [
            'pending'      => $pending,
            'rejected'     => $rejected,
            'positionMap'  => $roleMap,
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
            $roles = $this->roleModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to load departments/roles: ' . $e->getMessage());
            $departments = [];
            $roles = [];
        }

        $data = [
            'departments' => $departments,
            'roles'       => $roles,
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
            'first_name'      => 'required|min_length[2]',
            'last_name'       => 'required|min_length[2]',
            'email'           => 'required|valid_email|is_unique[employees.email]',
            'phone'           => 'permit_empty|max_length[20]',
            'rfid_number'     => 'required|max_length[100]|is_unique[employees.rfid_number]',
            'department_id'   => 'permit_empty|numeric',
            'employee_type'   => 'required|in_list[Manager,Employee]',
            'date_of_birth'   => 'required|valid_date',
            'date_of_joining' => 'required|valid_date',
            'status'          => 'required|in_list[active,inactive,suspended]',
            'employment_type' => 'permit_empty|in_list[full_time,part_time,contractual,probationary]',
            'rate'            => 'permit_empty|decimal',
            'rate_type'       => 'permit_empty|in_list[hourly,daily,monthly]',
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
                $err = ['date_of_birth' => 'Employees below 18 years old are not allowed.'];
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $err]);
                }
                return redirect()->back()->withInput()->with('errors', $err);
            }
            if ($age < 18) {
                $err = ['date_of_birth' => 'Employees below 18 years old are not allowed.'];
                if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $err]);
                }
                return redirect()->back()->withInput()->with('errors', $err);
            }
        }

        $phone = trim($this->request->getPost('phone') ?? '');
        if ($phone !== '') {
            $digitsOnly = preg_replace('/\D/', '', $phone);
            if (!preg_match('/^09\d{9}$/', $digitsOnly) && !preg_match('/^639\d{9}$/', $digitsOnly)) {
                $err = ['phone' => 'Phone must be a valid Philippine number: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars).'];
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

        $dateHired = trim($this->request->getPost('date_of_joining') ?? '');
        $rfidNumber = trim($this->request->getPost('rfid_number') ?? '');
        $departmentId = (int) ($this->request->getPost('department_id') ?? 0);
        $employeeType = trim((string) $this->request->getPost('employee_type'));
        $roleId = $this->resolveRoleId($employeeType);
        $data = [
            'employee_id'     => $employeeId,
            'first_name'      => trim($this->request->getPost('first_name')),
            'last_name'       => trim($this->request->getPost('last_name')),
            'email'           => trim($this->request->getPost('email')),
            'phone'           => $phone ?: null,
            'rfid_number'     => $rfidNumber,
            'department_id'   => $departmentId > 0 ? $departmentId : null,
            'role_id'         => $roleId,
            'date_of_birth'   => $dateOfBirth,
            'date_of_joining' => $dateHired,
            'date_hired'      => $dateHired,
            'status'          => $this->request->getPost('status') ?? 'active',
            'account_status'  => 'pending',
            'employment_type' => $this->request->getPost('employment_type') ?: null,
            'rate'            => $this->request->getPost('rate') ?: null,
            'rate_type'       => $this->request->getPost('rate_type') ?: null,
        ];

        try {
            if ($this->employeeModel->insert($data)) {
                $newEmployeeId = $this->employeeModel->getInsertID();
                $firstName = $this->request->getPost('first_name');
                $lastName = $this->request->getPost('last_name');
                $actorName = session()->get('name') ?? session()->get('username') ?? 'HR Admin';
                $actorRole = session()->get('role_name') ?? session()->get('role') ?? 'User';
                
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
                    $title = 'New Employee Awaiting Approval';
                    $message = "A new employee '{$firstName} {$lastName}' (ID: {$employeeId}) has been added and is waiting for account creation and approval.";

                    if (in_array($actorRole, ['HR Admin', 'hr'])) {
                        $title = 'New Employee Added by HR Admin';
                        $message = "HR Admin {$actorName} added '{$firstName} {$lastName}' (ID: {$employeeId}) and submitted for your approval.";
                    }

                    $this->notificationModel->insert([
                        'user_id' => $superAdmin->id,
                        'role' => 'Super Admin',
                        'title' => $title,
                        'message' => $message,
                        'status' => 'unread',
                        'type' => 'warning',
                        'icon' => 'fas fa-user-check',
                        'link' => site_url('employee/review/' . $newEmployeeId),
                        'is_read' => false,
                    ]);
                }

                // Auto-create salary record from the rate/rate_type if provided
                $rate     = (float) ($this->request->getPost('rate') ?? 0);
                $rateType = $this->request->getPost('rate_type') ?? 'monthly';
                if ($rate > 0) {
                    if ($rateType === 'hourly')    { $baseSalary = $rate * 8 * 26; }
                    elseif ($rateType === 'daily') { $baseSalary = $rate * 26; }
                    else                           { $baseSalary = $rate; }
                    $ded = PhDeductions::compute($baseSalary, 0);
                    $this->salaryModel->skipValidation(true)->insert([
                        'employee_id'             => $newEmployeeId,
                        'base_salary'             => $baseSalary,
                        'allowances'              => 0,
                        'sss_contribution'        => $ded['sss'],
                        'philhealth_contribution' => $ded['philhealth'],
                        'pagibig_contribution'    => $ded['pagibig'],
                        'withholding_tax'         => $ded['withholding_tax'],
                        'deductions'              => 0,
                        'net_salary'              => $ded['net_salary'],
                        'effective_from'          => date('Y-m-d'),
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

        // Check access: HR Admin and Super Admin see all details including salary.
        // Manager can view basic employee info but salary is hidden in the view.
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin', 'Manager', 'manager'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            $employee = $this->employeeModel->find($id);
            if (!$employee) {
                return redirect()->to('/employee')->with('error', 'Employee not found');
            }

            // Load related department
            $department = null;
            if ($employee->department_id) {
                $department = $this->departmentModel->find($employee->department_id);
            }

            // Resolve position the same way as the employee index:
            // employees → users (by email) → roles
            $db = \Config\Database::connect();
            $positionRecord = null;
            $userRow = $db->table('users')
                ->select('roles.id AS role_id, roles.name AS role_name')
                ->join('roles', 'roles.id = users.role_id AND roles.deleted_at IS NULL', 'left')
                ->where('users.email', $employee->email)
                ->where('users.deleted_at IS NULL')
                ->where('users.is_active', 1)
                ->get()->getRow();
            if ($userRow && $userRow->role_name) {
                // Build a simple object so the view can use $position->name
                $positionRecord = (object)['id' => $userRow->role_id, 'name' => $userRow->role_name];
            }

            // Load salary information for this employee; auto-create/recompute statutory if needed
            $salary  = $this->salaryModel->getEmployeeSalary($employee->id);
            $empRate = (float)($employee->rate ?? 0);
            if ($empRate > 0) {
                $rt = $employee->rate_type ?? 'monthly';
                if ($rt === 'hourly')    { $base = $empRate * 8 * 26; }
                elseif ($rt === 'daily') { $base = $empRate * 26; }
                else                     { $base = $empRate; }
                if (!$salary) {
                    $ded = PhDeductions::compute($base, 0);
                    $this->salaryModel->skipValidation(true)->insert([
                        'employee_id'             => $employee->id,
                        'base_salary'             => $base,
                        'allowances'              => 0,
                        'sss_contribution'        => $ded['sss'],
                        'philhealth_contribution' => $ded['philhealth'],
                        'pagibig_contribution'    => $ded['pagibig'],
                        'withholding_tax'         => $ded['withholding_tax'],
                        'deductions'              => 0,
                        'net_salary'              => $ded['net_salary'],
                        'effective_from'          => date('Y-m-d'),
                    ]);
                    $salary = $this->salaryModel->getEmployeeSalary($employee->id);
                } elseif ((float)($salary->sss_contribution ?? 0) == 0) {
                    // Backfill statutory columns for legacy rows
                    $allowances = (float)($salary->allowances ?? 0);
                    $extraDed   = (float)($salary->deductions ?? 0);
                    $ded = PhDeductions::compute((float)$salary->base_salary, $allowances);
                    $this->salaryModel->update($salary->id, [
                        'sss_contribution'        => $ded['sss'],
                        'philhealth_contribution' => $ded['philhealth'],
                        'pagibig_contribution'    => $ded['pagibig'],
                        'withholding_tax'         => $ded['withholding_tax'],
                        'net_salary'              => $ded['net_salary'] - $extraDed,
                    ]);
                    $salary = $this->salaryModel->getEmployeeSalary($employee->id);
                }
            }

            $data = [
                'employee'    => $employee,
                'department'  => $department,
                'position'    => $positionRecord,
                'departments' => $this->departmentModel->getActiveDepartments(),
                'roles'       => $this->roleModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll(),
                'positions'   => $this->roleModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll(),
                'salary'      => $salary,
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
            $roles = $this->roleModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();

            $data = [
                'employee'    => $employee,
                'departments' => $departments,
                'roles'       => $roles,
                'positions'   => $roles,
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
            'rfid_number'    => ['label' => 'RFID Number',    'rules' => 'required|max_length[100]|is_unique[employees.rfid_number,id,' . $id . ']'],
            'phone'          => ['label' => 'Phone',          'rules' => 'permit_empty|max_length[20]'],
            'department_id'  => ['label' => 'Department',     'rules' => 'permit_empty|integer'],
            'role_id'        => ['label' => 'Role',            'rules' => 'permit_empty|integer'],
            'date_of_birth'  => ['label' => 'Date of Birth',  'rules' => 'permit_empty|valid_date'],
            'date_of_joining'=> ['label' => 'Date Hired',      'rules' => 'required|valid_date'],
            'status'         => ['label' => 'Status',         'rules' => 'permit_empty|in_list[active,inactive,suspended]'],
            'rate'           => ['label' => 'Salary Rate',    'rules' => 'permit_empty'],
            'rate_type'      => ['label' => 'Rate Type',      'rules' => 'permit_empty|in_list[hourly,daily,monthly]'],
            'employment_type'=> ['label' => 'Employment Type','rules' => 'permit_empty|in_list[full_time,part_time,contractual,probationary]'],
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
        // Philippine phone validation: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars)
        if ($phone !== '') {
            $digitsOnly = preg_replace('/\D/', '', $phone);
            // Valid: 09 followed by 9 digits, OR 639 followed by 9 digits (from +639...)
            if (!preg_match('/^09\d{9}$/', $digitsOnly) && !preg_match('/^639\d{9}$/', $digitsOnly)) {
                $specialCharErrors['phone'] = 'Phone must be a valid Philippine number: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars).';
            }
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
                $dobErrors = ['date_of_birth' => 'Employees below 18 years old are not allowed.'];
                if ($isAjax) {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $dobErrors, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('errors', $dobErrors);
            }
            if ($age < 18) {
                $dobErrors = ['date_of_birth' => 'Employees below 18 years old are not allowed.'];
                if ($isAjax) {
                    return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $dobErrors, 'csrf_hash' => csrf_hash()]);
                }
                return redirect()->back()->withInput()->with('errors', $dobErrors);
            }
        }

        $dateHiredUpd = trim($this->request->getPost('date_of_joining') ?? '');
        $rfidNumber   = trim($this->request->getPost('rfid_number') ?? '');
        $data = [
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'email'           => trim($this->request->getPost('email')),
            'rfid_number'     => $rfidNumber,
            'phone'           => $phone ?: null,
            'department_id'   => $this->request->getPost('department_id') ?: null,
            'role_id'         => $this->request->getPost('role_id') ?: null,
            'date_of_birth'   => $dateOfBirth ?: null,
            'date_of_joining' => $dateHiredUpd ?: null,
            'date_hired'      => $dateHiredUpd ?: null,
            'status'          => $this->request->getPost('status') ?? 'active',
            'rate'            => $this->request->getPost('rate') ?: null,
            'rate_type'       => $this->request->getPost('rate_type') ?: null,
            'employment_type' => $this->request->getPost('employment_type') ?: null,
        ];

        try {
            $this->employeeModel->skipValidation(true)->update($id, $data);

            // Auto-upsert salary record when rate is provided
            $rate     = (float) ($this->request->getPost('rate') ?? 0);
            $rateType = $this->request->getPost('rate_type') ?? 'monthly';
            if ($rate > 0) {
                if ($rateType === 'hourly')    { $baseSalary = $rate * 8 * 26; }
                elseif ($rateType === 'daily') { $baseSalary = $rate * 26; }
                else                           { $baseSalary = $rate; }
                $existing   = $this->salaryModel->getEmployeeSalary($id);
                $allowances = $existing ? (float)($existing->allowances ?? 0) : 0;
                $extraDed   = $existing ? (float)($existing->deductions ?? 0) : 0;
                $ded = PhDeductions::compute($baseSalary, $allowances);
                if ($existing) {
                    $this->salaryModel->update($existing->id, [
                        'base_salary'             => $baseSalary,
                        'sss_contribution'        => $ded['sss'],
                        'philhealth_contribution' => $ded['philhealth'],
                        'pagibig_contribution'    => $ded['pagibig'],
                        'withholding_tax'         => $ded['withholding_tax'],
                        'net_salary'              => $ded['net_salary'] - $extraDed,
                    ]);
                } else {
                    $this->salaryModel->skipValidation(true)->insert([
                        'employee_id'             => $id,
                        'base_salary'             => $baseSalary,
                        'allowances'              => 0,
                        'sss_contribution'        => $ded['sss'],
                        'philhealth_contribution' => $ded['philhealth'],
                        'pagibig_contribution'    => $ded['pagibig'],
                        'withholding_tax'         => $ded['withholding_tax'],
                        'deductions'              => 0,
                        'net_salary'              => $ded['net_salary'],
                        'effective_from'          => date('Y-m-d'),
                    ]);
                }
            }

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
            'rfid_number'     => ['label' => 'RFID Number',    'rules' => 'required|max_length[100]|is_unique[employees.rfid_number,id,' . $id . ']'],
            'phone'           => ['label' => 'Phone',          'rules' => 'permit_empty|max_length[20]'],
            'department_id'   => ['label' => 'Department',     'rules' => 'permit_empty|integer'],
            'role_id'         => ['label' => 'Role',            'rules' => 'permit_empty|integer'],
            'date_of_birth'   => ['label' => 'Date of Birth',  'rules' => 'permit_empty|valid_date'],
            'date_of_joining' => ['label' => 'Date Hired',     'rules' => 'required|valid_date'],
            'status'          => ['label' => 'Status',         'rules' => 'permit_empty|in_list[active,inactive,suspended]'],
            'rate'            => ['label' => 'Salary Rate',    'rules' => 'permit_empty'],
            'rate_type'       => ['label' => 'Rate Type',      'rules' => 'permit_empty|in_list[hourly,daily,monthly]'],
            'employment_type' => ['label' => 'Employment Type','rules' => 'permit_empty|in_list[full_time,part_time,contractual,probationary]'],
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
        // Philippine phone validation: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars)
        if ($phone !== '') {
            $digitsOnly = preg_replace('/\D/', '', $phone);
            // Valid: 09 followed by 9 digits, OR 639 followed by 9 digits (from +639...)
            if (!preg_match('/^09\d{9}$/', $digitsOnly) && !preg_match('/^639\d{9}$/', $digitsOnly)) {
                $specialErrors['phone'] = 'Phone must be a valid Philippine number: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 chars).';
            }
        }
        if (!empty($specialErrors)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => $specialErrors, 'csrf_hash' => csrf_hash()]);
        }

        // Birthdate validation: employee must be at least 18 years old
        $dateOfBirth = trim($this->request->getPost('date_of_birth') ?? '');
        if ($dateOfBirth !== '') {
            $dob = \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
            if (!$dob || $dob->format('Y-m-d') !== $dateOfBirth) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['date_of_birth' => 'Please enter a valid date of birth (YYYY-MM-DD).'], 'csrf_hash' => csrf_hash()]);
            }
            $today = new \DateTime();
            $age   = $today->diff($dob)->y;
            if ($age < 18) {
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['date_of_birth' => 'Employees below 18 years old are not allowed.'], 'csrf_hash' => csrf_hash()]);
            }
        }

        $dateHiredRA = trim($this->request->getPost('date_of_joining') ?? '');
        $rfidNumber  = trim($this->request->getPost('rfid_number') ?? '');
        $data = [
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'email'           => trim($this->request->getPost('email')),
            'rfid_number'     => $rfidNumber,
            'phone'           => $phone ?: null,
            'department_id'   => $this->request->getPost('department_id') ?: null,
            'role_id'         => $this->request->getPost('role_id') ?: null,
            'date_of_birth'   => $dateOfBirth ?: null,
            'date_of_joining' => $dateHiredRA ?: null,
            'date_hired'      => $dateHiredRA ?: null,
            'status'          => $this->request->getPost('status') ?? 'active',
            'account_status'  => 'pending',
            'approval_notes'  => null,
            'rate'            => $this->request->getPost('rate') ?: null,
            'rate_type'       => $this->request->getPost('rate_type') ?: null,
            'employment_type' => $this->request->getPost('employment_type') ?: null,
        ];

        try {
            $this->employeeModel->skipValidation(true)->update($id, $data);
            $actorName = session()->get('name') ?? session()->get('username') ?? 'HR Admin';

            // Notify all Super Admins
            $db = \Config\Database::connect();
            $superAdminRole = $db->table('roles')->where('name', 'Super Admin')->get()->getRow();
            if ($superAdminRole) {
                $superAdmins = $this->userModel->where('role_id', $superAdminRole->id)->where('is_active', 1)->findAll();
                foreach ($superAdmins as $sa) {
                    $this->notificationModel->insert([
                        'user_id' => $sa->id,
                        'role'   => 'Super Admin',
                        'title'   => 'Re-application for Approval',
                        'message' => "HR Admin {$actorName} re-submitted {$firstName} {$lastName} after rejection and is awaiting your review.",
                        'status'  => 'unread',
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

        // Check access: Super Admin and HR Admin can delete any employee record
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

        try {
            // Do NOT hard-delete the employee record — doing so would permanently destroy
            // the employee_id and all data, making restoration impossible.
            // Instead, only soft-delete the linked user account (is_active = 0).
            // The $deletedAccountEmailSet filter in Employees::index() already hides
            // employees whose user account is deactivated, and reveals them automatically
            // when the account is restored — preserving the employee_id throughout.
            $userDeactivated = false;
            if (!empty($employee->email)) {
                $db = \Config\Database::connect();
                $linkedUserRows = $db->table('users')
                    ->select('id')
                    ->where('email', $employee->email)
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                $linkedUserIds = array_map('intval', array_column($linkedUserRows, 'id'));

                $db->table('users')
                    ->where('email', $employee->email)
                    ->where('is_active', 1)
                    ->update([
                        'is_active'  => 0,
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);
                $userDeactivated = ($db->affectedRows() > 0);

                if ($userDeactivated) {
                    foreach ($linkedUserIds as $linkedUserId) {
                        $this->invalidateUserSessions($linkedUserId);
                    }
                }
            }

            // For employees that have no linked user account (e.g. rejected applications),
            // fall back to actually deleting the employee record since there is nothing to restore.
            if (!$userDeactivated) {
                $this->employeeModel->delete($id);
                return redirect()->to('/employee')->with('success', 'Employee record permanently deleted.');
            }

            return redirect()->to('/users')->with('success', 'Employee account deactivated. Their record is preserved and can be fully restored from Manage Users.');
        } catch (\Exception $e) {
            log_message('error', 'Employee deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete employee');
        }
    }

    /**
     * HR Admin requests deletion — notifies all Super Admins, does NOT delete
     */
    public function requestDelete($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/employee');
        }

        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'hr'])) {
            return redirect()->to('/employee')->with('error', 'Access denied.');
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/employee')->with('error', 'Employee not found.');
        }

        $hrName  = session()->get('name') ?? session()->get('username') ?? 'HR Admin';
        $empName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));
        $empId   = $employee->employee_id ?? "ID:{$id}";

        $db = \Config\Database::connect();
        $superAdminRole = $db->table('roles')->where('name', 'Super Admin')->get()->getRow();

        if ($superAdminRole) {
            $superAdmins = $this->userModel
                ->where('role_id', $superAdminRole->id)
                ->where('is_active', 1)
                ->findAll();

            foreach ($superAdmins as $superAdmin) {
                $this->notificationModel->insert([
                    'user_id' => $superAdmin->id,
                    'role'    => 'Super Admin',
                    'title'   => 'Delete Request: ' . $empName,
                    'message' => "HR Admin {$hrName} has requested to delete employee \"{$empName}\" ({$empId}). Click to review and confirm the deletion.",
                    'status'  => 'unread',
                    'type'    => 'danger',
                    'icon'    => 'fas fa-user-times',
                    'link'    => site_url('employee/confirm-delete/' . $id),
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->to('/employee')->with('success', "Deletion request for \"{$empName}\" has been sent to Super Admin for approval.");
    }

    /**
     * Super Admin confirmation page before deleting an employee requested by HR Admin
     */
    public function confirmDelete($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Super Admin can confirm deletions.');
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/employee')->with('error', 'Employee record not found — it may have already been deleted.');
        }

        $departments = $this->departmentModel->findAll();
        $departmentMap = [];
        foreach ($departments as $dept) {
            $departmentMap[$dept->id] = $dept->name;
        }

        return view('employee/confirm_delete', [
            'employee'      => $employee,
            'departmentMap' => $departmentMap,
        ]);
    }

    /**
     * Super Admin rejects the HR Admin's deletion request — notifies all HR Admins, does NOT delete
     */
    public function rejectDelete($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/dashboard');
        }

        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Only Super Admin can reject deletion requests.');
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }

        $superAdminName = session()->get('name') ?? session()->get('username') ?? 'Super Admin';
        $empName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));
        $empId   = $employee->employee_id ?? "ID:{$id}";

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
                    'role'    => 'HR Admin',
                    'title'   => 'Delete Request Rejected: ' . $empName,
                    'message' => "Super Admin {$superAdminName} has rejected the deletion request for employee \"{$empName}\" ({$empId}). The employee record has NOT been removed.",
                    'status'  => 'unread',
                    'type'    => 'warning',
                    'icon'    => 'fas fa-user-shield',
                    'link'    => site_url('employee'),
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->to('/dashboard')->with('success', "Deletion request for \"{$empName}\" has been rejected. HR Admin has been notified.");
    }

    /**
     * Display salary management page
     */
    public function salary()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['HR Admin', 'Super Admin', 'hr', 'admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        try {
            // ── Auto-sync: create salary records for employees who have a rate but no salaries row ──
            $db = \Config\Database::connect();
            $unsynced = $db->table('employees')
                ->select('employees.id AS emp_pk, employees.rate, employees.rate_type')
                ->join('salaries', 'salaries.employee_id = employees.id', 'left')
                ->where('employees.status', 'active')
                ->where('employees.rate >', 0)
                ->where('salaries.id IS NULL', null, false)
                ->get()->getResultObject();

            foreach ($unsynced as $row) {
                $rt = $row->rate_type ?? 'monthly';
                $r  = (float) $row->rate;
                if ($rt === 'hourly')    { $base = $r * 8 * 26; }
                elseif ($rt === 'daily') { $base = $r * 26; }
                else                     { $base = $r; }
                $ded = PhDeductions::compute($base, 0);
                $this->salaryModel->skipValidation(true)->insert([
                    'employee_id'             => $row->emp_pk,
                    'base_salary'             => $base,
                    'allowances'              => 0,
                    'sss_contribution'        => $ded['sss'],
                    'philhealth_contribution' => $ded['philhealth'],
                    'pagibig_contribution'    => $ded['pagibig'],
                    'withholding_tax'         => $ded['withholding_tax'],
                    'deductions'              => 0,
                    'net_salary'              => $ded['net_salary'],
                    'effective_from'          => date('Y-m-d'),
                ]);
            }
            // Backfill statutory columns for existing salary records that predate this feature
            $legacyRows = $this->salaryModel
                ->where('base_salary >', 0)
                ->where('sss_contribution', 0)
                ->findAll();
            foreach ($legacyRows as $lr) {
                $allowances = (float)($lr->allowances ?? 0);
                $extraDed   = (float)($lr->deductions ?? 0);
                $ded = PhDeductions::compute((float)$lr->base_salary, $allowances);
                $this->salaryModel->update($lr->id, [
                    'sss_contribution'        => $ded['sss'],
                    'philhealth_contribution' => $ded['philhealth'],
                    'pagibig_contribution'    => $ded['pagibig'],
                    'withholding_tax'         => $ded['withholding_tax'],
                    'net_salary'              => $ded['net_salary'] - $extraDed,
                ]);
            }
            // ────────────────────────────────────────────────────────────────────────────────────────

            // Load ALL active employees with their salary info (left join so unsalaried employees appear too)
            // Position resolved the same way as employee index: employees → users (by email) → roles
            $queryBuilder = $db->table('employees')
                ->select("employees.id AS employee_pk, employees.employee_id AS emp_code,
                          CONCAT(employees.first_name, ' ', employees.last_name) AS employee_name,
                          employees.status,
                          employees.rate,
                          employees.rate_type,
                          roles.name AS role_name,
                          departments.name AS department_name,
                          salaries.id AS salary_id,
                          salaries.base_salary,
                          salaries.allowances,
                          salaries.deductions,
                          salaries.sss_contribution,
                          salaries.philhealth_contribution,
                          salaries.pagibig_contribution,
                          salaries.withholding_tax,
                          salaries.net_salary,
                          salaries.effective_from")
                ->join('users',        'users.email = employees.email AND users.deleted_at IS NULL AND users.is_active = 1', 'left')
                ->join('roles',        'roles.id = users.role_id AND roles.deleted_at IS NULL', 'left')
                ->join('departments',  'departments.id = employees.department_id', 'left')
                ->join('salaries',     'salaries.employee_id = employees.id', 'left')
                ->where('employees.status', 'active');
            
            // HR Admin only sees Employees and Managers (exclude Super Admin and other HR Admins)
            if (in_array($role, ['HR Admin', 'hr', 'hr_admin'])) {
                $queryBuilder->whereIn('roles.name', ['Employee', 'Manager', 'User', 'employee', 'manager', 'user']);
            }
            
            $employees = $queryBuilder
                ->orderBy('employees.first_name', 'ASC')
                ->get()->getResultObject();
        } catch (\Exception $e) {
            log_message('error', 'Failed to load salary list: ' . $e->getMessage());
            $employees = [];
        }

        $isAdmin = in_array($role, ['Super Admin', 'HR Admin', 'admin', 'hr']);
        $data['employees'] = $employees;
        $data['isAdmin']   = $isAdmin;
        return view('salary/manage', $data);
    }

    /**
     * Return salary data for a single employee as JSON (AJAX)
     */
    public function getSalary($employeeId)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'HR Admin', 'admin', 'hr'])) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $employee = $this->employeeModel->find($employeeId);
        $salary = $this->salaryModel->getEmployeeSalary($employeeId);
        return $this->response->setJSON([
            'success' => true,
            'data'    => $salary ? [
                'base_salary'             => (float) $salary->base_salary,
                'allowances'              => (float) ($salary->allowances              ?? 0),
                'sss_contribution'        => (float) ($salary->sss_contribution        ?? 0),
                'philhealth_contribution' => (float) ($salary->philhealth_contribution ?? 0),
                'pagibig_contribution'    => (float) ($salary->pagibig_contribution    ?? 0),
                'withholding_tax'         => (float) ($salary->withholding_tax         ?? 0),
                'deductions'              => (float) ($salary->deductions              ?? 0),
                'net_salary'              => (float) ($salary->net_salary              ?? 0),
                'effective_from'          => $salary->effective_from ?? '',
                'rate'                    => (float) ($employee->rate ?? 0),
                'rate_type'               => $employee->rate_type ?? 'monthly',
            ] : null,
        ]);
    }

    /**
     * Update employee salary
     */
    public function updateSalary()
    {
        $role = session()->get('role_name') ?? session()->get('role');
        if (!in_array($role, ['Super Admin', 'HR Admin', 'admin', 'hr'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }

        $employeeId    = $this->request->getPost('employee_id');
        $baseSalary    = $this->request->getPost('base_salary');
        $allowances    = (float) ($this->request->getPost('allowances') ?? 0);
        $deductions    = (float) ($this->request->getPost('deductions') ?? 0);
        $effectiveFrom = $this->request->getPost('effective_from') ?: date('Y-m-d');
        $rate          = (float) ($this->request->getPost('rate') ?? 0);
        $rateType      = $this->request->getPost('rate_type') ?? 'monthly';

        if (!$employeeId || $baseSalary === null || $baseSalary === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Employee and base salary are required.'
            ]);
        }

        // Validate rate_type
        if (!in_array($rateType, ['hourly', 'daily', 'monthly'])) {
            $rateType = 'monthly';
        }

        // Compute statutory deductions from base salary; deductions field = extra custom deductions
        $ded = PhDeductions::compute((float)$baseSalary, $allowances);
        $netSalary = $ded['net_salary'] - $deductions;

        // Update employee rate and rate_type
        if ($rate > 0) {
            $this->employeeModel->update($employeeId, [
                'rate'      => $rate,
                'rate_type' => $rateType,
            ]);
        }

        $existing = $this->salaryModel->getEmployeeSalary($employeeId);

        if ($existing) {
            // Update existing record
            $this->salaryModel->update($existing->id, [
                'base_salary'             => (float) $baseSalary,
                'allowances'              => $allowances,
                'sss_contribution'        => $ded['sss'],
                'philhealth_contribution' => $ded['philhealth'],
                'pagibig_contribution'    => $ded['pagibig'],
                'withholding_tax'         => $ded['withholding_tax'],
                'deductions'              => $deductions,
                'net_salary'              => $netSalary,
                'effective_from'          => $effectiveFrom,
            ]);
            $msg = 'Salary updated successfully.';
        } else {
            // Create new record
            $this->salaryModel->skipValidation(true)->insert([
                'employee_id'             => (int) $employeeId,
                'base_salary'             => (float) $baseSalary,
                'allowances'              => $allowances,
                'sss_contribution'        => $ded['sss'],
                'philhealth_contribution' => $ded['philhealth'],
                'pagibig_contribution'    => $ded['pagibig'],
                'withholding_tax'         => $ded['withholding_tax'],
                'deductions'              => $deductions,
                'net_salary'              => $netSalary,
                'effective_from'          => $effectiveFrom,
            ]);
            $msg = 'Salary rate set successfully.';
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $msg,
            'csrf_hash' => csrf_hash(),
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
            $role       = $employee->role_id        ? $this->roleModel->find($employee->role_id)            : null;

            return view('employee/review', [
                'employee'   => $employee,
                'department' => $department,
                'position'   => $role,
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
        $currentRole = session()->get('role_name') ?? session()->get('role');
        if (!in_array($currentRole, ['Super Admin', 'admin'])) {
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

            // Idempotent approve: if already approved with linked account, make sure
            // employment/user status is still active so manager views include the employee.
            if (($employee->account_status ?? '') === 'approved' && !empty($employee->user_id)) {
                $this->employeeModel->update($employeeId, [
                    'status' => 'active',
                ]);

                $this->userModel->skipValidation(true)->update((int) $employee->user_id, [
                    'is_active' => 1,
                ]);

                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'Employee is already approved.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }

            // Default password for all approved employee accounts
            $username = strtolower($employee->first_name[0] . $employee->last_name);
            $password = 'HRmanage!';

            // Get Employee role (employees get the 'Employee' role, not HR Admin)
            $db = \Config\Database::connect();
            $employeeRole = $db->table('roles')->where('name', 'Employee')->get()->getRow();
            // Fallback to any role with id=4, or the lowest-privilege role available
            $roleId = $employeeRole ? $employeeRole->id : 4;

            // Get HR Admin role (for notification purposes)
            $hrAdminRole = $db->table('roles')->where('name', 'HR Admin')->get()->getRow();

            // Create or reuse existing user account by email.
            $existingUser = $this->userModel->where('email', $employee->email)->first();
            $newUserId = null;

            if ($existingUser) {
                $generatedUsername = strtolower(preg_replace('/[^a-z0-9]/i', '', ($employee->first_name[0] ?? 'u') . $employee->last_name));
                $updateData = [
                    'name'      => $employee->first_name . ' ' . $employee->last_name,
                    'email'     => $employee->email,
                    'username'  => $existingUser->username ?: $generatedUsername,
                    'role_id'   => $roleId,
                    'is_active' => 1,
                ];

                // If user has no password yet, set default password on approval.
                if (empty($existingUser->password)) {
                    $updateData['password'] = $password;
                }

                // Skip strict create/update validation here because this is a
                // controlled system action and may involve legacy records that
                // do not satisfy current form-level validation rules.
                if (!$this->userModel->skipValidation(true)->update($existingUser->id, $updateData)) {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => 'Failed to update existing user account'
                    ]);
                }

                $newUserId = $existingUser->id;
            } else {
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
            }

            // Update employee with user_id, approved status, and activate employment status
            $this->employeeModel->update($employeeId, [
                'user_id'        => $newUserId,
                'account_status' => 'approved',
                'status'         => 'active',
            ]);

            // Notify department manager so the newly-approved employee is visible in their team workflows.
            $department = null;
            $managerUserId = 0;
            if (!empty($employee->department_id)) {
                $department = $db->table('departments')
                    ->select('id, name, manager_id')
                    ->where('id', (int) $employee->department_id)
                    ->get()
                    ->getRow();
                $managerUserId = (int) ($department->manager_id ?? 0);
            }

            if ($managerUserId > 0) {
                $managerUser = $this->userModel->find($managerUserId);
                if ($managerUser && (int) ($managerUser->is_active ?? 0) === 1) {
                    $deptName = $department->name ?? 'your department';
                    $this->notificationModel->insert([
                        'user_id' => $managerUserId,
                        'role'    => 'Manager',
                        'title'   => 'New Team Member Approved',
                        'message' => "{$employee->first_name} {$employee->last_name} was approved by Super Admin and is now active under {$deptName}.",
                        'status'  => 'unread',
                        'type'    => 'success',
                        'icon'    => 'fas fa-user-check',
                        'link'    => site_url('attendance/team'),
                        'is_read' => false,
                    ]);
                }
            }

            // Send email with credentials
            $this->sendCredentialsEmail($employee->email, $username, $password, $employee->first_name);

            // Notify HR Admin
            if ($hrAdminRole) {
                $superAdminName = session()->get('name') ?? session()->get('username') ?? 'Super Admin';
                $hrAdmins = $this->userModel
                    ->where('role_id', $hrAdminRole->id)
                    ->where('is_active', 1)
                    ->findAll();

                foreach ($hrAdmins as $hrAdmin) {
                    $this->notificationModel->insert([
                        'user_id' => $hrAdmin->id,
                        'role'    => 'HR Admin',
                        'title'   => 'Employee Account Approved',
                        'message' => "Super Admin {$superAdminName} approved '{$employee->first_name} {$employee->last_name}' (ID: {$employee->employee_id}). Account credentials were sent to the employee email.",
                        'status'  => 'unread',
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
        $currentRole = session()->get('role_name') ?? session()->get('role');
        if (!in_array($currentRole, ['Super Admin', 'admin'])) {
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
                        'role'    => 'HR Admin',
                        'title'   => 'Employee Account Rejected',
                        'message' => "The employee account for '{$employee->first_name} {$employee->last_name}' (ID: {$employee->employee_id}) has been rejected by Super Admin. Reason: {$rejectionNotes}",
                        'status'  => 'unread',
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
            $emailConfig = new \Config\Email();
            $emailService = \Config\Services::email();
            $emailService->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
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
