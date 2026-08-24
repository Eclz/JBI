<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Approved - JBI University</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b5bdb;
            margin-bottom: 10px;
        }
        .approval-badge {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        .credentials-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .password-display {
            background-color: #f8f9fa;
            border: 2px dashed #6c757d;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            margin: 10px 0;
        }
        .login-button {
            display: inline-block;
            background-color: #3b5bdb;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .login-button:hover {
            background-color: #2c4bc6;
        }
        .important-notice {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">JBI University</div>
            <div class="approval-badge">🎉 ADMISSION APPROVED! 🎉</div>
            <h2>Welcome to JBI University!</h2>
        </div>

        <p>Dear {{ $user->first_name }} {{ $user->last_name }},</p>

        <p><strong>Congratulations!</strong> We are delighted to inform you that your application to JBI University has been <strong>APPROVED</strong>. Welcome to our academic community!</p>

        @if($user->studentProfile)
        <p>You have been admitted to the <strong>{{ $user->studentProfile->program }}</strong> program in the <strong>{{ $user->studentProfile->department->name ?? 'N/A' }}</strong> department.</p>
        @endif

        @if($user->facultyProfile)
        <p>You have been approved for the position of <strong>{{ $user->facultyProfile->position }}</strong> in the <strong>{{ $user->facultyProfile->department->name ?? 'N/A' }}</strong> department.</p>
        @endif

        <div class="credentials-box">
            <h3>🔐 Your Login Credentials</h3>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Temporary Password:</strong></p>
            <div class="password-display">{{ $defaultPassword }}</div>
            <p><small><em>Please keep this password secure and change it immediately after your first login.</em></small></p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="login-button">Login to Your Account</a>
        </div>

        <div class="important-notice">
            <h4>🚨 IMPORTANT SECURITY NOTICE:</h4>
            <ul>
                <li>You <strong>MUST</strong> change your password on your first login</li>
                <li>Do not share your login credentials with anyone</li>
                <li>Use a strong, unique password for your account</li>
                <li>Log out completely when using shared computers</li>
            </ul>
        </div>

        <h3>📚 Next Steps:</h3>
        <ol>
            <li><strong>Login:</strong> Use the credentials above to access your account</li>
            <li><strong>Change Password:</strong> You'll be prompted to create a new password</li>
            <li><strong>Complete Profile:</strong> Update your profile information if needed</li>
            @if($user->isStudent())
            <li><strong>Course Registration:</strong> Register for your courses for the upcoming semester</li>
            <li><strong>Orientation:</strong> Attend the new student orientation session</li>
            @endif
            @if($user->isFaculty())
            <li><strong>Department Meeting:</strong> Contact your department head for onboarding</li>
            <li><strong>Course Assignment:</strong> Review your teaching assignments</li>
            @endif
        </ol>

        <p>If you encounter any issues logging in or have questions about your admission, please contact:</p>
        <ul>
            <li><strong>Email:</strong> <a href="mailto:info@jbiuniversity.com">info@jbiuniversity.com</a></li>
            <li><strong>WhatsApp:</strong> +27 68 443 8415</li>
            <li><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</li>
        </ul>

        <p>Once again, congratulations on your admission to JBI University. We look forward to your contributions to our academic community!</p>

        <p>Best regards,<br>
        <strong>JBI University Admissions Committee</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>JBI University | South Africa | www.jbiuniversity.com</p>
            <p>WhatsApp: +27 68 443 8415 | Email: info@jbiuniversity.com</p>
        </div>
    </div>
</body>
</html>
