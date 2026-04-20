<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\NotificationModel;
use App\Models\PositionModel;
use App\Models\UserModel;
use App\Controllers\Audit;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Users extends BaseController
{
    protected $userModel;
    protected $employeeModel;
    protected $departmentModel;
    protected $notificationModel;
    protected $positionModel;

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
        $this->positionModel = new PositionModel();
    }

    /**
     * Custom validation rule to check if person is at least 17 years old
     */
    public function valid_date_before_age_17(string $value = null, string $field = null): bool
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

            return $age >= 17;
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
        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['admin', 'super_admin', 'hr', 'hr_admin'], true)
            || in_array($roleName, ['super admin', 'hr admin'], true);
    }

    private function isSuperAdminUser(): bool
    {
        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['super_admin', 'admin'], true)
            || $roleName === 'super admin';
    }

    private function resolvePositionIdByName(string $positionName): ?int
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

    private function generateEmployeeId(): string
    {
        $lastEmployee = $this->employeeModel->orderBy('id', 'DESC')->first();
        $nextNumber = 1;

        if ($lastEmployee && preg_match('/PPA-(\d+)/', (string) $lastEmployee->employee_id, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'PPA-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    private function getSmtpConfig()
    {
        // Try multiple ways to load the Email configuration
        
        // Method 1: Direct file include
        $configFile = APPPATH . 'Config/Email.php';
        if (file_exists($configFile)) {
            // Extract configuration values using regex from the file content
            $content = file_get_contents($configFile);
            
            // Extract SMTP values using regex
            preg_match('/public\s+string\s+\$SMTPHost\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $host);
            preg_match('/public\s+string\s+\$SMTPUser\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $user);
            preg_match('/public\s+string\s+\$SMTPPass\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $pass);
            preg_match('/public\s+string\s+\$fromEmail\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $from);
            preg_match('/public\s+string\s+\$fromName\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $name);
            preg_match('/public\s+int\s+\$SMTPPort\s*=\s*(\d+)/', $content, $port);
            preg_match('/public\s+int\s+\$SMTPTimeout\s*=\s*(\d+)/', $content, $timeout);
            preg_match('/public\s+string\s+\$SMTPCrypto\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $crypto);
            
            return [
                'host'    => $host[1] ?? 'smtp.gmail.com',
                'user'    => $user[1] ?? '',
                'pass'    => $pass[1] ?? '',
                'port'    => (int)($port[1] ?? 587),
                'timeout' => (int)($timeout[1] ?? 30),
                'crypto'  => $crypto[1] ?? 'tls',
                'from'    => $from[1] ?? '',
                'name'    => $name[1] ?? 'PeopleAxis HR System',
            ];
        }
        
        // Fallback: Try object instantiation
        try {
            $config = new \Config\Email();
            return [
                'host'    => $config->SMTPHost ?? 'smtp.gmail.com',
                'user'    => $config->SMTPUser ?? '',
                'pass'    => $config->SMTPPass ?? '',
                'port'    => $config->SMTPPort ?? 587,
                'timeout' => $config->SMTPTimeout ?? 30,
                'crypto'  => $config->SMTPCrypto ?? 'tls',
                'from'    => $config->fromEmail ?? '',
                'name'    => $config->fromName ?? 'PeopleAxis HR System',
            ];
        } catch (\Exception $e) {
            log_message('error', '[getSmtpConfig] Error: ' . $e->getMessage());
        }
        
        return [
            'host'    => 'smtp.gmail.com',
            'user'    => '',
            'pass'    => '',
            'port'    => 587,
            'timeout' => 30,
            'crypto'  => 'tls',
            'from'    => '',
            'name'    => 'PeopleAxis HR System',
        ];
    }

    private function sendWelcomeEmail(string $to, string $userName, string $tempPassword): bool
    {
        if (!class_exists(PHPMailer::class)) {
            log_message('warning', '[sendWelcomeEmail] PHPMailer class not found');
            return false;
        }

        $cfg = $this->getSmtpConfig();
        $smtpHost = (string) $cfg['host'];
        $smtpUser = (string) $cfg['user'];
        $smtpPass = (string) $cfg['pass'];
        $smtpPort = (int) $cfg['port'];
        $smtpTimeout = (int) $cfg['timeout'];
        $smtpCrypto = strtolower(trim((string) $cfg['crypto']));
        $fromEmail = (string) $cfg['from'];
        $fromName = (string) $cfg['name'];

        if ($smtpHost === '' || $smtpUser === '' || $smtpPass === '') {
            log_message('warning', '[sendWelcomeEmail] SMTP configuration incomplete');
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->Port       = $smtpPort;
            $mail->Timeout    = $smtpTimeout;

            if ($smtpCrypto === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtpCrypto === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->SMTPAutoTLS = true;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->CharSet  = 'UTF-8';
            $mail->Subject  = 'Welcome to PeopleAxis - Your Account Has Been Created';
            
            $htmlBody = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <div style="background: linear-gradient(135deg,#2f5f45 0%,#6ea988 100%); color: white; padding: 20px; border-radius: 5px 5px 0 0;">
                    <h2>Welcome to PeopleAxis</h2>
                </div>
                <div style="padding: 20px; background: #f9f9f9;">
                    <p>Dear ' . esc($userName) . ',</p>
                    <p>Your account has been successfully created in the PeopleAxis HR System.</p>
                    <div style="background: white; padding: 15px; border-left: 4px solid #2f5f45; margin: 15px 0;">
                        <p><strong>Login Credentials:</strong></p>
                        <p><strong>Email:</strong> ' . esc($to) . '</p>
                        <p><strong>Temporary Password:</strong> <code style="background: #f0f0f0; padding: 5px 10px; border-radius: 3px;">' . esc($tempPassword) . '</code></p>
                    </div>
                    <p><strong>Important:</strong> Please change your password immediately upon first login for security purposes.</p>
                    <p>If you did not request this account or have any questions, please contact your administrator.</p>
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                    <p style="font-size: 12px; color: #666;">
                        This is an automated email. Please do not reply directly to this message.
                    </p>
                </div>
            </div>
            ';
            
            $mail->Body     = $htmlBody;
            $mail->AltBody  = strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (Exception $e) {
            $details = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
            log_message('error', '[sendWelcomeEmail] PHPMailer error to [' . $to . ']: ' . $details);
            return false;
        }
    }

    /**
     * Show create user form - Now redirects to main users page with modal
     */
    public function create()
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin or HR Admin only.');
        }
        
        // Redirect to users page - the add user modal is now on the main page
        return redirect()->to('/users');
    }

    /**
     * Show all users - READ operation
     */
    public function index()
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin or HR Admin only.');
        }
        
        // Load users with role information to avoid N+1 queries in the view
        $db = \Config\Database::connect();
        $users = $db->table('users')
            ->select('users.*, roles.id as role_id, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.created_at', 'DESC')
            ->get()
            ->getResult();

        $data['users'] = $users;
        $currentUserId = session()->get('user_id');
        
        // Get role names for display
        $db = \Config\Database::connect();
        $roles = $db->table('roles')->get()->getResultArray();
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role['id']] = $role['name'];
        }
        $data['roleMap'] = $roleMap;
        
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
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin or HR Admin only.');
        }
        
        // Get role_id early to determine if this is an admin role
        $roleId = (int) $this->request->getPost('role_id');
        $roleName = $this->getRoleNameByRoleId($roleId);
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
            $rules['rfid_number'] = 'required|max_length[100]|is_unique[employees.rfid_number]';
            $rules['position'] = 'required|in_list[Front Counter,Kitchen/Prep,Drive-Thru,Dining Room]';
            $rules['department_id'] = 'permit_empty|numeric';
            $rules['date_of_birth'] = 'required|valid_date|valid_date_before_age_17';
            $rules['date_of_joining'] = 'permit_empty|valid_date';
        } else {
            $rules['rfid_number'] = 'permit_empty|max_length[100]';
            $rules['position'] = 'permit_empty';
            $rules['department_id'] = 'permit_empty|numeric';
            $rules['date_of_birth'] = 'permit_empty';
            $rules['date_of_joining'] = 'permit_empty';
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
                    $validationErrors['date_of_birth'] = 'Employee must be at least 17 years old';
                }
            }
            
            return redirect()->back()->withInput()->with('errors', $validationErrors);
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
        $positionName = trim((string) $this->request->getPost('position'));
        $positionId = $this->resolvePositionIdByName($positionName);
        $departmentId = (int) ($this->request->getPost('department_id') ?? 0);
        $dateOfBirth = trim((string) $this->request->getPost('date_of_birth'));
        $dateOfJoiningInput = trim((string) $this->request->getPost('date_of_joining'));
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

        // Only create employee record for non-admin roles
        $newEmployeeId = 0;
        if (!$isAdminRole) {
            $employeeInsertResult = $this->employeeModel->skipValidation(true)->insert([
                'employee_id'     => $this->generateEmployeeId(),
                'first_name'      => $firstName !== '' ? $firstName : $name,
                'last_name'       => $lastName,
                'email'           => $this->request->getPost('email'),
                'phone'           => $phone,
                'rfid_number'     => $rfidNumber,
                'department_id'   => $departmentId > 0 ? $departmentId : null,
                'position_id'     => $positionId,
                'role_id'         => $roleId,
                'date_of_birth'   => $dateOfBirth,
                'date_of_joining' => $dateOfJoining,
                'date_hired'      => $dateOfJoining,
                'status'          => $isActive === 1 ? 'active' : 'inactive',
                'account_status'  => 'pending',
                'user_id'         => $userId,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            if (!$employeeInsertResult) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('errors',
                    $this->employeeModel->errors() ?: ['store' => 'Unable to create employee record.']
                );
            }
            $newEmployeeId = (int) $this->employeeModel->getInsertID();
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('errors', [
                'store' => 'Unable to create the new user at this time.'
            ]);
        }

        // Notify all active Super Admins that a new employee account is waiting for approval (only for non-admin roles).
        if ($newEmployeeId > 0) {
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
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin or HR Admin only.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User not found");
        }
        $data['user'] = $user;
        return view('auth/edit_user', $data);
    }

    public function update($id)
    {
        if (! $this->canManageUsers()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin or HR Admin only.');
        }
        
        try {
            // Find user
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'User not found']);
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
