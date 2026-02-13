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

    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        $data = [
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'name'     => $this->request->getPost('name'),
            'role'     => 'user', // Default role is user
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
