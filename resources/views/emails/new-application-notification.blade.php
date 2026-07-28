<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Application Received - JBI University</title>
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
            border-bottom: 2px solid #dc3545;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b5bdb;
            margin-bottom: 10px;
        }
        .alert-badge {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .applicant-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .review-button {
            display: inline-block;
            background-color: #3b5bdb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .review-button:hover {
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
            <div class="alert-badge">New Application</div>
            <h2>Application Requires Review</h2>
        </div>

        <p>Dear Administrator,</p>

        <p>A new application has been submitted and requires your review. Please find the applicant details below:</p>

        <div class="applicant-details">
            <h3>Applicant Information:</h3>
            <ul>
                <li><strong>Application Number:</strong> {{ $application->application_number }}</li>
                <li><strong>Name:</strong> {{ $application->full_name }}</li>
                <li><strong>Email:</strong> {{ $application->email }}</li>
                <li><strong>Phone:</strong> {{ $application->phone }}</li>
                <li><strong>Application Type:</strong> {{ $application->type_label }}</li>
                @if($application->type === 'student')
                <li><strong>Program:</strong> {{ $application->program }}</li>
                <li><strong>Previous School:</strong> {{ $application->previous_school ?? 'N/A' }}</li>
                <li><strong>Previous GPA:</strong> {{ $application->previous_gpa ?? 'N/A' }}</li>
                @endif
                @if($application->type === 'faculty')
                <li><strong>Position:</strong> {{ $application->position }}</li>
                <li><strong>Department:</strong> {{ $application->department }}</li>
                <li><strong>Highest Degree:</strong> {{ $application->highest_degree ?? 'N/A' }}</li>
                <li><strong>Experience:</strong> {{ $application->years_of_experience ?? 0 }} years</li>
                @endif
                <li><strong>Application Date:</strong> {{ $application->created_at->format('F j, Y \a\t g:i A') }}</li>
            </ul>
        </div>

        <p><strong>Action Required:</strong> Please review this application and either approve or reject it through the admin dashboard.</p>

        <div style="text-align: center;">
            <a href="{{ route('admin.applications.show', $application) }}" class="review-button">Review Application</a>
        </div>

        <p>You can access the full application details, documents, and make approval decisions through the admin panel.</p>

        <p>Best regards,<br>
        <strong>JBI University System</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>This is an automated notification from the JBI University Management System.</p>
        </div>
    </div>
</body>
</html>
