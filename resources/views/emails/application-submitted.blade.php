<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - JBI University</title>
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
        .success-badge {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .application-details {
            background-color: #f8f9fa;
            padding: 20px;
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
        .next-steps {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">JBI University</div>
            <div class="success-badge">Application Submitted</div>
            <h2>Thank You for Your Application!</h2>
        </div>

        <p>Dear {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>We have successfully received your application to JBI University. Thank you for choosing us for your educational journey!</p>

        <div class="application-details">
            <h3>📋 Application Details:</h3>
            <ul>
                <li><strong>Application Number:</strong> {{ $applicationNumber }}</li>
                <li><strong>Applicant Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</li>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Role:</strong> {{ ucfirst($user->role) }}</li>
                @if($user->studentProfile)
                <li><strong>Program:</strong> {{ $user->studentProfile->program }}</li>
                <li><strong>Department:</strong> {{ $user->studentProfile->department->name ?? 'N/A' }}</li>
                <li><strong>Admission Number:</strong> {{ $user->studentProfile->admission_number }}</li>
                @endif
                @if($user->facultyProfile)
                <li><strong>Position:</strong> {{ $user->facultyProfile->position }}</li>
                <li><strong>Department:</strong> {{ $user->facultyProfile->department->name ?? 'N/A' }}</li>
                <li><strong>Employee ID:</strong> {{ $user->facultyProfile->employee_id }}</li>
                @endif
                <li><strong>Submission Date:</strong> {{ $user->created_at->format('F j, Y \a\t g:i A') }}</li>
            </ul>
        </div>

        <div class="next-steps">
            <h3>🚀 What's Next?</h3>
            <ol>
                <li><strong>Email Verification:</strong> Please check your email for a verification link and click it to verify your email address.</li>
                <li><strong>Application Review:</strong> Our admissions team will review your application within 5-7 business days.</li>
                <li><strong>Status Updates:</strong> You will receive email notifications about any status changes to your application.</li>
                <li><strong>Interview (if required):</strong> Some programs may require an interview. We'll contact you if this applies to your application.</li>
            </ol>
        </div>

        <p><strong>📌 Important:</strong> Please keep your application number (<strong>{{ $applicationNumber }}</strong>) for your records. You may need it for future correspondence.</p>

        <p>If you have any questions about your application or the admissions process, contact our admissions office at <a href="mailto:admission@jbiuniversity.com">admission@jbiuniversity.com</a> or WhatsApp +27 68 443 8415.</p>

        <p>Best regards,<br>
        <strong>JBI University Admissions Team</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>JBI University | South Africa | www.jbiuniversity.com</p>
        </div>
    </div>
</body>
</html>
