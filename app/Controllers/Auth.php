<?php

namespace App\Controllers;

use App\Controllers\Audit;

class Auth extends BaseController
{
    protected $userModel;
    protected $loginAttemptModel;
    private const LOGIN_MAX_ATTEMPTS = 3;
    private const LOGIN_LOCKOUT_MINUTES = 15;
    private const LOGIN_ATTEMPT_KEY_PREFIX = 'login_failed_attempts_';
    private const LOGIN_LOCKOUT_KEY_PREFIX = 'login_locked_until_';
    private const TRUSTED_DEVICE_COOKIE = 'device_token';

    public function __construct()
    {
        $this->userModel          = model('UserModel');
        $this->loginAttemptModel  = new \App\Models\LoginAttemptModel();
    }

    /**
     * Safely write a record to the audit_logs table.
     * Uses AuditModel directly so it works regardless of helper loading order.
     * Never throws — any failure is only logged to the CI4 error log.
     */
    private function writeAuditLog(?int $userId, string $action, string $description = ''): void
    {
        try {
            $auditModel = new \App\Models\AuditModel();
            $auditModel->log($userId, $action, $description);
        } catch (\Throwable $e) {
            log_message('error', '[Auth] Audit log failed (' . $action . '): ' . $e->getMessage());
        }
    }

    private function isTrustedDevice(int $userId): bool
    {
        $deviceToken = $this->request->getCookie(self::TRUSTED_DEVICE_COOKIE);

        if (empty($deviceToken)) {
            return false;
        }

        try {
            $deviceTokenModel = model('DeviceTokenModel');
            if (! $deviceTokenModel) {
                return false;
            }

            return (bool) $deviceTokenModel->verifyDeviceToken($userId, (string) $deviceToken);
        } catch (\Throwable $e) {
            log_message('error', '[Auth] Trusted device check failed: ' . $e->getMessage());
            return false;
        }
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
            return $this->handleFailedLogin($loginKey, $login, 'Account not found. Please check your email/username.');
        }

        // Check if user is inactive
        if ($anyUser->is_active == 0) {
            return $this->handleFailedLogin($loginKey, $login, 'Your account is deactivated. Please contact an administrator.');
        }

        // Get active user
        $user = $this->userModel->getUserByEmail($login);

        if (!$user) {
            return $this->handleFailedLogin($loginKey, $login, 'User account is not active.');
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
            return $this->handleFailedLogin($loginKey, $login, 'Invalid email or password');
        }

        $this->clearLoginAttempts($loginKey);

        // Check if user has MFA enabled
        if ($user->mfa_enabled == 1) {
            // Skip MFA for a trusted device with a valid, unexpired token
            if ($this->isTrustedDevice((int) $user->id)) {
                $roleName = $this->userModel->getRoleName($user->id) ?? '';
                $storedRoleSlug = strtolower((string) ($user->role ?? ''));

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

                $ipAddress = $this->request->getIPAddress();
                $userAgent = $this->request->getUserAgent() ? $this->request->getUserAgent()->getAgentString() : 'Unknown';
                $loginDesc  = "User ({$roleName}) logged in with trusted device from IP: {$ipAddress} | Browser: {$userAgent}";
                $this->writeAuditLog((int) $user->id, 'Login', $loginDesc);

                $this->loginAttemptModel->record(
                    'success',
                    (int) $user->id,
                    $user->email,
                    $roleName,
                    null,
                    $ipAddress,
                    $userAgent
                );

                return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user->name);
            }

            // Store user info temporarily for MFA verification
            session()->set([
                'mfa_pending_user_id'   => $user->id,
                'mfa_pending_email'     => $user->email,
                'mfa_pending_name'      => $user->name,
                'mfa_pending_role_id'   => $user->role_id ?? null,
                'mfa_pending_role'      => $user->role ?? 'user',
            ]);

            // Generate and send MFA OTP
            $otpModel = model('OtpModel');
            $otp = $otpModel->generateOtp($user->email, 'login', $user->id);
            $otpExpiresAt = time() + (10 * 60);

            // Send MFA OTP email (login verification)
            $emailBody = view('auth/mfa_email_otp', [
                'otp'      => $otp,
                'userName' => $user->name,
            ]);

            $this->sendOtpEmail($user->email, $emailBody, 'Your Login Verification OTP - PeopleAxis');

            session()->set('mfa_otp_expires_at', $otpExpiresAt);

            log_message('info', '[loginProcess] MFA enabled for user: ' . $user->email . '. OTP sent.');
            return redirect()->to('/verify-mfa-login')->with('success', 'OTP has been sent to your email. Please verify to complete login.');
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

        // Record login in audit logs + login_attempts table
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent() ? $this->request->getUserAgent()->getAgentString() : 'Unknown';
        $loginDesc  = "User ({$roleName}) logged in from IP: {$ipAddress} | Browser: {$userAgent}";
        $this->writeAuditLog((int) $user->id, 'Login', $loginDesc);

        // Record successful attempt in login_attempts table
        $this->loginAttemptModel->record(
            'success',
            (int) $user->id,
            $user->email,
            $roleName,
            null,
            $ipAddress,
            $userAgent
        );

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user->name);
    }

    public function logout()
    {
        try {
            $userId = session()->get('user_id');

            // Record logout in audit logs BEFORE destroying the session
            if ($userId) {
                $userName = session()->get('name') ?? 'Unknown';
                $roleName = session()->get('role_name') ?? session()->get('role') ?? 'Unknown';
                $this->writeAuditLog((int) $userId, 'Logout', "User ({$roleName}) '{$userName}' logged out.");
            }

            // Clear all session data
            session()->remove(['user_id', 'email', 'username', 'name', 'role_id', 'role_name', 'role', 'logged_in']);

            // Clear login attempts
            $this->clearLoginAttempts(null);

            // Destroy session
            session()->destroy();

            return redirect()->to('/')->with('success', 'You have been logged out');
        } catch (\Throwable $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
            // Even if there's an error, safely destroy session and redirect
            try {
                session()->destroy();
            } catch (\Throwable $destroyError) {
                log_message('error', 'Session destroy error: ' . $destroyError->getMessage());
            }
            return redirect()->to('/')->with('success', 'You have been logged out');
        }
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

    private function handleFailedLogin(string $loginKey, string $login, string $message)
    {
        $attempts = (int) session()->get($this->getLoginAttemptKey($loginKey));
        $attempts++;

        // Log failed login attempt
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent() ? $this->request->getUserAgent()->getAgentString() : 'Unknown';

        // Try to get user ID for logging
        $user = $this->userModel
            ->groupStart()
                ->where('email', $login)
                ->orWhere('username', $login)
            ->groupEnd()
            ->first();

        $userId    = $user ? (int) $user->id : null;
        $userType  = $user ? ($this->userModel->getRoleName($userId) ?? 'Unknown') : null;

        // Determine failure reason for the attempts log
        $reason = 'invalid_password';
        if (!$user) {
            $reason = 'user_not_found';
        } elseif ($user->is_active == 0) {
            $reason = 'account_inactive';
        }

        // Record failed attempt in login_attempts table
        $this->loginAttemptModel->record(
            'failed',
            $userId,
            $login,
            $userType,
            $reason,
            $ipAddress,
            $userAgent
        );

        // Write to audit log
        $this->writeAuditLog($userId, 'Failed Login',
            "Failed login for '{$login}' from IP: {$ipAddress}. Attempt {$attempts}/" . self::LOGIN_MAX_ATTEMPTS . ". Reason: {$reason}"
        );

        if ($attempts >= self::LOGIN_MAX_ATTEMPTS) {
            $this->clearLoginAttempts($loginKey);
            session()->setTempdata(
                $this->getLoginLockoutKey($loginKey),
                time() + (self::LOGIN_LOCKOUT_MINUTES * 60),
                self::LOGIN_LOCKOUT_MINUTES * 60
            );

            // Record lockout in audit log
            $this->writeAuditLog($userId, 'Account Locked',
                "Account '{$login}' locked for " . self::LOGIN_LOCKOUT_MINUTES . " min after " . self::LOGIN_MAX_ATTEMPTS . " failed attempts from IP: {$ipAddress}"
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
                if (is_string($key) && strncmp($key, self::LOGIN_ATTEMPT_KEY_PREFIX, strlen(self::LOGIN_ATTEMPT_KEY_PREFIX)) === 0) {
                    session()->remove($key);
                }

                if (is_string($key) && strncmp($key, self::LOGIN_LOCKOUT_KEY_PREFIX, strlen(self::LOGIN_LOCKOUT_KEY_PREFIX)) === 0) {
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
            // Log new user registration
            $ipAddress = $this->request->getIPAddress();
            $userId = $this->userModel->getInsertID();
            Audit::log($userId, 'User Registration', 'User', "New user registered from IP: {$ipAddress}");
            
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

        // Log password reset request
        $ipAddress = $this->request->getIPAddress();
        Audit::log($user->id, 'Password Reset Request', 'User', "Password reset requested from IP: {$ipAddress}");

        // Generate OTP
        $otpModel = model('OtpModel');
        $otp = $otpModel->generateOtp($email);

        // Load the HTML email body
        $emailBody = view('Forgotpassword/emailotp', [
            'otp'      => $otp,
            'userName' => $user->name,
        ]);

        // Send via PHPMailer
        $sent = $this->sendOtpEmail($email, $emailBody, 'Your Password Reset OTP - PeopleAxis');

        if ($sent === true) {
            session()->set('reset_email', $email);
            return redirect()->to('/verify-otp')->with('success', 'OTP has been sent to your email. Please check your inbox.');
        } else {
            log_message('error', 'Failed to send OTP email: ' . $sent);
            return redirect()->to('/forgot-password')->with('error', 'Failed to send OTP email. Please try again later.');
        }
    }

    // Email sending method removed - using CodeIgniter Email service instead

    private function sendOtpEmail(string $to, string $htmlBody, string $subject = null)
    {
        try {
            $emailConfig = new \Config\Email();
            $emailService = \Config\Services::email();
            
            $emailService->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $emailService->setTo($to);
            // Use provided subject when available, otherwise default to password reset
            $subjectToUse = $subject ?? 'Your Password Reset OTP - PeopleAxis';
            $emailService->setSubject($subjectToUse);
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
        $otpRecord = $otpModel->verifyOtp($email, $otp, 'password_reset');

        if (!$otpRecord) {
            log_message('warning', '[verifyOtpProcess] Invalid or expired OTP for email: ' . $email);
            
            // Log failed OTP verification
            $user = $this->userModel->getUserByEmail($email);
            $ipAddress = $this->request->getIPAddress();
            if ($user) {
                Audit::log($user->id, 'OTP Verification Failed', 'User', "Failed OTP verification from IP: {$ipAddress}");
            }
            
            return redirect()->to('/verify-otp')->with('error', 'Invalid or expired OTP. Please try again or request a new OTP.');
        }

        log_message('info', '[verifyOtpProcess] OTP verified successfully for email: ' . $email);

        // Log successful OTP verification
        $user = $this->userModel->getUserByEmail($email);
        $ipAddress = $this->request->getIPAddress();
        if ($user) {
            Audit::log($user->id, 'OTP Verified', 'User', "OTP verified successfully from IP: {$ipAddress}");
        }

        // OTP is valid, store in session and redirect to reset password
        session()->set('otp_verified', true);
        session()->set('otp_id', $otpRecord->id);

        return redirect()->to('/reset-password');
    }

    /**
     * Verify MFA Login - Shows form to enter OTP for login
     */
    public function verifyMfaLogin()
    {
        // Check if MFA is pending
        $userId = session()->get('mfa_pending_user_id');
        
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $cooldownUntil = (int) (session()->getTempdata('mfa_resend_cooldown_until') ?? 0);
        $resendCooldown = max(0, $cooldownUntil - time());
        $otpExpiresAt = (int) (session()->get('mfa_otp_expires_at') ?? 0);
        $otpCountdown = max(0, $otpExpiresAt - time());

        return view('auth/verify_mfa_login', [
            'user_id' => $userId,
            'resend_cooldown' => $resendCooldown,
            'otp_countdown' => $otpCountdown,
        ]);
    }

    /**
     * Resend MFA Login OTP code
     */
    public function resendMfaCode()
    {
        $userId = (int) (session()->get('mfa_pending_user_id') ?? 0);
        $email = (string) (session()->get('mfa_pending_email') ?? '');
        $name = (string) (session()->get('mfa_pending_name') ?? 'User');

        if ($userId <= 0 || $email === '') {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
        }

        // Prevent rapid resend spam
        $nextAllowedAt = (int) (session()->getTempdata('mfa_resend_cooldown_until') ?? 0);
        if ($nextAllowedAt > time()) {
            $remaining = max(1, $nextAllowedAt - time());
            return redirect()->to('/verify-mfa-login')->with('error', 'Please wait ' . $remaining . ' seconds before resending another code.');
        }

        try {
            $otpModel = model('OtpModel');
            $otp = $otpModel->generateOtp($email, 'login', $userId);
            $otpExpiresAt = time() + (10 * 60);

            $emailBody = view('auth/mfa_email_otp', [
                'otp'      => $otp,
                'userName' => $name,
            ]);

            $sent = $this->sendOtpEmail($email, $emailBody, 'Your Login Verification OTP - PeopleAxis');
            if ($sent !== true) {
                log_message('error', '[resendMfaCode] Failed to send MFA OTP: ' . $sent);
                return redirect()->to('/verify-mfa-login')->with('error', 'Failed to resend code. Please try again.');
            }

            session()->set('mfa_otp_expires_at', $otpExpiresAt);

            // 60-second cooldown between resend requests
            $cooldown = 60;
            session()->setTempdata('mfa_resend_cooldown_until', time() + $cooldown, $cooldown);

            return redirect()->to('/verify-mfa-login')->with('success', 'A new verification code has been sent to your email.');
        } catch (\Throwable $e) {
            log_message('error', '[resendMfaCode] Exception: ' . $e->getMessage());
            return redirect()->to('/verify-mfa-login')->with('error', 'Unable to resend code right now. Please try again.');
        }
    }

    /**
     * Verify MFA Login OTP Process
     */
    public function verifyLoginMfaProcess()
    {
        $userId = session()->get('mfa_pending_user_id');
        $email = session()->get('mfa_pending_email');
        $otp = trim($this->request->getPost('otp'));
        $rememberDevice = $this->request->getPost('remember_device') ? true : false;

        // Validate inputs
        if (!$userId || !$email) {
            return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
        }

        if (empty($otp)) {
            return redirect()->to('/verify-mfa-login')->with('error', 'OTP is required');
        }

        log_message('info', '[verifyLoginMfaProcess] Verifying login MFA OTP for user: ' . $userId);

        // Verify OTP
        $otpModel = model('OtpModel');
        $otpRecord = $otpModel->verifyOtp($email, $otp, 'login');

        if (!$otpRecord) {
            log_message('warning', '[verifyLoginMfaProcess] Invalid or expired MFA OTP for user: ' . $userId);
            $this->writeAuditLog($userId, 'MFA Verification Failed', "Failed MFA OTP verification from IP: " . $this->request->getIPAddress());
            
            return redirect()->to('/verify-mfa-login')->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // Mark OTP as used
        $otpModel->markAsUsed($otpRecord->id);

        log_message('info', '[verifyLoginMfaProcess] MFA OTP verified successfully for user: ' . $userId);

        // Get full user data for session
        $user = $this->userModel->find($userId);

        // Determine role name and slug
        $roleName = $this->userModel->getRoleName($user->id) ?? '';
        $storedRoleSlug = strtolower((string) ($user->role ?? ''));

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

        // Set full session
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

        // Clear MFA pending session data
        session()->remove(['mfa_pending_user_id', 'mfa_pending_email', 'mfa_pending_name', 'mfa_pending_role_id', 'mfa_pending_role', 'mfa_otp_expires_at']);

        // Handle device memory
        if ($rememberDevice) {
            $deviceTokenModel = model('DeviceTokenModel');
            $ipAddress = $this->request->getIPAddress();
            $userAgent = $this->request->getUserAgent() ? $this->request->getUserAgent()->getAgentString() : 'Unknown';
            
            // Generate a user-friendly device name
            $deviceName = 'Device - ' . date('M d, Y H:i');
            
            $deviceToken = $deviceTokenModel->createDeviceToken($userId, $deviceName, $ipAddress, $userAgent);
            
            // Store token in a secure cookie
            $cookieOptions = [
                'expires'  => time() + (30 * 24 * 60 * 60), // 30 days
                'httponly' => true,
                'secure'   => $this->request->isSecure(),
                'samesite' => 'Lax',
                'path'     => '/',
            ];

            setcookie(self::TRUSTED_DEVICE_COOKIE, $deviceToken, $cookieOptions);
            log_message('info', '[verifyLoginMfaProcess] Device token created for user: ' . $userId);
        }

        // Record successful login
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent() ? $this->request->getUserAgent()->getAgentString() : 'Unknown';
        $loginDesc = "User ({$roleName}) logged in with MFA from IP: {$ipAddress}";
        $this->writeAuditLog($userId, 'Login', $loginDesc);

        $this->loginAttemptModel->record(
            'success',
            $userId,
            $email,
            $roleName,
            null,
            $ipAddress,
            $userAgent
        );

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user->name);
    }

    /**
     * Enable MFA for user
     */
    public function enableMfa()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login');
        }

        // Update user to enable MFA
        $this->userModel->update($userId, [
            'mfa_enabled' => 1,
            'mfa_method'  => 'email',
        ]);

        $this->writeAuditLog($userId, 'MFA Enabled', 'User enabled MFA (email)');

        return redirect()->back()->with('success', 'Two-factor authentication has been enabled.');
    }

    /**
     * Disable MFA for user
     */
    public function disableMfa()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login');
        }

        // Update user to disable MFA
        $this->userModel->update($userId, [
            'mfa_enabled' => 0,
        ]);

        // Optionally revoke all remembered devices
        $deviceTokenModel = model('DeviceTokenModel');
        $deviceTokenModel->revokeAllDevices($userId);

        $this->writeAuditLog($userId, 'MFA Disabled', 'User disabled MFA and revoked all trusted devices');

        return redirect()->back()->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Get user's remembered devices
     */
    public function getDevices()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ])->setStatusCode(401);
        }

        $deviceTokenModel = model('DeviceTokenModel');
        $devices = $deviceTokenModel->getUserDevices($userId);

        return $this->response->setJSON([
            'success' => true,
            'devices' => $devices,
        ]);
    }

    /**
     * Revoke a trusted device
     */
    public function revokeDevice()
    {
        $userId = session()->get('user_id');
        $deviceId = $this->request->getPost('device_id');

        if (!$userId || !$deviceId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
            ])->setStatusCode(400);
        }

        $deviceTokenModel = model('DeviceTokenModel');
        $deviceTokenModel->revokeDevice($userId, $deviceId);

        $this->writeAuditLog($userId, 'Device Revoked', 'User revoked trusted device: ' . $deviceId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Device has been revoked',
        ]);
    }
}
