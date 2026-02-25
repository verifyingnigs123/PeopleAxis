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

        // Find user by email (only active users can login)
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

        // Create session
        session()->set([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'name'      => $user->name,
            'role'      => $user->role,
            'logged_in' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user->name);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'You have been logged out');
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
