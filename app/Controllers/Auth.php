<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
        $login = trim((string) $this->request->getPost('email'));
        $password = $this->request->getPost('password');

        // Validate input
        if ($login === '' || empty($password)) {
            return redirect()->to('/login')->with('error', 'Email/Username and password are required');
        }

        // First check if user exists at all (including inactive)
        $anyUser = $this->userModel
            ->groupStart()
                ->where('email', $login)
                ->orWhere('username', $login)
            ->groupEnd()
            ->first();
        
        if (!$anyUser) {
            return redirect()->to('/login')->with('error', 'Account not found. Please check your email/username.');
        }

        // Check if user is inactive
        if ($anyUser->is_active == 0) {
            return redirect()->to('/login')->with('error', 'Your account is deactivated. Please contact an administrator.');
        }

        // Get active user
        $user = $this->userModel->getUserByEmail($login);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User account is not active.');
        }

        // Check if password is properly hashed and verify
        // If password is NOT bcrypt, rehash and try again
        $isBcrypt = (substr($user->password, 0, 4) === '$2y$' || substr($user->password, 0, 4) === '$2a$' || substr($user->password, 0, 4) === '$2b$');
        
        if (!$isBcrypt) {
            // Password is not bcrypt - rehash with user's input and update database
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $this->userModel->update($user->id, ['password' => $hashedPassword]);
            $user->password = $hashedPassword;
        }
        
        // Now verify the password
        $verified = $this->userModel->verifyPassword($password, $user->password);
        
        if (!$verified) {
            return redirect()->to('/login')->with('error', 'Invalid email or password');
        }

        // Determine role name and slug (role slug is source-of-truth when present).
        $roleName = $this->userModel->getRoleName($user->id) ?? '';
        $storedRoleSlug = strtolower((string) ($user->role ?? ''));

        // Map canonical DB role slugs into legacy session role values used by existing checks.
        $roleSlug = 'user';
        switch ($storedRoleSlug) {
            case 'super_admin':
                $roleSlug = 'admin';
                if ($roleName === '') {
                    $roleName = 'Super Admin';
                }
                break;
            case 'hr_admin':
                $roleSlug = 'hr';
                if ($roleName === '') {
                    $roleName = 'HR Admin';
                }
                break;
            case 'manager':
                $roleSlug = 'manager';
                if ($roleName === '') {
                    $roleName = 'Manager';
                }
                break;
            case 'employee':
                $roleSlug = 'user';
                if ($roleName === '') {
                    $roleName = 'Employee';
                }
                break;
            default:
                // Fallback for legacy records that only have role_id/role_name.
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
                        $roleName = $roleName !== '' ? $roleName : 'Employee';
                }
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
            'role'     => 'employee',
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
        return view('Forgotpassword/forgotpassword');
    }

    public function forgotPasswordProcess()
    {
        $email = $this->request->getPost('email');

        // Validate email
        if (empty($email)) {
            return redirect()->to('/forgot-password')->with('error', 'Email is required');
        }

        // Check if user exists
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user) {
            // Check if email exists but user is inactive
            $inactiveUser = $this->userModel->where('email', $email)->first();
            if ($inactiveUser && $inactiveUser->is_active == 0) {
                return redirect()->to('/forgot-password')->with('error', 'Your account is deactivated. Please contact an administrator.');
            }
            // For security reasons, don't reveal if email exists or not
            return redirect()->to('/forgot-password')->with('success', 'If your email exists in our system, you will receive an OTP shortly.');
        }

        // Generate OTP
        $otpModel = model('OtpModel');
        $otp = $otpModel->generateOtp($email);

        // Load the HTML email body
        $emailBody = view('Forgotpassword/emailotp', [
            'otp'      => $otp,
            'userName' => $user->name,
        ]);

        // Send via PHPMailer
        $sent = $this->sendOtpEmail($email, $emailBody);

        if ($sent === true) {
            session()->set('reset_email', $email);
            return redirect()->to('/verify-otp')->with('success', 'OTP has been sent to your email. Please check your inbox.');
        } else {
            log_message('error', 'Failed to send OTP email: ' . $sent);
            return redirect()->to('/forgot-password')->with('error', 'Failed to send OTP email. Please try again later.');
        }
    }

    // ---------------------------------------------------------------
    // PHPMailer helper
    // ---------------------------------------------------------------
    private function sendOtpEmail(string $to, string $htmlBody)
    {
        if (!class_exists(PHPMailer::class)) {
            return $this->sendOtpEmailFallback($to, $htmlBody);
        }

        $emailConfig = config('Email');
        $smtpHost = (string) ($emailConfig->SMTPHost ?? '');
        $smtpUser = (string) ($emailConfig->SMTPUser ?? '');
        $smtpPass = (string) ($emailConfig->SMTPPass ?? '');
        $smtpPort = (int) ($emailConfig->SMTPPort ?? 587);
        $smtpTimeout = (int) ($emailConfig->SMTPTimeout ?? 30);
        $smtpCrypto = strtolower(trim((string) ($emailConfig->SMTPCrypto ?? 'tls')));
        $fromEmail = (string) ($emailConfig->fromEmail ?? $smtpUser);
        $fromName = (string) ($emailConfig->fromName ?? 'PeopleAxis HR System');

        if ($smtpHost === '' || $smtpUser === '' || $smtpPass === '') {
            return 'SMTP configuration is incomplete (host/user/password missing).';
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

            // Fix SSL certificate verification issues common in XAMPP / local dev
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
            $mail->Subject  = 'Your Password Reset OTP - PeopleAxis';
            $mail->Body     = $htmlBody;
            $mail->AltBody  = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            $details = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
            log_message('error', '[sendOtpEmail] PHPMailer error to [' . $to . ']: ' . $details);
            return $details;
        }
    }

    private function sendOtpEmailFallback(string $to, string $htmlBody)
    {
        try {
            $email = service('email');
            $emailConfig = config('Email');

            $fromEmail = (string) ($emailConfig->fromEmail ?? 'no-reply@peopleaxis.local');
            $fromName = (string) ($emailConfig->fromName ?? 'PeopleAxis HR System');

            $email->setFrom($fromEmail, $fromName);
            $email->setTo($to);
            $email->setSubject('Your Password Reset OTP - PeopleAxis');
            $email->setMessage($htmlBody);

            if ($email->send()) {
                return true;
            }

            $debug = trim(strip_tags((string) $email->printDebugger(['headers', 'subject'])));
            $details = $debug !== '' ? $debug : 'Fallback mailer failed without details.';
            log_message('error', '[sendOtpEmailFallback] Email service error to [' . $to . ']: ' . $details);
            return $details;
        } catch (\Throwable $e) {
            log_message('error', '[sendOtpEmailFallback] Exception to [' . $to . ']: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function verifyOtp()
    {
        // Check if email is in session
        $email = session()->get('reset_email');
        
        if (!$email) {
            return redirect()->to('/forgot-password')->with('error', 'Please start the password reset process again.');
        }

        return view('Forgotpassword/verifyotp', ['email' => $email]);
    }

    public function verifyOtpProcess()
    {
        $email = session()->get('reset_email');
        $otp = $this->request->getPost('otp');

        // Validate inputs
        if (!$email) {
            return redirect()->to('/forgot-password')->with('error', 'Session expired. Please start the password reset process again.');
        }

        if (empty($otp)) {
            return redirect()->to('/verify-otp')->with('error', 'OTP is required');
        }

        // Verify OTP
        $otpModel = model('OtpModel');
        $otpRecord = $otpModel->verifyOtp($email, $otp);

        if (!$otpRecord) {
            return redirect()->to('/verify-otp')->with('error', 'Invalid or expired OTP. Please try again or request a new OTP.');
        }

        // OTP is valid, store in session and redirect to reset password
        session()->set('otp_verified', true);
        session()->set('otp_id', $otpRecord->id);

        return redirect()->to('/reset-password');
    }

    public function resetPassword()
    {
        // Check if OTP was verified
        $otpVerified = session()->get('otp_verified');
        $email = session()->get('reset_email');
        
        if (!$otpVerified || !$email) {
            return redirect()->to('/forgot-password')->with('error', 'Please complete the verification process first.');
        }

        return view('Forgotpassword/resetpassword', ['email' => $email]);
    }

    public function resetPasswordProcess()
    {
        // Check if OTP was verified
        $otpVerified = session()->get('otp_verified');
        $email = session()->get('reset_email');
        $otpId = session()->get('otp_id');
        
        if (!$otpVerified || !$email) {
            return redirect()->to('/forgot-password')->with('error', 'Please complete the verification process first.');
        }

        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // Validate inputs
        if (empty($password) || empty($passwordConfirm)) {
            return redirect()->to('/reset-password')->with('error', 'All fields are required');
        }

        if (strlen($password) < 6) {
            return redirect()->to('/reset-password')->with('error', 'Password must be at least 6 characters');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->to('/reset-password')->with('error', 'Passwords do not match');
        }

        // Find user by email and update password
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        // Update password
        $this->userModel->update($user->id, ['password' => $password]);

        // Mark OTP as used
        if ($otpId) {
            $otpModel = model('OtpModel');
            $otpModel->markAsUsed($otpId);
        }

        // Clear session
        session()->remove('reset_email');
        session()->remove('otp_verified');
        session()->remove('otp_id');

        return redirect()->to('/login')->with('success', 'Password has been reset successfully. Please log in with your new password.');
    }
}
