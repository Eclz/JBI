<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Payment Reminder</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            color: #0c5460;
        }
        .fee-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .amount-highlight {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background: #5568d3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .payment-instructions {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .payment-instructions h3 {
            margin-top: 0;
            color: #0c5460;
        }
        .payment-instructions ul {
            margin: 10px 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if($reminderType === 'overdue')
                ⚠️ Overdue Fee Payment
            @elseif($reminderType === 'due_soon')
                🔔 Fee Payment Due Soon
            @else
                💳 Fee Payment Reminder
            @endif
        </h1>
    </div>

    <div class="content">
        <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>

        @if($reminderType === 'overdue')
            <div class="alert alert-danger">
                <strong>URGENT:</strong> Your fee payment is now overdue!
                @if($feeRecord->late_fee > 0)
                    A late fee of {{ $currencyCode }} {{ number_format($feeRecord->late_fee, 2) }} has been applied.
                @endif
            </div>
            <p>This is an urgent reminder that your fee payment was due on <strong>{{ $feeRecord->due_date->format('F d, Y') }}</strong> and is now <strong>{{ abs($feeRecord->due_date->diffInDays(now())) }} days overdue</strong>.</p>
        @elseif($reminderType === 'due_soon')
            <div class="alert alert-warning">
                <strong>REMINDER:</strong> Your fee payment is due soon!
            </div>
            <p>This is a friendly reminder that your fee payment is due on <strong>{{ $feeRecord->due_date->format('F d, Y') }}</strong> (in {{ $feeRecord->due_date->diffInDays(now()) }} days).</p>
        @else
            <div class="alert alert-info">
                <strong>NOTICE:</strong> Fee payment pending.
            </div>
            <p>This is a reminder regarding your pending fee payment.</p>
        @endif

        <div class="fee-details">
            <h3 style="margin-top: 0; color: #667eea;">Fee Details</h3>

            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">{{ $feeRecord->invoice_number }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Fee Type:</span>
                <span class="detail-value">{{ $feeStructure->name }}</span>
            </div>

            @if($feeStructure->description)
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ $feeStructure->description }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Academic Year:</span>
                <span class="detail-value">{{ $feeStructure->academicYear->name }}</span>
            </div>

            @if($feeStructure->semester)
            <div class="detail-row">
                <span class="detail-label">Semester:</span>
                <span class="detail-value">{{ $feeStructure->semester->name }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Original Amount:</span>
                <span class="detail-value">{{ $currencyCode }} {{ number_format($feeRecord->amount, 2) }}</span>
            </div>

            @if($feeRecord->discount_amount > 0)
            <div class="detail-row">
                <span class="detail-label">Discount:</span>
                <span class="detail-value" style="color: #28a745;">-{{ $currencyCode }} {{ number_format($feeRecord->discount_amount, 2) }}</span>
            </div>
            @endif

            @if($feeRecord->late_fee > 0)
            <div class="detail-row">
                <span class="detail-label">Late Fee:</span>
                <span class="detail-value" style="color: #dc3545;">+{{ $currencyCode }} {{ number_format($feeRecord->late_fee, 2) }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value"><strong>{{ $currencyCode }} {{ number_format($feeRecord->total_amount, 2) }}</strong></span>
            </div>

            @if($feeRecord->paid_amount > 0)
            <div class="detail-row">
                <span class="detail-label">Paid Amount:</span>
                <span class="detail-value" style="color: #28a745;">{{ $currencyCode }} {{ number_format($feeRecord->paid_amount, 2) }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Due Date:</span>
                <span class="detail-value" style="color: {{ $reminderType === 'overdue' ? '#dc3545' : '#667eea' }};">
                    <strong>{{ $feeRecord->due_date->format('F d, Y') }}</strong>
                </span>
            </div>
        </div>

        <div class="amount-highlight">
            Amount Due: {{ $currencyCode }} {{ number_format($feeRecord->balance_amount, 2) }}
        </div>

        @if($feeRecord->paid_amount > 0)
            <p style="color: #28a745; text-align: center;">
                ✓ You have paid {{ $currencyCode }} {{ number_format($feeRecord->paid_amount, 2) }} ({{ number_format(($feeRecord->paid_amount / $feeRecord->total_amount) * 100, 1) }}% of total)
            </p>
        @endif

        <div class="payment-instructions">
            <h3>Payment Instructions</h3>
            <ul>
                <li>Log in to your student portal to make a payment online</li>
                <li>Visit the Finance Office during business hours (Mon-Fri, 9 AM - 5 PM)</li>
                <li>Bank transfer to: [Bank Account Details]</li>
                <li>Mobile money: [Mobile Money Details]</li>
            </ul>
            <p><strong>Important:</strong> Please use your Invoice Number ({{ $feeRecord->invoice_number }}) as the payment reference.</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">
                Pay Online Now
            </a>
        </div>

        @if($reminderType === 'overdue')
            <p style="color: #dc3545; font-weight: bold; text-align: center; margin-top: 20px;">
                ⚠️ Please settle this payment immediately to avoid further late fees or suspension of services.
            </p>
        @endif

        <p>If you have already made this payment, please disregard this reminder. If you're experiencing financial difficulties, please contact the Finance Office to discuss payment plan options.</p>

        <p>For any questions or concerns, please contact:</p>
        <ul>
            <li><strong>Email:</strong> info@jbiuniversity.com</li>
            <li><strong>WhatsApp:</strong> +27 68 443 8415</li>
            <li><strong>Office:</strong> Finance Department, Main Building, Room 205</li>
        </ul>
    </div>

    <div class="footer">
        <p>
            <strong>JBI University</strong><br>
            Finance Department<br>
            This is an automated reminder. Please do not reply to this email.
        </p>
        <p style="font-size: 12px; color: #999;">
            © {{ date('Y') }} JBI University. All rights reserved.
        </p>
    </div>
</body>
</html>
