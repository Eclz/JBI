<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\User;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Http\Requests\StoreFeeRecordRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Services\ReceiptVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        // Get statistics
        $totalCollected = FeeRecord::where('status', 'paid')->sum('paid_amount');
        $totalPending = FeeRecord::whereIn('status', ['pending', 'partial'])->sum('balance_amount');
        $totalOverdue = FeeRecord::where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '<', now());
            })->sum('balance_amount');
        $thisMonthCollection = FeeRecord::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('paid_amount');

        // Get fee records with filters
        $feeRecords = FeeRecord::with(['student.studentProfile', 'feeStructure'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'overdue') {
                    $query->where(function($q) {
                        $q->where('status', 'overdue')
                          ->orWhere(function($subQ) {
                              $subQ->where('status', 'pending')
                                   ->where('due_date', '<', now());
                          });
                    });
                } else {
                    $query->where('status', $status);
                }
            })
            ->when($request->semester_id, function ($query, $semesterId) {
                $query->whereHas('feeStructure', function ($q) use ($semesterId) {
                    $q->where('semester_id', $semesterId);
                });
            })
            ->when($request->due_date, function ($query, $dueDate) {
                $query->whereDate('due_date', '<=', $dueDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get fee structures
        $feeStructures = FeeStructure::with(['academicYear', 'semester'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get semesters for filter
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $courses = \App\Models\Course::with('semester')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->with('studentProfile')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Chart data for collection trends (last 6 months)
        $collectionChartLabels = [];
        $collectionChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $collectionChartLabels[] = $date->format('M Y');
            $collectionChartData[] = FeeRecord::where('status', 'paid')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('paid_amount');
        }

        // Status distribution data
        $statusChartData = [
            FeeRecord::where('status', 'paid')->count(),
            FeeRecord::where('status', 'pending')->count(),
            FeeRecord::where('status', 'overdue')
                ->orWhere(function($q) {
                    $q->where('status', 'pending')
                      ->where('due_date', '<', now());
                })->count(),
            FeeRecord::where('status', 'partial')->count(),
        ];

        $semesterTotals = FeeRecord::query()
            ->join('fee_structures', 'fee_records.fee_structure_id', '=', 'fee_structures.id')
            ->leftJoin('semesters', 'fee_structures.semester_id', '=', 'semesters.id')
            ->selectRaw('COALESCE(semesters.name, "Unassigned") as semester_name, SUM(fee_records.paid_amount) as total_paid')
            ->groupBy('semester_name')
            ->orderBy('semester_name')
            ->get();
        $semesterChartLabels = $semesterTotals->pluck('semester_name')->all();
        $semesterChartData = $semesterTotals->pluck('total_paid')->map(fn ($value) => (float) $value)->all();

        $cashStatusCounts = Payment::where('payment_method', 'cash')
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();
        $cashStatusLabels = ['pending', 'completed', 'failed'];
        $cashStatusData = [
            (int) ($cashStatusCounts['pending'] ?? 0),
            (int) ($cashStatusCounts['completed'] ?? 0),
            (int) ($cashStatusCounts['failed'] ?? 0),
        ];

        $dbDriver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $concatSql = $dbDriver === 'sqlite'
            ? 'TRIM(COALESCE(users.first_name, "") || " " || COALESCE(users.last_name, ""))'
            : 'CONCAT_WS(" ", users.first_name, users.last_name)';

        $semesterStudentBreakdown = FeeRecord::query()
            ->join('users', 'fee_records.user_id', '=', 'users.id')
            ->join('fee_structures', 'fee_records.fee_structure_id', '=', 'fee_structures.id')
            ->leftJoin('semesters', 'fee_structures.semester_id', '=', 'semesters.id')
            ->selectRaw('
                COALESCE(semesters.name, "Unassigned") as semester_name,
                fee_records.user_id as student_id,
                COALESCE(NULLIF(' . $concatSql . ', ""), users.name, users.email) as student_name,
                SUM(fee_records.total_amount) as total_amount,
                SUM(fee_records.paid_amount) as paid_amount,
                SUM(fee_records.balance_amount) as balance_amount,
                COUNT(fee_records.id) as invoice_count
            ')
            ->groupBy(
                'semesters.name',
                'fee_records.user_id',
                'users.first_name',
                'users.last_name',
                'users.name',
                'users.email'
            )
            ->orderByRaw('COALESCE(semesters.name, "Unassigned") asc')
            ->orderByRaw('COALESCE(NULLIF(' . $concatSql . ', ""), users.name, users.email) asc')
            ->get();

        return view('admin.fees.index', compact(
            'feeRecords',
            'feeStructures',
            'semesters',
            'courses',
            'students',
            'totalCollected',
            'totalPending',
            'totalOverdue',
            'thisMonthCollection',
            'collectionChartLabels',
            'collectionChartData',
            'statusChartData',
            'semesterChartLabels',
            'semesterChartData',
            'cashStatusLabels',
            'cashStatusData',
            'semesterStudentBreakdown'
        ));
    }

    public function show(FeeRecord $fee)
    {
        $fee->load(['student.studentProfile', 'feeStructure', 'processor']);

        $paymentEntries = Payment::where('fee_record_id', $fee->id)
            ->orderBy('payment_date', 'asc')
            ->get();
        $runningPaid = 0;
        $payments = $paymentEntries->map(function ($payment) use (&$runningPaid, $fee) {
            if ($payment->status === 'completed') {
                $runningPaid += $payment->amount;
            }
            $balanceAfter = max($fee->total_amount - $runningPaid, 0);

            return [
                'payment' => $payment,
                'paid_to_date' => $runningPaid,
                'balance_after' => $balanceAfter,
            ];
        })->sortByDesc(function ($row) {
            return $row['payment']->payment_date;
        })->values();

        return view('admin.fees.show', compact('fee', 'payments'));
    }

    public function create()
    {
        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->with('studentProfile')
            ->orderBy('first_name')
            ->get();

        $feeStructures = FeeStructure::where('is_active', true)
            ->with(['academicYear', 'semester'])
            ->orderBy('name')
            ->get();

        return view('admin.fees.create', compact('students', 'feeStructures'));
    }

    public function store(StoreFeeRecordRequest $request)
    {
        try {
            DB::beginTransaction();

            $feeRecord = FeeRecord::create($request->validated());

            // Log the creation
            // activity()
            //     ->performedOn($feeRecord)
            //     ->causedBy(auth()->user())
            //     ->log('Fee record created');

            DB::commit();

            return redirect()->route('admin.fees.index')
                ->with('success', 'Fee record created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create fee record: ' . $e->getMessage()]);
        }
    }

    public function edit(FeeRecord $fee)
    {
        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->with('studentProfile')
            ->orderBy('first_name')
            ->get();

        $feeStructures = FeeStructure::where('is_active', true)
            ->with(['academicYear', 'semester'])
            ->orderBy('name')
            ->get();

        return view('admin.fees.edit', compact('fee', 'students', 'feeStructures'));
    }

    public function update(Request $request, FeeRecord $fee)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $amount = $request->amount;
            $discountAmount = $request->discount_amount ?? 0;
            $lateFee = $request->late_fee ?? 0;
            $totalAmount = $amount - $discountAmount + $lateFee;
            $balanceAmount = $totalAmount - $fee->paid_amount;

            $fee->update([
                'amount' => $amount,
                'discount_amount' => $discountAmount,
                'late_fee' => $lateFee,
                'total_amount' => $totalAmount,
                'balance_amount' => $balanceAmount,
                'due_date' => $request->due_date,
                'payment_notes' => $request->payment_notes,
                'status' => $balanceAmount <= 0 ? 'paid' : ($fee->paid_amount > 0 ? 'partial' : 'pending'),
            ]);

            // Log the update
            // activity()
            //     ->performedOn($fee)
            //     ->causedBy(auth()->user())
            //     ->log('Fee record updated');

            DB::commit();

            return redirect()->route('admin.fees.index')
                ->with('success', 'Fee record updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update fee record: ' . $e->getMessage()]);
        }
    }

    public function destroy(FeeRecord $fee)
    {
        if ($fee->paid_amount > 0) {
            return back()->withErrors(['error' => 'Cannot delete fee record with payments.']);
        }

        try {
            DB::beginTransaction();

            // Log the deletion
            // activity()
            //     ->performedOn($fee)
            //     ->causedBy(auth()->user())
            //     ->log('Fee record deleted');

            $fee->delete();

            DB::commit();

            return redirect()->route('admin.fees.index')
                ->with('success', 'Fee record deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete fee record: ' . $e->getMessage()]);
        }
    }

    public function showPayment(FeeRecord $fee)
    {
        if ($fee->status === 'paid') {
            return back()->withErrors(['error' => 'This fee record is already fully paid.']);
        }

        return view('admin.fees.payment', compact('fee'));
    }

    public function processPayment(ProcessPaymentRequest $request, FeeRecord $fee)
    {
        try {
            DB::beginTransaction();

            $paymentAmount = $request->payment_amount;
            $newPaidAmount = $fee->paid_amount + $paymentAmount;
            $newBalanceAmount = $fee->total_amount - $newPaidAmount;

            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')
                    ->store('fee-payment-proofs/' . $fee->invoice_number, 'public');
            }

            // Update payment history
            $paymentHistory = $fee->payment_history ?? [];
            $paymentHistory[] = [
                'amount' => $paymentAmount,
                'date' => $request->payment_date,
                'method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'notes' => $request->payment_notes,
                'proof_path' => $proofPath,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ];

            // Determine new status
            $newStatus = $newBalanceAmount <= 0 ? 'paid' : 'partial';

            $fee->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalanceAmount,
                'status' => $newStatus,
                'paid_date' => $newStatus === 'paid' ? $request->payment_date : $fee->paid_date,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'payment_notes' => $request->payment_notes,
                'payment_history' => $paymentHistory,
                'processed_by' => auth()->id(),
            ]);

            Payment::create([
                'fee_record_id' => $fee->id,
                'student_id' => $fee->user_id,
                'amount' => $paymentAmount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'reference_number' => $fee->invoice_number,
                'notes' => $request->payment_notes,
                'payment_proof' => $proofPath,
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'payment_date' => Carbon::parse($request->payment_date),
            ]);

            // Log the payment
            // activity()
            //     ->performedOn($fee)
            //     ->causedBy(auth()->user())
            //     ->withProperties([
            //         'payment_amount' => $paymentAmount,
            //         'payment_method' => $request->payment_method,
            //         'transaction_id' => $request->transaction_id,
            //     ])
            //     ->log('Payment processed');

            DB::commit();

            // Send payment confirmation email
            try {
                \Mail::to($fee->student->email)
                    ->send(new \App\Mail\FeePaymentConfirmation($fee, $paymentAmount));
            } catch (\Exception $e) {
                \Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
                // Don't fail the payment if email fails
            }

            return redirect()->route('admin.fees.records.show', $fee)
                ->with('success', 'Payment processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process payment: ' . $e->getMessage()]);
        }
    }

    public function approvePayment(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending payments can be approved.']);
        }

        $fee = $payment->feeRecord;
        $fee->load('feeStructure');

        try {
            DB::beginTransaction();

            $newPaidAmount = $fee->paid_amount + $payment->amount;
            $newBalanceAmount = $fee->total_amount - $newPaidAmount;
            $newStatus = $newBalanceAmount <= 0 ? 'paid' : 'partial';

            $paymentHistory = $fee->payment_history ?? [];
            $paymentHistory[] = [
                'amount' => $payment->amount,
                'date' => $payment->payment_date,
                'method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'notes' => $payment->notes,
                'proof_path' => $payment->payment_proof,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ];

            $fee->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalanceAmount,
                'status' => $newStatus,
                'paid_date' => $newStatus === 'paid' ? now() : $fee->paid_date,
                'payment_method' => $payment->payment_method,
                'payment_history' => $paymentHistory,
                'processed_by' => auth()->id(),
            ]);

            $payment->update([
                'status' => 'completed',
                'processed_by' => auth()->id(),
            ]);

            $registrationFeeId = (int) \App\Models\SystemSetting::getSetting('registration_fee_structure_id', 0);
            $isRegistrationFee = $fee->fee_structure_id === $registrationFeeId || $fee->feeStructure?->type === 'registration';
            if ($isRegistrationFee) {
                $profile = $fee->student?->studentProfile;
                if ($profile && !$profile->registration_fee_paid_at) {
                    $profile->update([
                        'registration_fee_paid_at' => now(),
                    ]);
                }
                \App\Services\AdmissionWorkflow::activateStudent($fee->student, null, auth()->id());
            }

            Notification::create([
                'user_id' => $payment->student_id,
                'type' => 'payment',
                'title' => 'Payment Approved',
                'message' => 'Your payment has been approved and posted to your account.',
                'action_url' => route('student.fees.index'),
                'priority' => 'normal',
            ]);

            DB::commit();

            return back()->with('success', 'Payment approved and posted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve payment: ' . $e->getMessage()]);
        }
    }

    public function generateInvoices(Request $request)
    {
        $request->validate([
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'generation_type' => 'nullable|in:all,selected',
            'due_date' => 'nullable|date',
            'semester_id' => 'nullable|exists:semesters,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if ($request->input('generation_type') === 'selected' && !$request->filled('student_ids')) {
            return back()->withErrors(['student_ids' => 'Please select at least one student for selected generation.']);
        }

        try {
            DB::beginTransaction();

            $feeStructure = FeeStructure::findOrFail($request->fee_structure_id);

            $studentsQuery = User::query()
                ->where('role', 'student')
                ->where('is_active', true);

            if ($request->filled('student_ids')) {
                $studentsQuery->whereIn('id', $request->student_ids);
            }

            if ($request->filled('semester_id')) {
                $semesterId = (int) $request->semester_id;
                $studentsQuery->whereHas('courseEnrollments', function ($q) use ($semesterId) {
                    $q->where('status', 'enrolled')
                        ->whereHas('course', function ($courseQuery) use ($semesterId) {
                            $courseQuery->where('semester_id', $semesterId);
                        });
                });
            }

            if ($request->filled('course_id')) {
                $courseId = (int) $request->course_id;
                $studentsQuery->whereHas('courseEnrollments', function ($q) use ($courseId) {
                    $q->where('status', 'enrolled')
                        ->where('course_id', $courseId);
                });
            }

            $students = $studentsQuery->get();

            if ($students->isEmpty()) {
                DB::rollBack();
                return back()->withErrors(['error' => 'No students matched the selected invoice generation filters.']);
            }

            $generatedCount = 0;
            $skippedCount = 0;
            $todayPrefix = 'INV-' . now()->format('Ymd') . '-';
            $invoiceSequence = (int) (
                FeeRecord::where('invoice_number', 'like', $todayPrefix . '%')
                    ->selectRaw('MAX(CAST(SUBSTRING(invoice_number, -6) AS UNSIGNED)) as max_sequence')
                    ->value('max_sequence') ?? 0
            );

            foreach ($students as $student) {
                // Check if invoice already exists
                $existingRecord = FeeRecord::where('user_id', $student->id)
                    ->where('fee_structure_id', $feeStructure->id)
                    ->first();

                if ($existingRecord) {
                    $skippedCount++;
                    continue;
                }

                $invoiceNumber = $this->generateInvoiceNumber($invoiceSequence);

                FeeRecord::create([
                    'user_id' => $student->id,
                    'fee_structure_id' => $feeStructure->id,
                    'invoice_number' => $invoiceNumber,
                    'amount' => $feeStructure->amount,
                    'discount_amount' => 0,
                    'late_fee' => 0,
                    'total_amount' => $feeStructure->amount,
                    'paid_amount' => 0,
                    'balance_amount' => $feeStructure->amount,
                    'status' => 'pending',
                    'due_date' => $request->filled('due_date')
                        ? Carbon::parse($request->due_date)->toDateString()
                        : ($feeStructure->due_date ?? now()->addDays(30)),
                ]);

                $generatedCount++;
            }

            DB::commit();

            $message = "Generated {$generatedCount} invoices.";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} existing records.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to generate invoices: ' . $e->getMessage()]);
        }
    }

    public function demandNotice(FeeRecord $fee)
    {
        $fee->load(['student.studentProfile.department', 'feeStructure.academicYear', 'feeStructure.semester']);

        return view('admin.fees.demand-notice', compact('fee'));
    }

    public function receipt(FeeRecord $fee)
    {
        $fee->load(['student.studentProfile.department', 'feeStructure.academicYear', 'feeStructure.semester']);
        $payments = Payment::where('fee_record_id', $fee->id)
            ->where('status', 'completed')
            ->orderBy('payment_date', 'asc')
            ->get();

        $runningPaid = 0;
        $paymentRows = $payments->map(function ($payment) use (&$runningPaid, $fee) {
            $runningPaid += $payment->amount;
            return [
                'payment' => $payment,
                'paid_to_date' => $runningPaid,
                'balance_after' => max($fee->total_amount - $runningPaid, 0),
            ];
        });

        $receiptNumber = ReceiptVerificationService::summaryReceiptNumber($fee);
        $verificationCode = ReceiptVerificationService::summaryVerificationCode($fee);
        $verificationUrl = route('receipts.verify');

        return view('admin.fees.receipt', compact('fee', 'payments', 'paymentRows', 'receiptNumber', 'verificationCode', 'verificationUrl'));
    }

    public function transactionReceipt(Payment $payment)
    {
        $payment->load([
            'feeRecord.student.studentProfile.department',
            'feeRecord.feeStructure.academicYear',
            'feeRecord.feeStructure.semester',
            'processedBy',
        ]);

        if (!$payment->feeRecord) {
            return back()->withErrors(['error' => 'No fee record attached to this payment.']);
        }

        $fee = $payment->feeRecord;
        $receiptNumber = ReceiptVerificationService::transactionReceiptNumber($payment);
        $verificationCode = ReceiptVerificationService::transactionVerificationCode($payment);
        $verificationUrl = route('receipts.verify');

        return view('admin.fees.transaction-receipt', compact('payment', 'fee', 'receiptNumber', 'verificationCode', 'verificationUrl'));
    }

    private function generateInvoiceNumber(int &$sequence): string
    {
        do {
            $sequence++;
            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
        } while (FeeRecord::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    public function sendReminders(Request $request)
    {
        @set_time_limit(300);

        $request->validate([
            'reminder_type' => 'required|in:due_soon,overdue,all',
            'days_before_due' => 'nullable|integer|min:1|max:30',
        ]);

        try {
            $query = FeeRecord::with(['student', 'feeStructure'])
                ->whereIn('status', ['pending', 'partial']);

            switch ($request->reminder_type) {
                case 'due_soon':
                    $daysBefore = (int) ($request->days_before_due ?? 7);
                    $query->whereBetween('due_date', [now(), now()->addDays($daysBefore)]);
                    break;
                case 'overdue':
                    $query->where('due_date', '<', now());
                    break;
                case 'all':
                    // No additional filter
                    break;
            }

            $feeRecords = $query->get();
            $sentCount = 0;
            $failedCount = 0;

            foreach ($feeRecords as $feeRecord) {
                if (!$feeRecord->student || !$feeRecord->student->email) {
                    $failedCount++;
                    continue;
                }

                try {
                    // Send email reminder
                    Mail::to($feeRecord->student->email)
                        ->send(new \App\Mail\FeePaymentReminder($feeRecord));

                    $sentCount++;
                } catch (\Throwable $e) {
                    $failedCount++;
                    Log::error('Failed to send fee reminder: ' . $e->getMessage(), [
                        'fee_record_id' => $feeRecord->id,
                        'student_id' => $feeRecord->user_id,
                    ]);
                }
            }

            $message = "Sent {$sentCount} payment reminder(s) successfully.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} failed to send.";
            }

            return back()->with($sentCount > 0 ? 'success' : 'warning', $message);

        } catch (\Throwable $e) {
            Log::error('Error in sendReminders: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send reminders: ' . $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        // Implementation for exporting fee records to Excel/CSV
        // This would use Laravel Excel or similar package
        return back()->with('info', 'Export functionality will be implemented.');
    }

    // Fee Structures Management
    public function structures()
    {

        $feeStructures = FeeStructure::with(['academicYear', 'semester'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.fees.structures.index', compact('feeStructures'));
    }

    public function createStructure()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.fees.structures.create', compact('academicYears', 'semesters'));
    }

    public function storeStructure(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:tuition,registration,library,laboratory,technology,activity,other',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:one_time,semester,monthly,annual',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'applicable_to' => 'nullable|array',
            'is_mandatory' => 'boolean',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_days' => 'nullable|integer|min:1',
        ]);


        try {
            DB::beginTransaction();

            FeeStructure::create($request->all());

            DB::commit();

            return redirect()->route('admin.fees.structures.index')
                ->with('success', 'Fee structure created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create fee structure: ' . $e->getMessage()]);
        }
    }

    public function showStructure(FeeStructure $feeStructure)
    {
        $feeStructure->load(['academicYear', 'semester']);

        // Get related fee records
        $feeRecords = FeeRecord::where('fee_structure_id', $feeStructure->id)
            ->with(['student'])
            ->paginate(10);

        return view('admin.fees.structures.show', compact('feeStructure', 'feeRecords'));
    }

    public function editStructure(FeeStructure $feeStructure)
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.fees.structures.edit', compact('feeStructure', 'academicYears', 'semesters'));
    }

    public function updateStructure(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:tuition,registration,library,laboratory,technology,activity,other',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:one_time,semester,monthly,annual',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'applicable_to' => 'nullable|array',
            'is_mandatory' => 'boolean',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_days' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $feeStructure->update($request->all());

            DB::commit();

            return redirect()->route('admin.fees.structures.index')
                ->with('success', 'Fee structure updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update fee structure: ' . $e->getMessage()]);
        }
    }

    public function destroyStructure(FeeStructure $feeStructure)
    {
        // Check if there are any fee records using this structure
        $recordCount = FeeRecord::where('fee_structure_id', $feeStructure->id)->count();

        if ($recordCount > 0) {
            return back()->withErrors(['error' => "Cannot delete fee structure. It is being used by {$recordCount} fee record(s)."]);
        }

        try {
            DB::beginTransaction();

            // Log the deletion
            // activity()
            //     ->performedOn($feeStructure)
            //     ->causedBy(auth()->user())
            //     ->log('Fee structure deleted');

            $feeStructure->delete();

            DB::commit();

            return redirect()->route('admin.fees.structures.index')
                ->with('success', 'Fee structure deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete fee structure: ' . $e->getMessage()]);
        }
    }
}
