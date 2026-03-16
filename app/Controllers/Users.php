<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\Audit;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Show create user form - Now redirects to main users page with modal
     */
    public function create()
    {
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        // Redirect to users page - the add user modal is now on the main page
        return redirect()->to('/users');
    }

    /**
     * Show all users - READ operation
     */
    public function index()
    {
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
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

        return view('auth/users', $data);
    }

    public function store()
    {
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        $rules = [
            'name'              => 'required|min_length[3]|max_length[100]',
            'email'             => 'required|valid_email|is_unique[users.email]',
            'password'          => 'required|min_length[6]|max_length[255]',
            'confirm_password'  => 'required|matches[password]',
            'role_id'           => 'required|numeric',
            'is_active'         => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Require at least one special character in the password
        $password = $this->request->getPost('password');
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return redirect()->back()->withInput()->with('errors', [
                'password' => 'Password must contain at least one special character (e.g. !, @, #, $).'
            ]);
        }

        $name = trim((string) $this->request->getPost('name'));
        if (!preg_match('/^[A-Za-z0-9\s]+$/', $name)) {
            return redirect()->back()->withInput()->with('errors', [
                'name' => 'Name can only contain letters, numbers, and spaces. Special characters are not allowed.'
            ]);
        }

        $this->userModel->insert([
            'name'       => $name,
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'role_id'    => (int)$this->request->getPost('role_id'),
            'is_active'  => $this->request->getPost('is_active') ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Log audit
        $userId = session()->get('user_id');
        Audit::log($userId, 'CREATE', 'User', 'Created user: ' . esc($name) . ' (' . esc($this->request->getPost('email')) . ')');

        return redirect()->to('/users')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
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
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
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
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role_id' => (int)$roleId,
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
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
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
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
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
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
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
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
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
