<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .success-message {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .payment-details {
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
        .amount-box {
            background: #28a745;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-box h2 {
            margin: 0;
            font-size: 32px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .receipt-note {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✓ Payment Received</h1>
    </div>

    <div class="content">
        <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>

        <div class="success-message">
            ✓ Your payment has been successfully processed!
        </div>

        <p>Thank you for your payment. This email confirms that we have received your payment for the following fee:</p>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #28a745;">Payment Details</h3>

            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">{{ $feeRecord->invoice_number }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value">{{ now()->format('F d, Y g:i A') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $feeRecord->payment_method ?? 'N/A')) }}</span>
            </div>

            @if($feeRecord->transaction_id)
            <div class="detail-row">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value">{{ $feeRecord->transaction_id }}</span>
            </div>
            @endif
        </div>

        <div class="amount-box">
            <p style="margin: 0; font-size: 14px; opacity: 0.9;">Payment Amount</p>
            <h2>${{ number_format($paymentAmount, 2) }}</h2>
        </div>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #667eea;">Fee Summary</h3>

            <div class="detail-row">
                <span class="detail-label">Total Fee Amount:</span>
                <span class="detail-value">${{ number_format($feeRecord->total_amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Total Paid:</span>
                <span class="detail-value" style="color: #28a745;">${{ number_format($feeRecord->paid_amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Balance Due:</span>
                <span class="detail-value" style="color: {{ $feeRecord->balance_amount > 0 ? '#dc3545' : '#28a745' }}; font-weight: bold;">
                    ${{ number_format($feeRecord->balance_amount, 2) }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    @if($feeRecord->status === 'paid')
                        <span style="color: #28a745; font-weight: bold;">✓ Fully Paid</span>
                    @elseif($feeRecord->status === 'partial')
                        <span style="color: #ffc107; font-weight: bold;">Partially Paid ({{ number_format(($feeRecord->paid_amount / $feeRecord->total_amount) * 100, 1) }}%)</span>
                    @else
                        <span style="color: #dc3545;">Pending</span>
                    @endif
                </span>
            </div>
        </div>

        @if($feeRecord->balance_amount > 0)
            <div class="receipt-note">
                <strong>Note:</strong> You still have a balance of <strong>${{ number_format($feeRecord->balance_amount, 2) }}</strong> remaining on this fee. Please make payment by {{ $feeRecord->due_date->format('F d, Y') }} to avoid late fees.
            </div>
        @else
            <div class="success-message" style="font-size: 16px;">
                ✓ This fee has been fully paid. Thank you!
            </div>
        @endif

        <div class="receipt-note">
            <strong>Important:</strong> Please keep this email as your payment receipt. An official receipt will also be available in your student portal.
        </div>

        <p>You can view your complete payment history and download official receipts by logging into your student portal.</p>

        <p>If you have any questions about this payment or your account, please contact:</p>
        <ul>
            <li><strong>Email:</strong> finance@jbiuniversity.edu</li>
            <li><strong>Phone:</strong> +1 (555) 123-4567</li>
            <li><strong>Office:</strong> Finance Department, Main Building, Room 205</li>
        </ul>

        <p>Thank you for your prompt payment!</p>
    </div>

    <div class="footer">
        <p>
            <strong>JBI University</strong><br>
            Finance Department<br>
            This is an automated confirmation. Please do not reply to this email.
        </p>
        <p style="font-size: 12px; color: #999;">
            © {{ date('Y') }} JBI University. All rights reserved.
        </p>
    </div>
</body>
</html>
