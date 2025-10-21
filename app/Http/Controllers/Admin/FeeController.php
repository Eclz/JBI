<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Http\Requests\StoreFeeRecordRequest;
use App\Http\Requests\ProcessPaymentRequest;
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

        return view('admin.fees.index', compact(
            'feeRecords',
            'feeStructures',
            'semesters',
            'totalCollected',
            'totalPending',
            'totalOverdue',
            'thisMonthCollection',
            'collectionChartLabels',
            'collectionChartData',
            'statusChartData'
        ));
    }

    public function show(FeeRecord $fee)
    {
        $fee->load(['student.studentProfile', 'feeStructure', 'processor']);

        return view('admin.fees.show', compact('fee'));
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

            // Update payment history
            $paymentHistory = $fee->payment_history ?? [];
            $paymentHistory[] = [
                'amount' => $paymentAmount,
                'date' => $request->payment_date,
                'method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'notes' => $request->payment_notes,
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

            return redirect()->route('admin.fees.show', $fee)
                ->with('success', 'Payment processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process payment: ' . $e->getMessage()]);
        }
    }

    public function generateInvoices(Request $request)
    {
        $request->validate([
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $feeStructure = FeeStructure::findOrFail($request->fee_structure_id);

            // Get students to generate invoices for
            $students = $request->student_ids
                ? User::whereIn('id', $request->student_ids)->where('role', 'student')->get()
                : User::where('role', 'student')->where('is_active', true)->get();

            $generatedCount = 0;
            $skippedCount = 0;

            foreach ($students as $student) {
                // Check if invoice already exists
                $existingRecord = FeeRecord::where('user_id', $student->id)
                    ->where('fee_structure_id', $feeStructure->id)
                    ->first();

                if ($existingRecord) {
                    $skippedCount++;
                    continue;
                }

                // Generate invoice number
                $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad(FeeRecord::max('id') + 1, 6, '0', STR_PAD_LEFT);

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
                    'due_date' => $feeStructure->due_date ?? now()->addDays(30),
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

    public function sendReminders(Request $request)
    {
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
                try {
                    // Send email reminder
                    Mail::to($feeRecord->student->email)
                        ->send(new \App\Mail\FeePaymentReminder($feeRecord));

                    $sentCount++;

                    // Log the reminder
                    // activity()
                    //     ->performedOn($feeRecord)
                    //     ->causedBy(auth()->user())
                    //     ->log('Payment reminder sent');
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to send fee reminder: ' . $e->getMessage(), [
                        'fee_record_id' => $feeRecord->id,
                        'student_id' => $feeRecord->user_id,
                    ]);
                }
            }

            $message = "Sent {$sentCount} payment reminder(s) successfully.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} failed.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
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
            'type' => 'required|in:tuition,library,laboratory,technology,activity,other',
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
            'type' => 'required|in:tuition,library,laboratory,technology,activity,other',
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
