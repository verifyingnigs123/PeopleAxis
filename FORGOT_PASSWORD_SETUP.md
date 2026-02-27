# Forgot Password Feature Setup Guide

The forgot password feature has been successfully implemented with OTP verification. Follow these steps to complete the setup.

## Components Created

1. **Database Migration**: `2026_02_27_000001_CreateOtpTable.php`
   - Creates `otp` table to store temporary OTP codes
   - Stores email, OTP code, creation time, expiration time, and usage status

2. **Models**:
   - `OtpModel.php` - Manages OTP generation, verification, and cleanup

3. **Controller Methods** (Auth.php):
   - `forgotPassword()` - Display forgot password form
   - `forgotPasswordProcess()` - Generate OTP and send via email
   - `verifyOtp()` - Display OTP verification form
   - `verifyOtpProcess()` - Verify OTP and proceed to password reset
   - `resetPassword()` - Display reset password form
   - `resetPasswordProcess()` - Update user password

4. **Views**:
   - `auth/forgot-password.php` - Request password reset form
   - `auth/verify-otp.php` - OTP verification form
   - `auth/reset-password.php` - Reset password form
   - `auth/email-otp.php` - Email template for OTP

5. **Routes** (app/Config/Routes.php):
   ```
   /forgot-password          - GET/POST
   /verify-otp               - GET/POST
   /reset-password           - GET/POST
   ```

## Gmail SMTP Configuration

To enable email sending through Gmail:

### Step 1: Create a Gmail App Password

1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable 2-Step Verification if not already enabled
3. Go back to Security settings
4. Find "App passwords" option
5. Select "Mail" and "Windows Computer" (or your device)
6. Google will generate a 16-character app password
7. Copy this password

### Step 2: Update Email Configuration

Edit `app/Config/Email.php`:

```php
public string $fromEmail  = 'your-email@gmail.com';      // Your Gmail address
public string $fromName   = 'PeopleAxis';
public string $SMTPHost   = 'smtp.gmail.com';           // Don't change this
public string $SMTPUser   = 'your-email@gmail.com';     // Your Gmail address
public string $SMTPPass   = 'xxxx xxxx xxxx xxxx';      // The 16-char app password from Step 1
public int $SMTPPort      = 587;                         // Don't change this
public string $SMTPCrypto = 'tls';                       // Don't change this
public string $protocol   = 'smtp';                      // Don't change this
public string $mailType   = 'html';                      // Don't change this
```

### Step 3: Test the Feature

1. Start your application: `php spark serve`
2. Go to `/login` page
3. Click "Forgot Password?"
4. Enter your email address
5. You should receive an OTP via email
6. Enter the OTP to proceed with password reset
7. Create a new password and log in

## Feature Details

### OTP Specifications
- **Length**: 6 digits
- **Expiration**: 10 minutes from generation
- **Format**: Random numeric code
- **One-time use**: Each OTP can only be used once

### Password Reset Flow
1. User requests password reset → Enters email
2. System generates OTP → Sends via Gmail
3. User receives email with OTP
4. User enters OTP → Verified
5. User sets new password
6. OTP marked as used
7. User logs in with new password

### Security Features
- OTP expires after 10 minutes
- Each email can only have one active OTP
- Previous OTPs are deleted when a new one is generated
- OTP is marked as used after successful password reset
- Session validation at each step
- CSRF protection on all forms

### Email Features
- Professional HTML email template
- Clear instructions for users
- Security warnings in email
- Automatic signature with company name
- Responsive design

## Testing
To test without Gmail (development only):

1. Check the `writable/logs` directory for email content
2. Or enable debug mode to see email details in browser

## Troubleshooting

### "Failed to send OTP" error
- Check Email.php configuration
- Verify Gmail app password is correct (no spaces should be included when copying)
- Ensure 2-Step Verification is enabled on Gmail account
- Check firewall/antivirus isn't blocking SMTP port 587

### OTP not received
- Check spam/junk folder
- Verify sender email in Email.php matches Gmail account
- Check internet connection

### "Invalid or expired OTP" error
- OTP expires after 10 minutes
- Request a new OTP if expired
- Ensure entered OTP is exactly 6 digits

## Database
The migration has already been run. To manually check the OTP table:

```sql
SELECT * FROM otp;
```

To clean expired OTPs:

```php
$otpModel = model('OtpModel');
$otpModel->cleanExpiredOtps();
```

## Files Modified/Created

### Created:
- `app/Database/Migrations/2026_02_27_000001_CreateOtpTable.php`
- `app/Models/OtpModel.php`
- `app/Views/auth/forgot-password.php`
- `app/Views/auth/verify-otp.php`
- `app/Views/auth/reset-password.php`
- `app/Views/auth/email-otp.php`

### Modified:
- `app/Controllers/Auth.php`
- `app/Config/Email.php`
- `app/Config/Routes.php`

## Support
For issues or questions, please check the logs in `writable/logs/` directory.
