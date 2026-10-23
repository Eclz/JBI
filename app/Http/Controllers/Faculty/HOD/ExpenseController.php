<?php

namespace App\Http\Controllers\Faculty\HOD;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\FinanceBudget;
use App\Models\FinanceExpense;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Get the department where the current user is Head of Department.
     */
    private function getDepartment()
    {
        $user = auth()->user();
        $department = Department::where('head_of_department_id', $user->id)->first();

        if (!$department && ($user->isSuperAdmin() || $user->isAdmin())) {
            $department = Department::first();
        }

        if (!$department) {
            abort(404, 'No departmental assignment found for this Head of Department.');
        }

        return $department;
    }

    /**
     * Display a listing of department expenses and budget alignment overview.
     */
    public function index(Request $request)
    {
        $department = $this->getDepartment();

        // 1. Fetch latest department budget details
        $budget = FinanceBudget::where('department_id', $department->id)->latest()->first();
        $allocatedAmount = (float) ($budget?->allocated_amount ?? 0);
        $spentAmount = (float) FinanceExpense::where('department_id', $department->id)
            ->where('status', 'approved')
            ->sum('amount');
        $pendingAmount = (float) FinanceExpense::where('department_id', $department->id)
            ->where('status', 'pending')
            ->sum('amount');
        $remainingBudget = max(0, $allocatedAmount - $spentAmount);
        $utilizationPercentage = $allocatedAmount > 0 
            ? min(100, round(($spentAmount / $allocatedAmount) * 100, 1)) 
            : 0;

        // 2. Query department expenses with filtering
        $query = FinanceExpense::with(['requester', 'approver'])
            ->where('department_id', $department->id);

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('expense_number', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->input('date_to'));
        }

        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = [
            'Academic & Teaching',
            'Supplies & Stationery',
            'Equipment & Repairs',
            'Operational',
            'Travel & Fuel',
            'Events & Workshops',
            'Maintenance',
            'Utility',
            'Other',
        ];

        $paymentMethods = [
            'Petty Cash',
            'Cash',
            'Bank Transfer',
            'Cheque',
            'Mobile Money',
        ];

        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $canCreateExpense = $request->user()->hasPermission('expenses', 'create');
        $canViewExpense = $request->user()->hasPermission('expenses', 'view');
        $canEditExpense = $request->user()->hasPermission('expenses', 'edit');
        $canDeleteExpense = $request->user()->hasPermission('expenses', 'delete');

        return view('faculty.hod.expenses.index', compact(
            'department',
            'budget',
            'allocatedAmount',
            'spentAmount',
            'pendingAmount',
            'remainingBudget',
            'utilizationPercentage',
            'expenses',
            'categories',
            'paymentMethods',
            'currencyCode',
            'canCreateExpense',
            'canViewExpense',
            'canEditExpense',
            'canDeleteExpense'
        ));
    }

    /**
     * Store a newly recorded department expense and reconcile with budget.
     */
    public function store(Request $request)
    {
        $department = $this->getDepartment();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:pending,approved',
            'description' => 'nullable|string|max:2000',
        ], [
            'expense_date.before_or_equal' => 'Expense date cannot be in the future.',
        ]);

        $status = $validated['status'] ?? 'approved';
        $amount = (float) $validated['amount'];

        $expenseNumber = 'EXP-' . date('Ymd') . '-HOD' . rand(100, 999);

        $expense = FinanceExpense::create([
            'expense_number' => $expenseNumber,
            'department_id' => $department->id,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'amount' => $amount,
            'expense_date' => $validated['expense_date'],
            'status' => $status,
            'payment_method' => $validated['payment_method'] ?? 'Petty Cash',
            'requested_by' => auth()->id(),
            'approved_by' => $status === 'approved' ? auth()->id() : null,
            'description' => $validated['description'] ?? null,
        ]);

        // Budget alignment
        if ($status === 'approved') {
            $this->applyExpenseToBudget($department->id, $amount);
        } else {
            $this->commitExpenseToBudget($department->id, $amount);
        }

        // Budget overutilization check notice
        $budget = FinanceBudget::where('department_id', $department->id)->latest()->first();
        $warningNotice = '';
        if ($budget && $budget->allocated_amount > 0 && $budget->spent_amount > $budget->allocated_amount) {
            $currency = SystemSetting::getSetting('default_currency', 'USD');
            $overspent = number_format($budget->spent_amount - $budget->allocated_amount, 2);
            $warningNotice = " (Note: Department budget allocation exceeded by {$currency} {$overspent}.)";
        }

        return redirect()->route('faculty.hod.expenses.index')
            ->with('success', "Department expense recorded and aligned with budget successfully.{$warningNotice}");
    }

    /**
     * Display the specified department expense.
     */
    public function show($id)
    {
        $department = $this->getDepartment();
        $expense = FinanceExpense::with(['department', 'requester', 'approver'])
            ->where('department_id', $department->id)
            ->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'expense' => $expense,
                'currency' => SystemSetting::getSetting('default_currency', 'USD'),
            ]);
        }

        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        return view('faculty.hod.expenses.show', compact('department', 'expense', 'currencyCode'));
    }

    /**
     * Update an existing department expense.
     */
    public function update(Request $request, $id)
    {
        $department = $this->getDepartment();
        $expense = FinanceExpense::where('department_id', $department->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'description' => 'nullable|string|max:2000',
        ], [
            'expense_date.before_or_equal' => 'Expense date cannot be in the future.',
        ]);

        $oldStatus = $expense->status;
        $newStatus = $validated['status'] ?? $oldStatus;
        $oldAmount = (float) $expense->amount;
        $newAmount = (float) $validated['amount'];

        // Reconcile budget if status or amount changed
        if ($oldStatus === 'approved') {
            $this->revertExpenseFromBudget($department->id, $oldAmount);
        } elseif ($oldStatus === 'pending') {
            $this->uncommitExpenseFromBudget($department->id, $oldAmount);
        }

        if ($newStatus === 'approved') {
            $this->applyExpenseToBudget($department->id, $newAmount);
            if (!$expense->approved_by) {
                $validated['approved_by'] = auth()->id();
            }
        } elseif ($newStatus === 'pending') {
            $this->commitExpenseToBudget($department->id, $newAmount);
            $validated['approved_by'] = null;
        }

        $expense->update($validated);

        return redirect()->route('faculty.hod.expenses.index')
            ->with('success', 'Department expense updated and budget reconciled successfully.');
    }

    /**
     * Remove a department expense and adjust the budget.
     */
    public function destroy($id)
    {
        $department = $this->getDepartment();
        $expense = FinanceExpense::where('department_id', $department->id)->findOrFail($id);

        if ($expense->status === 'approved') {
            $this->revertExpenseFromBudget($department->id, (float) $expense->amount);
        } elseif ($expense->status === 'pending') {
            $this->uncommitExpenseFromBudget($department->id, (float) $expense->amount);
        }

        $expense->delete();

        return redirect()->route('faculty.hod.expenses.index')
            ->with('success', 'Department expense deleted and budget spent amount reverted.');
    }

    /**
     * Increment spent_amount on department budget (creates budget if missing).
     */
    private function applyExpenseToBudget($departmentId, float $amount): void
    {
        if ($amount <= 0 || !$departmentId) {
            return;
        }

        $budget = FinanceBudget::where('department_id', $departmentId)->latest()->first();

        if (!$budget) {
            $academicYear = AcademicYear::where('is_current', true)->first()?->name 
                ?? AcademicYear::where('is_active', true)->first()?->name 
                ?? date('Y') . '/' . (date('Y') + 1);

            $budget = FinanceBudget::create([
                'budget_code' => 'BDG-' . date('Y') . '-' . $departmentId . '-' . rand(10, 99),
                'academic_year' => $academicYear,
                'department_id' => $departmentId,
                'allocated_amount' => 0.00,
                'spent_amount' => 0.00,
                'committed_amount' => 0.00,
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);
        }

        $budget->increment('spent_amount', $amount);
    }

    /**
     * Decrement spent_amount on department budget.
     */
    private function revertExpenseFromBudget($departmentId, float $amount): void
    {
        if ($amount <= 0 || !$departmentId) {
            return;
        }

        $budget = FinanceBudget::where('department_id', $departmentId)->latest()->first();
        if ($budget) {
            $budget->decrement('spent_amount', min((float) $budget->spent_amount, $amount));
        }
    }

    /**
     * Increment committed_amount for pending expenses.
     */
    private function commitExpenseToBudget($departmentId, float $amount): void
    {
        if ($amount <= 0 || !$departmentId) {
            return;
        }

        $budget = FinanceBudget::where('department_id', $departmentId)->latest()->first();
        if ($budget) {
            $budget->increment('committed_amount', $amount);
        }
    }

    /**
     * Decrement committed_amount for pending expenses.
     */
    private function uncommitExpenseFromBudget($departmentId, float $amount): void
    {
        if ($amount <= 0 || !$departmentId) {
            return;
        }

        $budget = FinanceBudget::where('department_id', $departmentId)->latest()->first();
        if ($budget && $budget->committed_amount > 0) {
            $budget->decrement('committed_amount', min((float) $budget->committed_amount, $amount));
        }
    }
}
