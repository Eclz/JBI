<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentReferenceNumber;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AdmissionWorkflow;
use App\Services\ReceiptVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeeController extends Controller
{
    public function ledger()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $user = Auth::user();
        \App\Services\FeeInvoiceService::ensureStudentInvoiced($user);

        // Get all fee records for student (Tuition, Functional, Retake, Missed Paper)
        $feeRecords = FeeRecord::where('user_id', $user->id)
            ->with(['feeStructure.academicYear', 'feeStructure.semester'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Also fetch completed payments
        $payments = Payment::where('student_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('student.fees.ledger', compact('feeRecords', 'payments', 'currencyCode'));
    }

    public function structure()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $user = Auth::user();
        \App\Services\FeeInvoiceService::ensureStudentInvoiced($user);

        $sp = $user->studentProfile;
        $feeStructures = \App\Models\FeeStructure::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('student.fees.structure', compact('user', 'sp', 'feeStructures', 'currencyCode'));
    }

    public function index()
    {
        $user = Auth::user();
        \App\Services\FeeInvoiceService::ensureStudentInvoiced($user);

        $feeRecords = FeeRecord::where('user_id', $user->id)
            ->with('feeStructure')
            ->orderBy('due_date', 'asc')
            ->paginate(20);
        $semesterBalances = FeeRecord::where('user_id', Auth::id())
            ->with('feeStructure.semester')
            ->get()
            ->groupBy(function ($record) {
                return $record->feeStructure?->semester?->name ?? 'Unassigned';
            })
            ->map(function ($records) {
                return [
                    'total' => $records->sum('total_amount'),
                    'paid' => $records->sum('paid_amount'),
                    'balance' => $records->sum('balance_amount'),
                ];
            });

        $paymentLedgerRaw = Payment::where('student_id', Auth::id())
            ->with(['feeRecord.feeStructure.academicYear', 'feeRecord.feeStructure.semester'])
            ->orderBy('payment_date', 'asc')
            ->get();
        $runningPaid = [];
        $paymentLedger = $paymentLedgerRaw->map(function ($payment) use (&$runningPaid) {
            $feeId = $payment->fee_record_id;
            $feeTotal = $payment->feeRecord?->total_amount ?? 0;
            $paidToDate = $runningPaid[$feeId] ?? 0;

            if ($payment->status === 'completed') {
                $paidToDate += $payment->amount;
                $runningPaid[$feeId] = $paidToDate;
            }

            $balanceAfter = max($feeTotal - $paidToDate, 0);

            return [
                'payment' => $payment,
                'paid_to_date' => $paidToDate,
                'balance_after' => $balanceAfter,
            ];
        })->sortByDesc(function ($row) {
            return $row['payment']->payment_date;
        })->values();

        $firstPayable = FeeRecord::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->where('status', '!=', 'paid')
                    ->orWhere('balance_amount', '>', 0);
            })
            ->orderBy('due_date', 'asc')
            ->first();

        $summary = [
            'total_fees' => FeeRecord::where('user_id', Auth::id())->sum('total_amount'),
            'paid_amount' => FeeRecord::where('user_id', Auth::id())->sum('paid_amount'),
            'outstanding' => FeeRecord::where('user_id', Auth::id())->sum('balance_amount'),
        ];

        $allUnpaidFees = FeeRecord::where('user_id', Auth::id())
            ->where('balance_amount', '>', 0)
            ->get();

        $userPrns = PaymentReferenceNumber::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Check for expired PRNs
        foreach ($userPrns as $prn) {
            $isExp = $prn->is_expired; // auto updates status if expired
        }

        return view('student.fees.index', compact('feeRecords', 'summary', 'firstPayable', 'paymentLedger', 'semesterBalances', 'allUnpaidFees', 'userPrns'));
    }

    public function generatePrn(Request $request)
    {
        $request->validate([
            'fee_record_id' => 'required|exists:fee_records,id',
            'payment_type' => 'required|in:full,partial',
            'custom_amount' => 'nullable|numeric|min:1',
        ]);

        $feeRecord = FeeRecord::where('user_id', Auth::id())
            ->findOrFail($request->fee_record_id);

        if ($feeRecord->balance_amount <= 0) {
            return back()->with('error', 'This fee item has already been fully paid.');
        }

        $amount = $request->payment_type === 'full' 
            ? $feeRecord->balance_amount 
            : min($request->custom_amount ?? $feeRecord->balance_amount, $feeRecord->balance_amount);

        if ($amount <= 0) {
            return back()->with('error', 'Please enter a valid payment amount.');
        }

        $itemName = $feeRecord->payment_notes ?? $feeRecord->feeStructure?->name ?? 'University Tuition & Fees';
        $prnNumber = PaymentReferenceNumber::generateUniquePrn();

        $prn = PaymentReferenceNumber::create([
            'user_id' => Auth::id(),
            'fee_record_id' => $feeRecord->id,
            'fee_structure_id' => $feeRecord->fee_structure_id,
            'prn_number' => $prnNumber,
            'fee_item_name' => $itemName,
            'amount' => $amount,
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'generated_at' => now(),
            'expires_at' => now()->addDays(30), // Time-bound within 30 days
        ]);

        return redirect()->route('student.fees.prn.show', $prn)
            ->with('success', 'PRN ' . $prnNumber . ' generated successfully! Valid for 30 days.');
    }

    public function showPrn(PaymentReferenceNumber $prn)
    {
        if ($prn->user_id !== Auth::id()) {
            abort(403);
        }

        // Trigger expiration check
        $isExp = $prn->is_expired;

        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $paymentMethods = $this->paymentMethods();

        return view('student.fees.prn_slip', compact('prn', 'currencyCode', 'paymentMethods'));
    }

    public function processPrnPayment(Request $request, PaymentReferenceNumber $prn)
    {
        if ($prn->user_id !== Auth::id()) {
            abort(403);
        }

        if ($prn->is_expired) {
            return redirect()->route('student.fees.index')
                ->with('error', 'This PRN expired on ' . ($prn->expires_at ? $prn->expires_at->format('M d, Y') : '30-day limit') . '. Please generate a new PRN to make your payment.');
        }

        if ($prn->status === 'paid') {
            return redirect()->route('student.fees.index')
                ->with('info', 'This PRN has already been paid and completed.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $feeRecord = $prn->feeRecord;

        if ($feeRecord) {
            $newPaidAmount = $feeRecord->paid_amount + $prn->amount;
            $newBalance = max(0, $feeRecord->total_amount - $newPaidAmount);

            $feeRecord->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'paid_date' => now(),
                'payment_method' => $request->payment_method,
                'status' => $newBalance <= 0 ? 'paid' : 'partial',
            ]);
        }

        $prn->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $request->payment_method,
            'transaction_reference' => Str::uuid()->toString(),
        ]);

        // Create transaction entry
        Payment::create([
            'fee_record_id' => $feeRecord?->id,
            'student_id' => Auth::id(),
            'amount' => $prn->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $prn->transaction_reference,
            'reference_number' => $prn->prn_number,
            'notes' => 'Payment made via PRN ' . $prn->prn_number . ' for ' . $prn->fee_item_name,
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        return redirect()->route('student.fees.index')
            ->with('success', 'Payment of USD ' . number_format($prn->amount, 2) . ' processed successfully using PRN ' . $prn->prn_number . '! Your account balance has been updated.');
    }

    public function pay(FeeRecord $fee)
    {
        if ($fee->user_id !== Auth::id()) {
            abort(403);
        }

        $fee->load('feeStructure');
        $paymentMethods = $this->paymentMethods();

        return view('student.fees.pay', compact('fee', 'paymentMethods'));
    }

    public function processPayment(Request $request, FeeRecord $fee)
    {
        if ($fee->user_id !== Auth::id()) {
            abort(403);
        }

        $paymentMethods = $this->paymentMethods();
        $fee->load('feeStructure');

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $fee->balance_amount,
            'payment_method' => 'required|in:' . implode(',', array_keys($paymentMethods)),
            'payment_proof' => 'required_if:payment_method,cash|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')
                ->store('fee-payment-proofs/' . $fee->invoice_number, 'public');
        }

        $isCash = $request->payment_method === 'cash';
        $paymentStatus = $isCash ? 'pending' : 'completed';

        if (!$isCash) {
            $newPaidAmount = $fee->paid_amount + $request->amount;
            $newBalance = $fee->total_amount - $newPaidAmount;

            $fee->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'paid_date' => now(),
                'payment_method' => $request->payment_method,
                'status' => $newBalance <= 0 ? 'paid' : 'partial',
            ]);
        }

        $payment = Payment::create([
            'fee_record_id' => $fee->id,
            'student_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => Str::uuid()->toString(),
            'reference_number' => $fee->invoice_number,
            'notes' => $request->input('notes'),
            'payment_proof' => $proofPath,
            'status' => $paymentStatus,
            'payment_date' => now(),
        ]);

        if ($isCash) {
            $admins = User::whereIn('role', ['admin', 'finance'])->where('is_active', true)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'payment',
                    'title' => 'New Cash Payment Pending',
                    'message' => 'A student has submitted a cash payment that needs approval.',
                    'action_url' => route('admin.fees.records.show', $fee),
                    'priority' => 'high',
                ]);
            }

            Notification::create([
                'user_id' => Auth::id(),
                'type' => 'payment',
                'title' => 'Payment Submitted',
                'message' => 'Your cash payment was submitted and is pending approval.',
                'action_url' => route('student.fees.index'),
                'priority' => 'normal',
            ]);
        } else {
            $registrationFeeId = (int) SystemSetting::getSetting('registration_fee_structure_id', 0);
            $isRegistrationFee = $fee->fee_structure_id === $registrationFeeId || $fee->feeStructure?->type === 'registration';
            if ($isRegistrationFee) {
                $profile = Auth::user()->studentProfile;
                if ($profile && !$profile->registration_fee_paid_at) {
                    $profile->update([
                        'registration_fee_paid_at' => now(),
                    ]);
                }
                AdmissionWorkflow::activateStudent(Auth::user());
            }
        }

        return redirect()->route('student.fees.index')
            ->with('success', $isCash ? 'Cash payment submitted for approval.' : 'Payment processed successfully.');
    }

    public function receipt(FeeRecord $fee)
    {
        if ($fee->user_id !== Auth::id()) {
            abort(403);
        }

        $fee->load('feeStructure');
        $payments = Payment::where('fee_record_id', $fee->id)
            ->orderBy('payment_date', 'desc')
            ->get();
        $receiptNumber = ReceiptVerificationService::summaryReceiptNumber($fee);
        $verificationCode = ReceiptVerificationService::summaryVerificationCode($fee);
        $verificationUrl = route('receipts.verify');

        return view('student.fees.receipt', compact('fee', 'payments', 'receiptNumber', 'verificationCode', 'verificationUrl'));
    }

    public function transactionReceipt(FeeRecord $fee, Payment $payment)
    {
        if ($fee->user_id !== Auth::id()) {
            abort(403);
        }

        if ($payment->fee_record_id !== $fee->id || $payment->student_id !== Auth::id()) {
            abort(403);
        }

        $fee->load('feeStructure');
        $payment->load('processedBy');
        $receiptNumber = ReceiptVerificationService::transactionReceiptNumber($payment);
        $verificationCode = ReceiptVerificationService::transactionVerificationCode($payment);
        $verificationUrl = route('receipts.verify');

        return view('student.fees.transaction-receipt', compact('fee', 'payment', 'receiptNumber', 'verificationCode', 'verificationUrl'));
    }

    private function paymentMethods(): array
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'card' => 'Card',
            'payfast' => 'PayFast',
            'paygate' => 'PayGate',
            'peach_payments' => 'Peach Payments',
            'ozow' => 'Ozow',
            'yoco' => 'Yoco',
            'snapscan' => 'SnapScan',
        ];
    }
}
