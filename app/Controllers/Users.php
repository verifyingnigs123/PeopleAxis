<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\NotificationModel;
use App\Models\PositionModel;
use App\Models\UserModel;
use App\Controllers\Audit;

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

    private function getRoleSlugByRoleId(int $roleId): string
    {
        $db = \Config\Database::connect();
        $role = $db->table('roles')->select('name')->where('id', $roleId)->get()->getRow();
        $roleName = strtolower((string) ($role->name ?? 'employee'));

        return self::ROLE_NAME_TO_SLUG[$roleName] ?? 'employee';
    }

    private function canManageUsers(): bool
    {
        $role = strtolower((string) session()->get('role'));
        $roleName = strtolower((string) session()->get('role_name'));

        return in_array($role, ['admin', 'super_admin', 'hr', 'hr_admin'], true)
            || in_array($roleName, ['super admin', 'hr admin'], true);
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
        
        $rules = [
            'name'           => 'required|min_length[3]|max_length[100]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'phone'          => 'permit_empty|max_length[20]',
            'rfid_number'    => 'required|max_length[100]|is_unique[employees.rfid_number]',
            'position'       => 'required|in_list[Front Counter,Kitchen/Prep,Drive-Thru,Dining Room]',
            'department_id'  => 'permit_empty|numeric',
            'role_id'        => 'required|numeric',
            'date_of_birth'  => 'required|valid_date',
            'date_of_joining'=> 'required|valid_date',
            'is_active'      => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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
        $roleId = (int) $this->request->getPost('role_id');
        $dateOfBirth = trim((string) $this->request->getPost('date_of_birth'));
        $dateOfJoining = trim((string) $this->request->getPost('date_of_joining'));
        $isActive = (int) $this->request->getPost('is_active');
        $generatedPassword = bin2hex(random_bytes(8));

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

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('errors', [
                'store' => 'Unable to create the new user at this time.'
            ]);
        }

        // Notify all active Super Admins that a new employee account is waiting for approval.
        $actorName = session()->get('name') ?? session()->get('username') ?? 'HR Admin';
        $dbNotif = \Config\Database::connect();
        $superAdminRole = $dbNotif->table('roles')->where('name', 'Super Admin')->get()->getRow();
        if ($superAdminRole && $newEmployeeId > 0) {
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

        // Log audit
        $userId = session()->get('user_id');
        Audit::log($userId, 'CREATE', 'User', 'Created user: ' . esc($name) . ' (' . esc($this->request->getPost('email')) . ')');

        return redirect()->to('/users')->with('success', 'User created successfully. Super Admin has been notified for approval.');
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
            $password = $this->request->getPost('password');

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
                ->where('email', $email)
                ->where('id !=', $id)
                ->get()
                ->getResultArray();

            if (!empty($existingEmail)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Email already in use'
                ]);
            }

            // Prepare update data
            $normalizedRoleId = (int) $roleId;
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role' => $this->getRoleSlugByRoleId($normalizedRoleId),
                'role_id' => $normalizedRoleId,
                'is_active' => (int)$isActive,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Only update password if provided
            if (!empty($password) && is_string($password) && strlen(trim($password)) >= 6) {
                if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                    return $this->response->setStatusCode(422)->setJSON([
                        'success' => false,
                        'message' => 'Password must contain at least one special character (e.g. !, @, #, $).'
                    ]);
                }
                $updateData['password'] = trim($password);
            }

            // Update in database
            // skipValidation: controller already validated all fields above;
            // the model's is_unique email rule would fail because it cannot
            // resolve the {id} placeholder when 'id' is not in $updateData.
            $result = $this->userModel->skipValidation(true)->update($id, $updateData);

            if ($result) {
                // Fetch updated role name for DOM update
                $roleRecord = $db->table('roles')->where('id', (int)$roleId)->get()->getRow();
                $updatedRoleName = $roleRecord ? $roleRecord->name : 'User';

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
