<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = BudgetRequest::with('department')->where('requested_by', auth()->id())->latest()->paginate(15);
        return view('faculty.dean.budgets.index', compact('budgets'));
    }

    public function create()
    {
        $deanId = auth()->id();
        $departments = \App\Models\Department::whereHas('faculty', function($q) use ($deanId) {
            $q->where('dean_id', $deanId);
        })->orderBy('name')->get();
        return view('faculty.dean.budgets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $validated['requested_by'] = auth()->id();
        $validated['status'] = 'Draft';

        BudgetRequest::create($validated);

        return redirect()->route('faculty.dean.budgets.index')->with('success', 'Budget request created as Draft.');
    }

    public function show(BudgetRequest $budget)
    {
        if ($budget->requested_by !== auth()->id()) abort(403);
        return view('faculty.dean.budgets.show', compact('budget'));
    }

    public function edit(BudgetRequest $budget)
    {
        if ($budget->requested_by !== auth()->id()) abort(403);
        if ($budget->status !== 'Draft' && $budget->status !== 'Rejected') {
            return back()->with('error', 'Only drafts or rejected budgets can be edited.');
        }

        $deanId = auth()->id();
        $departments = \App\Models\Department::whereHas('faculty', function($q) use ($deanId) {
            $q->where('dean_id', $deanId);
        })->orderBy('name')->get();
        return view('faculty.dean.budgets.edit', compact('budget', 'departments'));
    }

    public function update(Request $request, BudgetRequest $budget)
    {
        if ($budget->requested_by !== auth()->id()) abort(403);
        if ($budget->status !== 'Draft' && $budget->status !== 'Rejected') {
            return back()->with('error', 'Only drafts or rejected budgets can be edited.');
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($budget->status === 'Rejected') {
            $validated['status'] = 'Draft';
        }

        $budget->update($validated);

        return redirect()->route('faculty.dean.budgets.index')->with('success', 'Budget request updated.');
    }

    public function submit(BudgetRequest $budget)
    {
        if ($budget->requested_by !== auth()->id()) abort(403);
        if ($budget->status !== 'Draft') {
            return back()->with('error', 'Only drafts can be submitted.');
        }

        $budget->update(['status' => 'Pending Finance Approval']);

        return back()->with('success', 'Budget submitted for finance approval.');
    }

    public function destroy(BudgetRequest $budget)
    {
        if ($budget->requested_by !== auth()->id()) abort(403);
        if ($budget->status === 'Approved') {
            return back()->with('error', 'Approved budgets cannot be deleted.');
        }

        $budget->delete();

        return redirect()->route('faculty.dean.budgets.index')->with('success', 'Budget request deleted.');
    }
}
