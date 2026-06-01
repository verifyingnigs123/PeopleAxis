<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Controllers\Audit;

class Users extends BaseController
{
    protected $userModel;
    protected $employeeModel;
    protected $departmentModel;
    protected $notificationModel;

    private const ROLE_NAME_TO_SLUG = [
        'super admin' => 'super_admin',
        'hr admin'    => 'hr_admin',
        'manager'     => 'manager',
        'employee'    => 'employee',
    ];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->employeeModel = new EmployeeModel();
        $this->departmentModel = new DepartmentModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Custom validation helper to check if person is at least 18 years old
     */
    private function validateDateBeforeAge18(string $value = null): bool
    {
        if (empty($value)) {
            return true;
        }

        try {
            $dateOfBirth = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$dateOfBirth) {
                return false;
            }

            $today = new \DateTime();
            $age = $today->diff($dateOfBirth)->y;

            return $age >= 18;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getRoleSlugByRoleId(int $roleId): string
    {
        $db = \Config\Database::connect();
        $role = $db->table('roles')->select('name')->where('id', $roleId)->get()->getRow();
        $roleName = strtolower((string) ($role->name ?? 'employee'));

        return self::ROLE_NAME_TO_SLUG[$roleName] ?? 'employee';
    }

    private function getRoleNameByRoleId(int $roleId): string
    {
        $db = \Config\Database::connect();
        $role = $db->table('roles')->select('name')->where('id', $roleId)->get()->getRow();
        return (string) ($role->name ?? 'Employee');
    }

    private function canManageUsers(): bool
    {
        // Use privilege helper for dynamic privilege checking
        $helper = new \App\Helpers\PrivilegeHelper();
        return $helper->canManageUsers();
    }

    private function isSuperAdminUser(): bool
    {
        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['super_admin', 'admin'], true)
            || $roleName === 'super admin';
    }

    private function isProtectedSuperAdminAccount(?object $user): bool
    {
        if (! $user) {
            return false;
        }

        $roleName = strtolower((string) ($user->role_name ?? ''));
        $roleSlug = strtolower((string) ($user->role ?? ''));

        return $roleName === 'super admin' || $roleSlug === 'super_admin';
    }

    private function generateEmployeeId(): string
    {
        $db = \Config\Database::connect();
        $maxResult = $db->table('employees')
            ->select('MAX(CAST(SUBSTRING(employee_id, 5) AS UNSIGNED)) as max_num')
            ->where('employee_id LIKE', 'PPA-%')
            ->get()
            ->getRow();

        $nextNumber = 1;
        if ($maxResult && $maxResult->max_num) {
            $nextNumber = ((int) $maxResult->max_num) + 1;
        }

        // Keep generating IDs until we find one that doesn't exist
        $attemptCount = 0;
        while ($attemptCount < 1000) {
            $newId = 'PPA-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
            $exists = $db->table('employees')->where('employee_id', $newId)->countAllResults() > 0;

            if (!$exists) {
                return $newId;
            }

            $nextNumber++;
            $attemptCount++;
        }

        // Fallback: return a unique ID with timestamp if all else fails
        return 'PPA-' . str_pad((string) (time() % 100000), 5, '0', STR_PAD_LEFT);
    }

    private function sendWelcomeEmail(string $to, string $userName, string $tempPassword): bool
    {
        try {
            $emailConfig = new \Config\Email();
            $emailService = \Config\Services::email();
            $emailService->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $emailService->setTo($to);
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
                            <p>Dear {$userName},</p>
                            <p>Your account has been successfully created in the PeopleAxis HR System. Please use the credentials below to log in:</p>
                            <div class='credentials'>
                                <p><span class='label'>Email:</span> {$to}</p>
                                <p><span class='label'>Password:</span> {$tempPassword}</p>
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

            if (!$emailService->send()) {
                log_message('error', '[sendWelcomeEmail] Failed to send credentials email to [' . $to . ']');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('error', '[sendWelcomeEmail] Failed to send credentials email to [' . $to . ']: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Show create user form - Now redirects to main users page with modal
     */
    public function create()
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        // Redirect to users page - the add user modal is now on the main page
        // Redirect with hash so the users page can open the add-user modal automatically
        return redirect()->to('/users#addUser');
    }

    /**
     * Show all users - READ operation
     */
    public function index()
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        // Load users with role information to avoid N+1 queries in the view
        $db = \Config\Database::connect();
        $users = $db->table('users')
            ->select('users.*, roles.id as role_id, roles.name as role_name, roles.privileges as role_privileges')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.created_at', 'DESC')
            ->get()
            ->getResult();

        $data['users'] = $users;
        $currentUserId = session()->get('user_id');
        
        // Get role names and privileges for display
        $db = \Config\Database::connect();
        $roles = $db->table('roles')->get()->getResultArray();
        $roleMap = [];
        $rolePrivilegesMap = [];
        foreach ($roles as $role) {
            $roleMap[$role['id']] = $role['name'];
            $privileges = [];
            if (!empty($role['privileges'])) {
                try {
                    $decoded = json_decode($role['privileges'], true);
                    if (is_array($decoded)) {
                        $privileges = $decoded;
                    }
                } catch (\Exception $e) {
                    $privileges = [];
                }
            }
            $rolePrivilegesMap[$role['id']] = $privileges;
        }
        $data['roleMap'] = $roleMap;
        $data['rolePrivilegesMap'] = $rolePrivilegesMap;
        
        // Separate current admin from other users
        $adminUser = null;
        $otherUsers = [];
        foreach ($data['users'] as $user) {
            if ($user->id == $currentUserId) {
                $adminUser = $user;
            } else {
                $otherUsers[] = $user;
            }
        }

        // If admin user found, put them at the beginning of the list
        if ($adminUser) {
            array_unshift($otherUsers, $adminUser);
        }

        $data['users'] = $otherUsers;
        $data['currentUserId'] = $currentUserId;

        // Build employee email map so the view can show whether each user is linked to an employee record.
        // Join roles so we can display the employee's role in the manage-users table.
        $employeeRows = $db->table('employees')
            ->select('employees.email, employees.first_name, employees.last_name, employees.employee_id, employees.account_status, roles.name as position_name')
            ->join('roles', 'roles.id = employees.role_id', 'left')
            ->get()
            ->getResultArray();
        $employeeEmailMap = [];
        foreach ($employeeRows as $emp) {
            $employeeEmailMap[$emp['email']] = $emp;
        }
        $data['employeeEmailMap'] = $employeeEmailMap;
        $data['departments'] = $this->departmentModel->getActiveDepartments();

        return view('auth/users', $data);
    }

    public function store()
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        // Get role_id early to determine if this is an admin role
        $roleId = (int) $this->request->getPost('role_id');
        $roleName = $this->getRoleNameByRoleId($roleId);
        // Prevent privilege escalation: only an actual Super Admin may assign the Super Admin role
        if (strtolower(trim((string) $roleName)) === 'super admin' && ! $this->isSuperAdminUser()) {
            return redirect()->back()->withInput()->with('errors', [
                'role_id' => 'You are not authorized to assign the Super Admin role.'
            ]);
        }
        $isAdminRole = in_array(strtolower((string) $roleName), ['super admin', 'hr admin'], true);
        
        // Set validation rules based on role type
        $rules = [
            'name'           => 'required|min_length[3]|max_length[100]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'phone'          => 'permit_empty|regex_match[/^\+639\d{9}$/]',
            'role_id'        => 'required|numeric',
            'is_active'      => 'required|in_list[0,1]'
        ];
        
        // Add employee-specific validation only for non-admin roles
        if (!$isAdminRole) {
            $rules['rfid_number'] = 'required|max_length[100]';
            $rules['department_id'] = 'permit_empty|numeric';
            $rules['date_of_birth'] = 'required|valid_date';
            $rules['date_of_joining'] = 'permit_empty|valid_date';
            $rules['employment_type'] = 'permit_empty|in_list[full_time,part_time,contractual,probationary]';
            $rules['rate'] = 'permit_empty|decimal';
            $rules['rate_type'] = 'permit_empty|in_list[hourly,daily,monthly]';
        } else {
            $rules['rfid_number'] = 'permit_empty|max_length[100]';
            $rules['department_id'] = 'permit_empty|numeric';
            $rules['date_of_birth'] = 'permit_empty';
            $rules['date_of_joining'] = 'permit_empty';
            $rules['employment_type'] = 'permit_empty|in_list[full_time,part_time,contractual,probationary]';
            $rules['rate'] = 'permit_empty|decimal';
            $rules['rate_type'] = 'permit_empty|in_list[hourly,daily,monthly]';
        }

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            
            // Custom error messages
            if (isset($validationErrors['phone'])) {
                $validationErrors['phone'] = 'Phone number must be in Philippine format (+639xxxxxxxxx).';
            }
            if (isset($validationErrors['date_of_birth'])) {
                if (strpos($validationErrors['date_of_birth'], 'The') === 0 && strpos($validationErrors['date_of_birth'], 'field is required') !== false) {
                    $validationErrors['date_of_birth'] = 'Date of birth is required';
                } else {
                    $validationErrors['date_of_birth'] = 'User should be 18 and above (please be guided).';
                }
            }
            
            return redirect()->back()->withInput()->with('errors', $validationErrors);
        }

        // Additional validation: Check age for non-admin roles
        if (!$isAdminRole) {
            $dateOfBirth = trim((string) $this->request->getPost('date_of_birth'));
            if ($dateOfBirth && !$this->validateDateBeforeAge18($dateOfBirth)) {
                return redirect()->back()->withInput()->with('errors', [
                    'date_of_birth' => 'User should be 18 and above (please be guided).'
                ]);
            }
        }

        $name = trim((string) $this->request->getPost('name'));
        if (!preg_match('/^[A-Za-z0-9\s]+$/', $name)) {
            return redirect()->back()->withInput()->with('errors', [
                'name' => 'Name can only contain letters, numbers, and spaces. Special characters are not allowed.'
            ]);
        }

        $firstName = trim((string) $this->request->getPost('first_name'));
        $lastName = trim((string) $this->request->getPost('last_name'));
        $phone = trim((string) $this->request->getPost('phone'));
        $rfidNumber = trim((string) $this->request->getPost('rfid_number'));
        $departmentId = (int) ($this->request->getPost('department_id') ?? 0);
        $dateOfBirth = trim((string) $this->request->getPost('date_of_birth'));
        $dateOfJoiningInput = trim((string) $this->request->getPost('date_of_joining'));
        $employmentType = trim((string) $this->request->getPost('employment_type'));
        $rateInput = trim((string) $this->request->getPost('rate'));
        $rateType = trim((string) $this->request->getPost('rate_type'));
        // Use current date if date_of_joining is not provided
        $dateOfJoining = !empty($dateOfJoiningInput) ? $dateOfJoiningInput : date('Y-m-d');
        $isActive = (int) $this->request->getPost('is_active');
        $generatedPassword = 'Hrmanage';

        $db = \Config\Database::connect();
        $db->transStart();

        $userId = $this->userModel->insert([
            'name'       => $name,
            'email'      => $this->request->getPost('email'),
            'password'   => $generatedPassword,
            'role'       => $this->getRoleSlugByRoleId($roleId),
            'role_id'    => $roleId,
            'is_active'  => $isActive,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!$userId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('errors',
                $this->userModel->errors() ?: ['store' => 'Unable to create user account.']
            );
        }

        // Only create employee record for non-admin roles (Employees and Managers get employee records)
        $newEmployeeId = 0;
        if (!$isAdminRole) {
            $userEmail = $this->request->getPost('email');
            
            // Get current user's role to determine approval requirement
            $currentUserRole = strtolower((string) session()->get('role_name'));
            
            // Approval logic:
            // - Super Admin creates Manager/Employee → Auto-approved (no approval needed)
            // - HR Admin creates Manager/Employee → Pending (requires Super Admin approval)
            $accountStatus = ($currentUserRole === 'super admin') ? 'approved' : 'pending';
            
            // Check if an employee with this email or RFID already exists
            $existingEmployee = $this->employeeModel->where('email', $userEmail)->first();
            
            // If not found by email and RFID is provided, check by RFID
            if (!$existingEmployee && $rfidNumber) {
                $existingEmployee = $this->employeeModel->where('rfid_number', $rfidNumber)->first();
            }
            
            if ($existingEmployee) {
                // Employee record exists - update it with the user_id if not already linked
                if (!$existingEmployee->user_id) {
                    $updateData = [
                        'user_id'         => $userId,
                        'first_name'      => $firstName !== '' ? $firstName : $name,
                        'last_name'       => $lastName,
                        'email'           => $userEmail,
                        'phone'           => $phone ?: $existingEmployee->phone,
                        'rfid_number'     => $rfidNumber ?: $existingEmployee->rfid_number,
                        'date_of_birth'   => $dateOfBirth ?: $existingEmployee->date_of_birth,
                        'date_of_joining' => $dateOfJoining,
                        'date_hired'      => $dateOfJoining ?: null,
                        'status'          => $isActive === 1 ? 'active' : 'inactive',
                        'account_status'  => $accountStatus,
                        'employment_type' => $employmentType !== '' ? $employmentType : ($existingEmployee->employment_type ?? null),
                        'rate'            => $rateInput !== '' ? (float) $rateInput : ($existingEmployee->rate ?? null),
                        'rate_type'       => $rateType !== '' ? $rateType : ($existingEmployee->rate_type ?? null),
                    ];
                    
                    if ($departmentId > 0) {
                        $updateData['department_id'] = $departmentId;
                    }
                    if ($roleId > 0) {
                        $updateData['role_id'] = $roleId;
                    }
                    
                    log_message('debug', 'Updating existing employee with data: ' . json_encode($updateData));
                    
                    $updateResult = $this->employeeModel->update($existingEmployee->id, $updateData);
                    if (!$updateResult) {
                        $db->transRollback();
                        $errors = $this->employeeModel->errors() ?: ['store' => 'Unable to update employee record.'];
                        log_message('error', 'Employee update failed. Model errors: ' . json_encode($errors));
                        return redirect()->back()->withInput()->with('errors', $errors);
                    }
                    $newEmployeeId = (int) $existingEmployee->id;
                } else {
                    // Employee already has a user_id, skip employee creation
                    $newEmployeeId = (int) $existingEmployee->id;
                }
            } else {
                // No existing employee, create a new one
                $employeeData = [
                    'employee_id'     => $rfidNumber ?: $this->generateEmployeeId(),
                    'first_name'      => $firstName !== '' ? $firstName : $name,
                    'last_name'       => $lastName,
                    'email'           => $userEmail,
                    'phone'           => $phone ?: '',
                    'rfid_number'     => $rfidNumber ?: '',
                    'date_of_birth'   => $dateOfBirth ?: null,
                    'date_of_joining' => $dateOfJoining,
                    'date_hired'      => $dateOfJoining ?: null,
                    'status'          => $isActive === 1 ? 'active' : 'inactive',
                    'account_status'  => $accountStatus,
                    'user_id'         => $userId,
                    'employment_type' => $employmentType !== '' ? $employmentType : null,
                    'rate'            => $rateInput !== '' ? (float) $rateInput : null,
                    'rate_type'       => $rateType !== '' ? $rateType : null,
                ];
                
                // Only add optional fields if they have values
                if ($departmentId > 0) {
                    $employeeData['department_id'] = $departmentId;
                }
                if ($roleId > 0) {
                    $employeeData['role_id'] = $roleId;
                }

                log_message('debug', 'Attempting employee insert with data: ' . json_encode($employeeData));
                
                $employeeInsertResult = $this->employeeModel->insert($employeeData);

                if (!$employeeInsertResult) {
                    $db->transRollback();
                    $errors = $this->employeeModel->errors() ?: ['store' => 'Unable to create employee record.'];
                    $dbError = $db->error();
                    log_message('error', 'Employee insert failed. Model errors: ' . json_encode($errors) . ' | DB error: ' . json_encode($dbError));
                    return redirect()->back()->withInput()->with('errors', $errors);
                }
                $newEmployeeId = (int) $this->employeeModel->getInsertID();
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            log_message('error', 'Database transaction failed for user creation');
            return redirect()->back()->withInput()->with('errors', [
                'store' => 'Unable to create the new user at this time. Please try again.'
            ]);
        }

        // Notify all active Super Admins that a new employee account is waiting for approval (only for non-admin roles with pending status).
        if ($newEmployeeId > 0) {
            // Check if employee account is pending approval
            $employee = $this->employeeModel->find($newEmployeeId);
            if ($employee && $employee->account_status === 'pending') {
                $actorName = session()->get('name') ?? session()->get('username') ?? 'HR Admin';
                $dbNotif = \Config\Database::connect();
                $superAdminRole = $dbNotif->table('roles')->where('name', 'Super Admin')->get()->getRow();
                if ($superAdminRole) {
                    $superAdmins = $this->userModel
                        ->where('role_id', $superAdminRole->id)
                        ->where('is_active', 1)
                        ->findAll();

                    foreach ($superAdmins as $superAdmin) {
                        $this->notificationModel->insert([
                            'user_id' => $superAdmin->id,
                            'role'    => 'Super Admin',
                            'title'   => 'New Employee Awaiting Approval',
                            'message' => "HR Admin {$actorName} added '{$firstName} {$lastName}' and submitted the account for your approval.",
                            'status'  => 'unread',
                            'type'    => 'warning',
                            'icon'    => 'fas fa-user-check',
                            'link'    => site_url('employee/review/' . $newEmployeeId),
                            'is_read' => false,
                        ]);
                    }
                }
            }
        }

        // Send welcome email to the newly created user
        $userEmail = (string) $this->request->getPost('email');
        $this->sendWelcomeEmail($userEmail, $name, $generatedPassword);

        // Log audit
        $userId = session()->get('user_id');
        Audit::log($userId, 'CREATE', 'User', 'Created user: ' . esc($name) . ' (' . esc($this->request->getPost('email')) . ')');

        return redirect()->to('/users')->with('success', 'User created successfully. Welcome email has been sent to the user.');
    }

    public function edit($id)
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User not found");
        }
        $db = \Config\Database::connect();
        $roleRecord = $db->table('roles')
            ->select('name')
            ->where('id', $user->role_id)
            ->get()
            ->getRow();
        $user->role_name = $roleRecord->name ?? null;

        if ($this->isProtectedSuperAdminAccount($user)) {
            return redirect()->to('/users')->with('error', 'Super Admin accounts cannot be edited.');
        }

        $data['user'] = $user;
        return view('auth/edit_user', $data);
    }

    public function update($id)
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        try {
            // Find user
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'User not found']);
            }

            $db = \Config\Database::connect();
            $targetRole = $db->table('roles')
                ->select('name')
                ->where('id', $user->role_id)
                ->get()
                ->getRow();
            $user->role_name = $targetRole->name ?? null;

            if ($this->isProtectedSuperAdminAccount($user)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Super Admin accounts cannot be edited.'
                ]);
            }

            // Get form data
            $name = trim((string) $this->request->getPost('name'));
            $email = $this->request->getPost('email');
            $roleId = $this->request->getPost('role_id') ?? $this->request->getPost('role');
            $isActive = $this->request->getPost('is_active');
            $password = trim((string) $this->request->getPost('password'));

            // Basic validation
            if (!$name || !$email || !$roleId) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Name, email, and role are required'
                ]);
            }

            if (strlen($name) < 3 || strlen($name) > 100) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Name must be 3-100 characters'
                ]);
            }

            if (!preg_match('/^[A-Za-z0-9\s]+$/', $name)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Name can only contain letters, numbers, and spaces. Special characters are not allowed.'
                ]);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Invalid email format'
                ]);
            }

            // roleId should be numeric; additional validation can be added to ensure role exists
            if (!is_numeric($roleId)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Invalid role selection'
                ]);
            }

            // Check if email already exists for another user
            $db = \Config\Database::connect();
            $existingEmail = $db->table('users')
                ->select('id')
                ->where('email', $email)
                ->where('id !=', $id)
                ->limit(1)
                ->get()
                ->getRow();

            if ($existingEmail) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Email already in use'
                ]);
            }

            $normalizedRoleId = (int) $roleId;
            $roleRecord = $db->table('roles')
                ->select('id, name')
                ->where('id', $normalizedRoleId)
                ->get()
                ->getRow();

            if (! $roleRecord) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Invalid role selection'
                ]);
            }

            $roleName = strtolower(trim((string) $roleRecord->name));

            // Prevent privilege escalation: only an actual Super Admin may assign the Super Admin role
            if ($roleName === 'super admin' && ! $this->isSuperAdminUser()) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'You are not authorized to assign the Super Admin role.'
                ]);
            }

            // Prepare update data
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role' => self::ROLE_NAME_TO_SLUG[$roleName] ?? 'employee',
                'role_id' => $normalizedRoleId,
                'is_active' => (int)$isActive,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update password when provided (or auto-filled by Super Admin flow).
            if ($password !== '') {
                if (strlen($password) < 6) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'success' => false,
                        'message' => 'Password must be at least 6 characters.'
                    ]);
                }
                $updateData['password'] = $password;
            }

            // Update in database
            // skipValidation: controller already validated all fields above;
            // the model's is_unique email rule would fail because it cannot
            // resolve the {id} placeholder when 'id' is not in $updateData.
            $result = $this->userModel->skipValidation(true)->update($id, $updateData);

            if ($result) {
                // Reuse role already fetched above for DOM update
                $updatedRoleName = (string) $roleRecord->name;

                // Log audit
                $userId = session()->get('user_id');
                Audit::log($userId, 'UPDATE', 'User', 'Updated user: ' . esc($name) . ' (ID: ' . $id . ')');

                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'User updated successfully',
                    'csrf_hash' => csrf_hash(),
                    'user'      => [
                        'id'        => (int)$id,
                        'name'      => $name,
                        'email'     => $email,
                        'role_id'   => (int)$roleId,
                        'role_name' => $updatedRoleName,
                        'is_active' => (int)$isActive,
                    ],
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to update user'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Update user error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Activate user
     */
    public function activate($id)
    {
        if (! $this->canManageUsers()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin or HR Admin only.'
            ]);
        }

        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            if ($this->isProtectedSuperAdminAccount($user)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Super Admin accounts cannot be modified.'
                ]);
            }

            if ($this->userModel->activateUser($id)) {
                // Log audit
                $userId = session()->get('user_id');
                $user = $this->userModel->find($id);
                Audit::log($userId, 'UPDATE', 'User', 'Activated user: ' . esc($user->name ?? 'Unknown') . ' (ID: ' . $id . ')');

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User activated successfully',
                    'status' => 'ACTIVE',
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to activate user'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Activate user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Deactivate user (soft delete)
     */
    public function deactivate($id)
    {
        if (! $this->canManageUsers()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin or HR Admin only.'
            ]);
        }

        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            if ($this->isProtectedSuperAdminAccount($user)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Super Admin accounts cannot be modified.'
                ]);
            }

            // Prevent admin from deactivating themselves
            if ($user->id == session()->get('user_id')) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account'
                ]);
            }

            if ($this->userModel->deactivateUser($id)) {
                $this->invalidateUserSessions((int) $id);

                // Log audit
                $userId = session()->get('user_id');
                $user = $this->userModel->find($id);
                Audit::log($userId, 'UPDATE', 'User', 'Deactivated user: ' . esc($user->name ?? 'Unknown') . ' (ID: ' . $id . ')');

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User deactivated successfully',
                    'status' => 'INACTIVE',
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to deactivate user'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Deactivate user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete user (hard delete)
     */
    public function delete($id)
    {
        if (! $this->canManageUsers()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            if ($this->isProtectedSuperAdminAccount($user)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Super Admin accounts cannot be modified.'
                ]);
            }

            // Prevent admin from deleting themselves
            if ($user->id == session()->get('user_id')) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ]);
            }

            // Soft-delete: mark user inactive and set deleted_at
            $result = $this->userModel->update($id, [
                'is_active' => 0,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            if ($result) {
                $this->invalidateUserSessions((int) $id);

                // Log audit
                $userId = session()->get('user_id');
                Audit::log($userId, 'DELETE', 'User', 'Deleted user: ' . esc($user->name ?? 'Unknown') . ' (ID: ' . $id . ')');

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User deleted (soft) successfully',
                    'status' => 'DELETED',
                    'csrf_hash' => csrf_hash()
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete user'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Delete user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        if (! $this->canManageUsers()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied. Super Admin only.'
            ]);
        }

        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            if ($this->userModel->update($id, ['is_active' => 1, 'deleted_at' => null])) {
                // Log audit
                $userId = session()->get('user_id');
                $user = $this->userModel->find($id);
                Audit::log($userId, 'RESTORE', 'User', 'Restored user: ' . esc($user->name ?? 'Unknown') . ' (ID: ' . $id . ')');

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User restored successfully',
                    'status' => 'RESTORED',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to restore user'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Restore user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
