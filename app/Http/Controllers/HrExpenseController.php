<?php

namespace App\Http\Controllers;

use App\Models\HrExpenseClaim;
use Illuminate\Http\Request;

class HrExpenseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'claim_date' => 'required|date',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data['status'] = 'Pending';
        
        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('expenses', 'public');
        }

        HrExpenseClaim::create($data);

        return back()->with('success', 'Expense claim submitted successfully.');
    }

    public function updateStatus(Request $request, HrExpenseClaim $claim)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Paid',
            'rejection_reason' => 'required_if:status,Rejected|nullable|string',
        ]);

        $data = ['status' => $request->status];
        
        if (in_array($request->status, ['Approved', 'Paid'])) {
            $data['approved_by_id'] = auth()->id();
        }

        if ($request->status === 'Rejected') {
            $data['rejection_reason'] = $request->rejection_reason;
        }

        $claim->update($data);

        return back()->with('success', 'Expense claim status updated to ' . $request->status . '.');
    }
}
