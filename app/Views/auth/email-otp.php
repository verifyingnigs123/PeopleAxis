<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP - PeopleAxis</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            background-color: #f9f9f9;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px 20px;
            background-color: white;
        }
        .email-greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .otp-section {
            background-color: #f0f4f8;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
        }
        .otp-expiry {
            color: #e74c3c;
            font-weight: bold;
            margin-top: 10px;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #dee2e6;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
        .social-links {
            text-align: center;
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #3498db;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🔐 PeopleAxis</h1>
            <p>Password Reset Request</p>
        </div>

        <div class="email-body">
            <div class="email-greeting">
                <p>Hello <?= htmlspecialchars($userName) ?>,</p>
                <p>We received a request to reset your PeopleAxis account password. Use the OTP code below to proceed:</p>
            </div>

            <div class="otp-section">
                <p style="margin: 0 0 10px 0; color: #7f8c8d;">Your One-Time Password (OTP)</p>
                <div class="otp-code"><?= htmlspecialchars($otp) ?></div>
                <div class="otp-expiry">This OTP will expire in 10 minutes</div>
            </div>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong>
                <p style="margin: 5px 0 0 0;">
                    Never share this OTP with anyone. PeopleAxis staff will never ask you for your OTP.
                </p>
            </div>

            <p>
                If you didn't request a password reset, please ignore this email and your account will remain secure. 
                Your password will not change without your OTP verification.
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <p>If you need further assistance, please contact our support team.</p>
            </div>
        </div>

        <div class="email-footer">
            <p style="margin: 0;">
                &copy; <?= date('Y') ?> PeopleAxis. All rights reserved.
            </p>
            <p style="margin: 5px 0 0 0;">
                This is an automated email, please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
