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

    public function index()
    {
        $data['users'] = $this->userModel->orderBy('created_at', 'DESC')->findAll();
        return view('auth/users', $data);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[255]',
            'role'     => 'required|in_list[admin,user]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'role'       => $this->request->getPost('role'),
            'is_active'  => $this->request->getPost('is_active') ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/users')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User not found");
        }
        $data['user'] = $user;
        return view('auth/edit_user', $data);
    }

    public function update($id)
    {
        try {
            // Find user
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'User not found']);
            }

            // Get form data
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $role = $this->request->getPost('role');
            $isActive = $this->request->getPost('is_active');
            $password = $this->request->getPost('password');

            // Basic validation
            if (!$name || !$email || !$role) {
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

            if ($role !== 'admin' && $role !== 'user') {
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
                'role' => $role,
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

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        $this->userModel->delete($id);
        return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
    }
}
