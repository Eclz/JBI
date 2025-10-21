<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    public function index()
    {
        $feeRecords = FeeRecord::where('student_id', Auth::id())
            ->with('feeStructure')
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        $summary = [
            'total_fees' => FeeRecord::where('student_id', Auth::id())->sum('amount'),
            'paid_amount' => FeeRecord::where('student_id', Auth::id())->sum('paid_amount'),
            'outstanding' => FeeRecord::where('student_id', Auth::id())
                ->whereRaw('amount > paid_amount')
                ->sum(\DB::raw('amount - paid_amount')),
        ];

        return view('student.fees.index', compact('feeRecords', 'summary'));
    }

    public function pay(FeeRecord $fee)
    {
        if ($fee->student_id !== Auth::id()) {
            abort(403);
        }

        return view('student.fees.pay', compact('fee'));
    }

    public function processPayment(Request $request, FeeRecord $fee)
    {
        if ($fee->student_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($fee->amount - $fee->paid_amount),
            'payment_method' => 'required|in:cash,card,bank_transfer,online',
        ]);

        $fee->update([
            'paid_amount' => $fee->paid_amount + $request->amount,
            'payment_date' => now(),
            'payment_method' => $request->payment_method,
            'status' => ($fee->paid_amount + $request->amount >= $fee->amount) ? 'paid' : 'partial',
        ]);

        return redirect()->route('student.fees.index')
            ->with('success', 'Payment processed successfully.');
    }

    public function receipt(FeeRecord $fee)
    {
        if ($fee->student_id !== Auth::id()) {
            abort(403);
        }

        return view('student.fees.receipt', compact('fee'));
    }
}
