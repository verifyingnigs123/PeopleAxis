<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $userModel;
    private const LOGIN_MAX_ATTEMPTS = 3;
    private const LOGIN_LOCKOUT_MINUTES = 15;
    private const LOGIN_ATTEMPT_KEY_PREFIX = 'login_failed_attempts_';
    private const LOGIN_LOCKOUT_KEY_PREFIX = 'login_locked_until_';

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
        $loginKey = $this->getLoginThrottleKey($login);

        $lockoutMessage = $this->getLoginLockoutMessage($loginKey);
        if ($lockoutMessage !== null) {
            return redirect()->to('/login')->with('error', $lockoutMessage);
        }

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
            return $this->handleFailedLogin($loginKey, 'Account not found. Please check your email/username.');
        }

        // Check if user is inactive
        if ($anyUser->is_active == 0) {
            return $this->handleFailedLogin($loginKey, 'Your account is deactivated. Please contact an administrator.');
        }

        // Get active user
        $user = $this->userModel->getUserByEmail($login);

        if (!$user) {
            return $this->handleFailedLogin($loginKey, 'User account is not active.');
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
            return $this->handleFailedLogin($loginKey, 'Invalid email or password');
        }

        $this->clearLoginAttempts($loginKey);

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
        $this->clearLoginAttempts();
        session()->destroy();
        return redirect()->to('/')->with('success', 'You have been logged out');
    }

    public function sessionStatus()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success'      => false,
                'force_logout' => true,
                'message'      => 'Your session has ended. Please sign in again.',
            ])->setStatusCode(401);
        }

        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            session()->destroy();

            return $this->response->setJSON([
                'success'      => false,
                'force_logout' => true,
                'message'      => 'Your session is invalid. Please sign in again.',
            ])->setStatusCode(401);
        }

        $user = $this->userModel->select('id, is_active, deleted_at')->find($userId);

        if (! $user || (int) $user->is_active !== 1 || ! empty($user->deleted_at)) {
            session()->destroy();

            return $this->response->setJSON([
                'success'      => false,
                'force_logout' => true,
                'message'      => 'Your account has been removed or deactivated. Please sign in again.',
            ])->setStatusCode(401);
        }

        return $this->response->setJSON([
            'success' => true,
            'active'  => true,
        ]);
    }

    private function getLoginThrottleKey(string $login): string
    {
        return hash('sha256', strtolower(trim($login)));
    }

    private function getLoginAttemptKey(string $loginKey): string
    {
        return self::LOGIN_ATTEMPT_KEY_PREFIX . $loginKey;
    }

    private function getLoginLockoutKey(string $loginKey): string
    {
        return self::LOGIN_LOCKOUT_KEY_PREFIX . $loginKey;
    }

    private function getLoginLockoutMessage(string $loginKey): ?string
    {
        $lockedUntil = (int) session()->getTempdata($this->getLoginLockoutKey($loginKey));

        if ($lockedUntil <= 0) {
            return null;
        }

        $remainingSeconds = $lockedUntil - time();
        if ($remainingSeconds <= 0) {
            $this->clearLoginAttempts();
            return null;
        }

        $remainingMinutes = max(1, (int) ceil($remainingSeconds / 60));

        return 'Too many failed login attempts. Please try again in ' . $remainingMinutes . ' minute' . ($remainingMinutes === 1 ? '' : 's') . '.';
    }

    private function handleFailedLogin(string $loginKey, string $message)
    {
        $attempts = (int) session()->get($this->getLoginAttemptKey($loginKey));
        $attempts++;

        if ($attempts >= self::LOGIN_MAX_ATTEMPTS) {
            $this->clearLoginAttempts($loginKey);
            session()->setTempdata(
                $this->getLoginLockoutKey($loginKey),
                time() + (self::LOGIN_LOCKOUT_MINUTES * 60),
                self::LOGIN_LOCKOUT_MINUTES * 60
            );

            return redirect()->to('/login')->with(
                'error',
                'Too many failed login attempts. Please try again in ' . self::LOGIN_LOCKOUT_MINUTES . ' minutes.'
            );
        }

        session()->set($this->getLoginAttemptKey($loginKey), $attempts);

        return redirect()->to('/login')->with('error', $message);
    }

    private function clearLoginAttempts(?string $loginKey = null): void
    {
        if ($loginKey === null) {
            foreach (session()->get() as $key => $value) {
                if (is_string($key) && str_starts_with($key, self::LOGIN_ATTEMPT_KEY_PREFIX)) {
                    session()->remove($key);
                }

                if (is_string($key) && str_starts_with($key, self::LOGIN_LOCKOUT_KEY_PREFIX)) {
                    session()->remove($key);
                }
            }

            return;
        }

        session()->remove($this->getLoginAttemptKey($loginKey));
        session()->remove($this->getLoginLockoutKey($loginKey));
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

    // Email sending method removed - using CodeIgniter Email service instead

    private function sendOtpEmail(string $to, string $htmlBody)
    {
        try {
            $emailConfig = new \Config\Email();
            $emailService = \Config\Services::email();
            
            $emailService->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $emailService->setTo($to);
            $emailService->setSubject('Your Password Reset OTP - PeopleAxis');
            $emailService->setMessage($htmlBody);
            $emailService->setAltMessage(strip_tags($htmlBody));
            
            if ($emailService->send()) {
                log_message('info', '[sendOtpEmail] Email sent successfully to ' . $to);
                return true;
            } else {
                $error = $emailService->printDebugger();
                log_message('error', '[sendOtpEmail] Failed to send OTP email to [' . $to . ']: ' . $error);
                return 'Failed to send email. Please try again later.';
            }
        } catch (\Exception $e) {
            log_message('error', '[sendOtpEmail] Exception: ' . $e->getMessage());
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
        $otp = trim($this->request->getPost('otp'));

        // Validate inputs
        if (!$email) {
            return redirect()->to('/forgot-password')->with('error', 'Session expired. Please start the password reset process again.');
        }

        if (empty($otp)) {
            return redirect()->to('/verify-otp')->with('error', 'OTP is required');
        }

        log_message('info', '[verifyOtpProcess] Verifying OTP for email: ' . $email . ', OTP: ' . $otp);

        // Verify OTP
        $otpModel = model('OtpModel');
        $otpRecord = $otpModel->verifyOtp($email, $otp);

        if (!$otpRecord) {
            log_message('warning', '[verifyOtpProcess] Invalid or expired OTP for email: ' . $email);
            return redirect()->to('/verify-otp')->with('error', 'Invalid or expired OTP. Please try again or request a new OTP.');
        }

        log_message('info', '[verifyOtpProcess] OTP verified successfully for email: ' . $email);

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
