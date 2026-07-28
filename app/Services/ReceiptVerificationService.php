<?php

namespace App\Services;

use App\Models\FeeRecord;
use App\Models\Payment;

class ReceiptVerificationService
{
    public static function summaryReceiptNumber(FeeRecord $fee): string
    {
        return 'RCPT-SUM-' . str_pad((string) $fee->id, 8, '0', STR_PAD_LEFT);
    }

    public static function transactionReceiptNumber(Payment $payment): string
    {
        return 'RCPT-TXN-' . str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT);
    }

    public static function summaryVerificationCode(FeeRecord $fee): string
    {
        return strtoupper(substr(
            hash('sha256', 'SUM|' . $fee->id . '|' . $fee->user_id . '|' . $fee->total_amount . '|' . optional($fee->created_at)->timestamp),
            0,
            16
        ));
    }

    public static function transactionVerificationCode(Payment $payment): string
    {
        return strtoupper(substr(
            hash('sha256', 'TXN|' . $payment->id . '|' . $payment->fee_record_id . '|' . $payment->amount . '|' . optional($payment->created_at)->timestamp),
            0,
            16
        ));
    }
}
