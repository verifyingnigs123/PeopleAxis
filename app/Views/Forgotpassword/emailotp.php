<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP - PeopleAxis</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f9;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#2c3e50 0%,#3498db 100%);padding:40px 40px 30px;text-align:center;">
                            <div style="font-size:48px;margin-bottom:12px;">🔐</div>
                            <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:-0.5px;">PeopleAxis</h1>
                            <p style="margin:6px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">HR Management System</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">

                            <p style="margin:0 0 8px;font-size:16px;color:#2c3e50;">Hello, <strong><?= esc($userName ?? 'User') ?></strong> 👋</p>
                            <p style="margin:0 0 28px;font-size:15px;color:#555f6e;line-height:1.6;">
                                We received a request to reset the password for your <strong>PeopleAxis</strong> account.
                                Use the OTP below to proceed. This code is valid for <strong>10 minutes</strong>.
                            </p>

                            <!-- OTP Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                                <tr>
                                    <td align="center">
                                        <div style="background:linear-gradient(135deg,#eaf4fb 0%,#d6eaf8 100%);border:2px dashed #3498db;border-radius:12px;padding:28px 20px;display:inline-block;min-width:220px;">
                                            <p style="margin:0 0 8px;font-size:13px;color:#7f8c8d;text-transform:uppercase;letter-spacing:1.5px;font-weight:600;">Your OTP Code</p>
                                            <div style="font-size:42px;font-weight:800;letter-spacing:12px;color:#2c3e50;font-family:'Courier New',monospace;">
                                                <?= esc($otp ?? '------') ?>
                                            </div>
                                            <p style="margin:10px 0 0;font-size:12px;color:#e74c3c;font-weight:600;">
                                                ⏰ Expires in 10 minutes
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Steps -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8f9fa;border-radius:8px;padding:20px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:0 20px;">
                                        <p style="margin:0 0 12px;font-size:13px;font-weight:700;color:#2c3e50;text-transform:uppercase;letter-spacing:0.5px;">How to reset your password:</p>
                                        <p style="margin:0 0 8px;font-size:14px;color:#555f6e;">
                                            <span style="background:#3498db;color:#fff;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:8px;">1</span>
                                            Go to the OTP verification page
                                        </p>
                                        <p style="margin:0 0 8px;font-size:14px;color:#555f6e;">
                                            <span style="background:#3498db;color:#fff;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:8px;">2</span>
                                            Enter the 6-digit OTP code above
                                        </p>
                                        <p style="margin:0;font-size:14px;color:#555f6e;">
                                            <span style="background:#3498db;color:#fff;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:8px;">3</span>
                                            Set your new password
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff9e6;border-left:4px solid #f39c12;border-radius:6px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <p style="margin:0;font-size:13px;color:#7d5a00;">
                                            <strong>⚠️ Security Notice:</strong> If you did not request this password reset, please ignore this email.
                                            Your account remains secure and no changes have been made.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:14px;color:#7f8c8d;line-height:1.6;">
                                If you have any issues, please contact our support team.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8f9fa;border-top:1px solid #dee2e6;padding:24px 40px;text-align:center;">
                            <p style="margin:0 0 6px;font-size:14px;color:#2c3e50;font-weight:600;">PeopleAxis HR Management</p>
                            <p style="margin:0;font-size:12px;color:#aab2bd;">
                                This is an automated message. Please do not reply to this email.
                            </p>
                            <p style="margin:8px 0 0;font-size:11px;color:#ccc;">
                                &copy; <?= date('Y') ?> PeopleAxis. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
