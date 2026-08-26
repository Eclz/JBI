<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved - JBI University</title>
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
        .congratulations {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .payment-details {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .payment-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
            <div class="success-badge">Application Approved</div>
            <h2>Congratulations!</h2>
        </div>

        <p>Dear {{ $application->first_name }} {{ $application->last_name }},</p>

        <div class="congratulations">
            <h3>🎉 Your Application Has Been Approved!</h3>
            <p>We are pleased to inform you that your application to JBI University has been approved. Welcome to our academic community!</p>
        </div>

        <p><strong>Application Number:</strong> {{ $application->application_number }}</p>

        <div class="payment-details">
            <h3>💳 Next Step: Admission Fee Payment</h3>
            <p><strong>Amount:</strong> 50 usd</p>
            <p><strong>Bank Details:</strong></p>
            <ul>
                <li>Bank Name: Stanbic Bank South Africa</li>
                <li>Account Name: JBI University</li>
                <li>Account Number: 9030012345678</li>
                <li>Branch: Kampala Main Branch</li>
                <li>Reference: {{ $application->application_number }}</li>
            </ul>
            <p><strong>Important:</strong> Please use your application number ({{ $application->application_number }}) as the payment reference.</p>
        </div>

        <p>After making the payment, please upload your payment receipt using the link below:</p>

        <div style="text-align: center;">
            <a href="{{ route('applications.upload-payment', $application->application_number) }}" class="payment-button">Upload Payment Proof</a>
        </div>

        <p><strong>What Happens After Payment:</strong></p>
        <ol>
            <li>Upload your payment receipt through the link above</li>
            <li>Our finance team will verify your payment within 1-2 business days</li>
            <li>Once verified, you will receive:
                <ul>
                    <li>Official Admission Letter</li>
                    <li>Admission Number</li>
                    <li>Student/Faculty Number</li>
                    <li>Further registration instructions</li>
                </ul>
            </li>
        </ol>

        <p>If you have any questions, contact us at <a href="mailto:admission@jbiuniversity.com">admission@jbiuniversity.com</a> or WhatsApp +27 68 443 8415.</p>

        <p>We look forward to welcoming you to JBI University!</p>

        <p>Best regards,<br>
        <strong>JBI University Admissions Team</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>JBI University | South Africa | www.jbiuniversity.com</p>
        </div>
    </div>
</body>
</html>
