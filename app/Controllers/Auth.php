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
        // Basic validation
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $password_confirmation = $this->request->getPost('password_confirmation');

        $errors = [];
        if (empty($name) || strlen(trim($name)) < 3) {
            $errors[] = 'Name is required and must be at least 3 characters.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $password_confirmation) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Prepare user data but mark as inactive until verification
        $data = [
            'email'    => $email,
            'password' => $password,
            'name'     => $name,
            'role'     => 'user',
            'is_active'=> 0,
        ];

        if (! $this->userModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        // Generate verification code and store in session (short lived)
        $code = sprintf('%06d', random_int(0, 999999));
        session()->set('temp_verification_code', $code);
        session()->set('temp_email', $email);

        // Send verification email using SMTP config from environment
        $emailConfig = [
            'protocol'   => 'smtp',
            'SMTPHost'   => env('SMTP_HOST') ?: 'smtp.gmail.com',
            'SMTPUser'   => env('SMTP_USER') ?: '',
            'SMTPPass'   => env('SMTP_PASS') ?: '',
            'SMTPPort'   => env('SMTP_PORT') ? (int) env('SMTP_PORT') : 587,
            'SMTPCrypto' => env('SMTP_CRYPTO') ?: 'tls',
            'mailType'   => 'html',
            'charset'    => 'utf-8',
        ];

        $emailService = \Config\Services::email($emailConfig);
        $fromEmail = env('FROM_EMAIL') ?: ($this->request->getServer('SERVER_ADMIN') ?? 'no-reply@localhost');
        $fromName  = env('FROM_NAME') ?: 'PeopleAxis';

        $emailService->setFrom($fromEmail, $fromName);
        $emailService->setTo($email);
        $emailService->setSubject('PeopleAxis - Email Verification');

        $message = "<p>Hi " . esc($name) . ",</p>";
        $message .= "<p>Thanks for registering. Your verification code is: <strong>" . $code . "</strong></p>";
        $message .= "<p>Enter this code on the verification page to activate your account.</p>";

        $emailService->setMessage($message);

        try {
            $sent = $emailService->send();
        } catch (\Exception $e) {
            // If email fails, still proceed but show message
            $sent = false;
            log_message('error', 'Verification email failed: ' . $e->getMessage());
        }

        if (! $sent) {
            // Let user know email wasn't sent but account is created (inactive)
            return redirect()->to('/verify-email')->with('warning', 'Account created but verification email could not be sent. Contact admin.');
        }

        return redirect()->to('/verify-email')->with('success', 'Registration successful! Check your email for the verification code.');
    }

    /**
     * Show verification form
     */
    public function verifyEmail()
    {
        return view('auth/verify_email');
    }

    /**
     * Process verification code
     */
    public function verifyEmailProcess()
    {
        $code = $this->request->getPost('code');
        $sessCode = session()->get('temp_verification_code');
        $email = session()->get('temp_email');

        if (! $code || ! $sessCode || ! $email) {
            return redirect()->back()->with('error', 'Invalid verification attempt.');
        }

        if (trim($code) !== trim($sessCode)) {
            return redirect()->back()->with('error', 'Verification code is incorrect.');
        }

        // Activate user
        $user = $this->userModel->where('email', $email)->first();
        if (! $user) {
            return redirect()->to('/register')->with('error', 'User not found.');
        }

        $this->userModel->update($user->id, ['is_active' => 1]);

        // Clear temp session keys
        session()->remove('temp_verification_code');
        session()->remove('temp_email');

        return redirect()->to('/login')->with('success', 'Email verified! You can now log in.');
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
