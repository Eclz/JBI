<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title ?? 'New Notification' }} - JBI University</title>
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
            border-bottom: 2px solid #3a7bd5;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3a7bd5;
            margin-bottom: 10px;
        }
        .alert-badge {
            background-color: #3a7bd5;
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
            border-left: 4px solid #3a7bd5;
        }
        .action-button {
            display: inline-block;
            background-color: #3a7bd5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .action-button:hover {
            background-color: #2a6bc5;
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
            <div class="alert-badge">New Notification</div>
            <h2>{{ $notification->title ?? 'New Update' }}</h2>
        </div>

        <p>Dear {{ $user->first_name ?? $user->name }},</p>

        <div class="details-box">
            <p style="margin: 0; font-size: 16px; font-weight: 500;">{{ $notification->message }}</p>
        </div>

        @if(!empty($notification->action_url))
        <div style="text-align: center;">
            <a href="{{ $notification->action_url }}" class="action-button">View Details</a>
        </div>
        @endif

        <p>Best regards,<br>
        <strong>JBI University System</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>This is an automated notification from the JBI University Management System. You can view all your notifications by logging into your student dashboard.</p>
        </div>
    </div>
</body>
</html>
