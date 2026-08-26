<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update - JBI University</title>
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
            border-bottom: 2px solid #6c757d;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b5bdb;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .encouragement {
            background-color: #e7f3ff;
            border-left: 4px solid #3b5bdb;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">JBI University</div>
            <h2>Application Status Update</h2>
        </div>

        <p>Dear {{ $user->first_name }} {{ $user->last_name }},</p>

        <p>Thank you for your interest in JBI University and for taking the time to submit your application. After careful consideration of your application materials, we regret to inform you that we are unable to offer you admission at this time.</p>

        @if($rejectionReason)
        <h3>Feedback:</h3>
        <p>{{ $rejectionReason }}</p>
        @endif

        <div class="encouragement">
            <h3>We Encourage You to Reapply</h3>
            <p>Please know that this decision does not reflect your potential or worth as a student. We receive many qualified applications, and our admission process is highly competitive. We encourage you to consider reapplying in the future, and we would be happy to provide guidance on strengthening your application.</p>
        </div>

        <h3>Next Steps:</h3>
        <ul>
            <li><strong>Reapplication:</strong> You may reapply for the next admission cycle</li>
            <li><strong>Feedback Session:</strong> Schedule a meeting with our admissions counselor for personalized feedback</li>
            <li><strong>Alternative Programs:</strong> Consider our other programs that might be a good fit</li>
            <li><strong>Transfer Options:</strong> Explore transfer opportunities after completing coursework elsewhere</li>
        </ul>

        <p>If you would like to discuss your application or learn about ways to strengthen a future application, please contact our admissions office at <a href="mailto:admission@jbiuniversity.com">admission@jbiuniversity.com</a> or WhatsApp +27 68 443 8415.</p>

        <p>We wish you the very best in your educational pursuits and hope you will consider JBI University again in the future.</p>

        <p>Sincerely,<br>
        <strong>JBI University Admissions Committee</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>JBI University | South Africa | www.jbiuniversity.com</p>
        </div>
    </div>
</body>
</html>
