<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
    }

    public function login(): string
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($email) || empty($password)) {
            return redirect()->to('/login')->with('error', 'Email and password are required');
        }

        // Find user by email
        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            // Check if user exists but is inactive
            $inactiveUser = $this->userModel->where('email', $email)->first();
            if ($inactiveUser && $inactiveUser->is_active == 0) {
                return redirect()->to('/login')->with('error', 'Your account is deactivated. Please contact an administrator.');
            }
            return redirect()->to('/login')->with('error', 'Email not found');
        }

        // Verify password
        if (!$this->userModel->verifyPassword($password, $user->password)) {
            return redirect()->to('/login')->with('error', 'Incorrect password');
        }

        // Determine role name
        $roleName = $this->userModel->getRoleName($user->id) ?? ($user->role ?? '');

        // Map human role to short slug for existing views
        $roleSlug = 'user';
        switch ($roleName) {
            case 'Super Admin':
                $roleSlug = 'admin';
                break;
            case 'HR Admin':
                $roleSlug = 'hr';
                break;
            case 'Manager':
                $roleSlug = 'manager';
                break;
            default:
                $roleSlug = 'user';
        }

        // Create session
        session()->set([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'username'  => $user->username ?? null,
            'name'      => $user->name,
            'role_id'   => $user->role_id ?? null,
            'role_name' => $roleName,
            'role'      => $roleSlug,
            'logged_in' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user->name);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'You have been logged out');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username') ?: null;
        $password = $this->request->getPost('password');
        $name = $this->request->getPost('name');

        // Default to Employee role
        $db = \Config\Database::connect();
        $role = $db->table('roles')->where('name', 'Employee')->get()->getRow();

        $data = [
            'email'    => $email,
            'username' => $username,
            'password' => $password,
            'name'     => $name,
            'role_id'  => $role->id ?? null,
        ];

        if ($this->userModel->save($data)) {
            return redirect()->to('/login')->with('success', 'Registration successful! Please log in');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }

    public function forgotPassword()
    {
        return view('auth/forgot-password');
    }

    public function forgotPasswordProcess()
    {
        // TODO: Implement password reset logic
        return redirect()->to('/login');
    }
}
