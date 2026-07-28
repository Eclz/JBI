<?php

namespace App\Http\Controllers;

use App\Models\FeeRecord;
use App\Models\Payment;
use App\Services\ReceiptVerificationService;
use Illuminate\Http\Request;

class ReceiptVerificationController extends Controller
{
    public function showForm()
    {
        return view('receipts.verify');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'receipt_number' => 'required|string|max:100',
            'verification_code' => 'required|string|max:50',
        ]);

        $receiptNumber = strtoupper(trim($validated['receipt_number']));
        $verificationCode = strtoupper(trim($validated['verification_code']));

        $result = [
            'valid' => false,
            'message' => 'Receipt could not be verified.',
            'type' => null,
            'record' => null,
        ];

        if (str_starts_with($receiptNumber, 'RCPT-TXN-')) {
            $id = (int) ltrim(substr($receiptNumber, strlen('RCPT-TXN-')), '0');
            $payment = Payment::with(['student', 'feeRecord.feeStructure'])->find($id);

            if ($payment) {
                $expectedCode = ReceiptVerificationService::transactionVerificationCode($payment);
                if (hash_equals($expectedCode, $verificationCode)) {
                    $result = [
                        'valid' => true,
                        'message' => 'Transaction receipt is valid.',
                        'type' => 'transaction',
                        'record' => $payment,
                    ];
                }
            }
        } elseif (str_starts_with($receiptNumber, 'RCPT-SUM-')) {
            $id = (int) ltrim(substr($receiptNumber, strlen('RCPT-SUM-')), '0');
            $fee = FeeRecord::with(['student', 'feeStructure'])->find($id);

            if ($fee) {
                $expectedCode = ReceiptVerificationService::summaryVerificationCode($fee);
                if (hash_equals($expectedCode, $verificationCode)) {
                    $result = [
                        'valid' => true,
                        'message' => 'Summary receipt is valid.',
                        'type' => 'summary',
                        'record' => $fee,
                    ];
                }
            }
        }

        return view('receipts.verify', [
            'result' => $result,
            'receiptNumber' => $receiptNumber,
            'verificationCode' => $verificationCode,
        ]);
    }
}
