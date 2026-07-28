<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Fee Payment Instructions - JBI University</title>
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
            border-bottom: 2px solid #ffc107;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b5bdb;
            margin-bottom: 10px;
        }
        .alert-badge {
            background-color: #ffc107;
            color: #333;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .payment-info {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
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
            <div class="alert-badge">Payment Required</div>
            <h2>Admission Fee Payment Instructions</h2>
        </div>

        <p>Dear {{ $application->first_name }} {{ $application->last_name }},</p>

        <p>Congratulations again on your successful application! To complete your admission process, please pay the admission fee as detailed below:</p>

        <div class="amount">{{ number_format($admissionFee) }} {{ $currencyCode }}</div>

        <div class="payment-info">
            <h3>💳 Payment Details:</h3>
            <ul>
                <li><strong>Bank Name:</strong> Stanbic Bank Uganda</li>
                <li><strong>Account Name:</strong> JBI University</li>
                <li><strong>Account Number:</strong> 9030012345678</li>
                <li><strong>Branch:</strong> South Africa Main Branch</li>
                <li><strong>Swift Code:</strong> SBICUGKX</li>
                <li><strong>Currency:</strong> {{ $currencyCode }}</li>
                <li><strong>Payment Reference:</strong> {{ $application->application_number }}</li>
            </ul>
            <p><strong>⚠️ IMPORTANT:</strong> Please use your application number ({{ $application->application_number }}) as the payment reference to ensure proper tracking.</p>
        </div>

        <h3>Payment Options:</h3>
        <ol>
            <li><strong>Bank Transfer:</strong> Use the bank details above</li>
            <li><strong>Mobile Money:</strong> Contact admissions office for mobile money details</li>
            <li><strong>Cash Payment:</strong> Visit the university finance office during business hours (Mon-Fri, 8AM-5PM)</li>
        </ol>

        <h3>After Making Payment:</h3>
        <p>Please upload your payment receipt/proof using the button below. This will help us verify your payment quickly.</p>

        <div style="text-align: center;">
            <a href="{{ route('applications.upload-payment', $application->application_number) }}" class="payment-button">Upload Payment Proof</a>
        </div>

        <p><strong>What to Upload:</strong></p>
        <ul>
            <li>Bank deposit slip or transfer receipt</li>
            <li>Mobile money confirmation SMS screenshot</li>
            <li>Any official payment confirmation document</li>
        </ul>

        <p><strong>Note:</strong> Payment verification typically takes 1-2 business days. Once verified, you will receive your official admission letter.</p>

        <p>If you need assistance or have questions about payment, please contact:<br>
        <strong>Email:</strong> finance@johnsonbibleinstitute.com<br>
        <strong>Phone:</strong> +27 67 965 3866<br>
        <strong>WhatsApp:</strong>+27 67 965 3866</p>

        <p>Best regards,<br>
        <strong>JBI University Finance Office</strong></p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} JBI University. All rights reserved.</p>
            <p>JBI University | 91 Progress Road Lindhaven Roodeport South Africa</p>
        </div>
    </div>
</body>
</html>
