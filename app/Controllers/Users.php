<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Show create user form
     */
    public function create()
    {
        // Check if user is Super Admin
        $roleName = session()->get('role_name');
        if ($roleName !== 'Super Admin' && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Super Admin only.');
        }
        
        return view('auth/create_user');
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
        
        $data['users'] = $this->userModel->getAllUsers();
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
            'name'           => 'required|min_length[3]|max_length[100]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'password'       => 'required|min_length[6]|max_length[255]',
            'confirm_password' => 'required|matches[password]',
            'role_id'        => 'required|in_list[1,2,3,4]',
            'is_active'      => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'role_id'    => $this->request->getPost('role_id'),
            'is_active'  => $this->request->getPost('is_active'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

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
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $roleId = $this->request->getPost('role_id');
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

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Invalid email format'
                ]);
            }

            if (!in_array($roleId, ['1', '2', '3', '4'])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Invalid role'
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
                'role_id' => $roleId,
                'is_active' => (int)$isActive,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Only update password if provided
            if (!empty($password) && is_string($password) && strlen(trim($password)) >= 6) {
                $updateData['password'] = trim($password);
            }

            // Update in database
            $result = $this->userModel->update($id, $updateData);

            if ($result) {
                return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
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
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User activated successfully',
                    'status' => 'ACTIVE'
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
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User deactivated successfully',
                    'status' => 'INACTIVE'
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
}
