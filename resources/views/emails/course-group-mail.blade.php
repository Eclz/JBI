<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }} - JBI University</title>
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
            border-bottom: 2px solid #1e3a8a;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        .course-badge {
            background-color: #1e3a8a;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        .details-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #1e3a8a;
            white-space: pre-wrap;
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
            <div class="course-badge">{{ $course->course_code }} - {{ $course->name }}</div>
            <h2 style="margin-top: 15px; color: #333;">{{ $subjectLine }}</h2>
        </div>

        <p>Dear {{ $recipient->first_name }},</p>

        <p>Your course lecturer, <strong>{{ $sender->full_name }}</strong>, has sent a message to all students enrolled in <strong>{{ $course->course_code }}</strong>:</p>

        <div class="details-box">{{ $body }}</div>

        <p>To reply to this email, you can reply directly or message them via the LMS Mailbox.</p>

        <p>Best regards,<br>
        <strong>JBI University LMS Notification</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>This message was sent from the JBI University Mailbox System to students enrolled in {{ $course->course_code }}.</p>
        </div>
    </div>
</body>
</html>
