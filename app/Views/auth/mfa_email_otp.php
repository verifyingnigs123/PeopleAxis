<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 20px;
            color: #333;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .otp-box {
            background-color: #f9f9f9;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background-color: #5568d3;
        }
        .divider {
            border-top: 1px solid #eee;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Login Verification</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hi <?= $userName ?? 'User'; ?>,</p>
            </div>

            <p>We've received a sign-in request for your PeopleAxis account. To complete your login, please enter the verification code below:</p>

            <div class="otp-box">
                <div class="otp-label">Your verification code is:</div>
                <div class="otp-code"><?= $otp; ?></div>
            </div>

            <p><strong>This verification code is usable for 10 minutes only.</strong></p>

            <div class="warning">
                <strong>⚠️ Security Warning:</strong> If you didn't try to log in, your password may be compromised. Please change your password immediately or contact our support team.
            </div>

            <div class="divider"></div>

            <p style="color: #666; font-size: 14px;">
                <strong>For your security:</strong>
            </p>
            <ul style="color: #666; font-size: 14px;">
                <li>Never share this code with anyone</li>
                <li>PeopleAxis will never ask for your verification code via email or phone</li>
                <li>This is a time-sensitive verification code</li>
            </ul>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
            <p>&copy; 2026 PeopleAxis. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
