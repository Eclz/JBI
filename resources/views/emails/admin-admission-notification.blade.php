<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admitted Successfully - JBI University</title>
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
        .alert-badge {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
        .details-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .action-button {
            display: inline-block;
            background-color: #3b5bdb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .action-button:hover {
            background-color: #2c4bc6;
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
            <div class="alert-badge">Admission Activated</div>
            <h2>Student Admission Complete</h2>
        </div>

        <p>Dear Administrator,</p>

        <p>This is to notify you that the student admission workflow has been successfully completed for the following applicant:</p>

        <div class="details-box">
            <h3>Student Details:</h3>
            <ul>
                <li><strong>Name:</strong> {{ $student->full_name }}</li>
                <li><strong>Email:</strong> {{ $student->email }}</li>
                <li><strong>Phone:</strong> {{ $student->phone }}</li>
                <li><strong>Student ID:</strong> {{ $student->student_id }}</li>
                @if($application)
                <li><strong>Application ID:</strong> #{{ $application->application_number }}</li>
                <li><strong>Program:</strong> {{ $application->program }}</li>
                <li><strong>Admitted On:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</li>
                @endif
            </ul>
        </div>

        <p>The student's portal is now active, and they have been notified of their login credentials and admission letter.</p>

        <div style="text-align: center;">
            <a href="{{ $link }}" class="action-button">View Application Details</a>
        </div>

        <p>Best regards,<br>
        <strong>JBI University System</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>This is an automated notification from the JBI University Management System.</p>
        </div>
    </div>
</body>
</html>
