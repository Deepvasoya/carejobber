<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #17D27C;
            margin-bottom: 10px;
        }
        .title {
            font-size: 28px;
            color: #1F2937;
            margin-bottom: 20px;
        }
        .code-container {
            background-color: #F0FDF4;
            border: 2px solid #17D27C;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .verification-code {
            font-size: 36px;
            font-weight: bold;
            color: #17D27C;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .expiry-info {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #17D27C;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Jobs Portal</div>
            <h1 class="title">Password Reset Code</h1>
        </div>

        <p>Hello <strong>{{ $name }}</strong>,</p>

        <p>You requested to reset your password for your Jobs Portal account. Use the verification code below to reset your password:</p>

        <div class="code-container">
            <p style="margin: 0 0 10px 0; color: #374151;">Your verification code is:</p>
            <div class="verification-code">{{ $code }}</div>
            <p style="margin: 10px 0 0 0; color: #6B7280; font-size: 14px;">Enter this code in the app to reset your password</p>
        </div>

        <div class="expiry-info">
            <strong>⏰ Important:</strong> This code will expire at <strong>{{ $expires_at }}</strong>. Please use it within 30 minutes.
        </div>

        <p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>

        <p>For security reasons:</p>
        <ul>
            <li>Never share this code with anyone</li>
            <li>Our team will never ask for this code</li>
            <li>This code can only be used once</li>
        </ul>

        <div class="footer">
            <p>Best regards,<br>
            <strong>Jobs Portal Team</strong></p>
            
            <p style="font-size: 12px; color: #9CA3AF;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
