<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #17D27C, #5E2DFA);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .verification-code {
            background: #fff;
            border: 2px solid #5E2DFA;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #5E2DFA;
            letter-spacing: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Email Verification</h1>
        <p>Medojob</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $name }},</h2>
        
        <p>Thank you for registering with Medojob! To complete your registration, please use the verification code below:</p>
        
        <div class="verification-code">
            <p><strong>Your verification code is:</strong></p>
            <div class="code">{{ $verification_code }}</div>
            <p><small>This code will expire on {{ $expires_at }}</small></p>
        </div>
        
        <p>Enter this code in the mobile app to verify your email address and complete your registration.</p>
        
        <div class="warning">
            <strong>Important:</strong> This code is valid for 30 minutes only. If you don't verify your email within this time, you'll need to request a new code.
        </div>
        
        <p>If you didn't create an account with Medojob, please ignore this email.</p>
        
        <p>Best regards,<br>
        The Medojob Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
