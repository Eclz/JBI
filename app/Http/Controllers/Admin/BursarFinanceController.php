<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\FinanceRevenue;
use App\Models\FinanceBudget;
use App\Models\FinanceExpense;
use App\Models\Supplier;
use App\Models\VendorInvoice;
use App\Models\SponsorInvoice;
use App\Models\PayrollRecord;
use App\Models\UniversityAsset;
use App\Models\BankAccount;
use App\Models\ResearchGrant;
use App\Models\GeneralLedgerEntry;
use App\Models\FinanceAuditLog;
use App\Models\Department;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class BursarFinanceController extends Controller
{
    /**
     * Executive Finance Dashboard
     */
    public function dashboard()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        
        $totalTuitionCollected = FeeRecord::sum('paid_amount');
        $totalOutstandingFees = FeeRecord::sum('balance_amount');
        $totalOtherRevenue = FinanceRevenue::sum('amount');
        $totalExpenses = FinanceExpense::sum('amount');
        $totalPayrollPaid = PayrollRecord::sum('net_salary');
        $totalAssetsValue = UniversityAsset::sum('current_value');

        $recentRevenues = FinanceRevenue::latest()->take(5)->get();
        $recentExpenses = FinanceExpense::with('department')->latest()->take(5)->get();
        $budgets = FinanceBudget::with('department')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('admin.finance.index', compact(
            'currencyCode',
            'totalTuitionCollected',
            'totalOutstandingFees',
            'totalOtherRevenue',
            'totalExpenses',
            'totalPayrollPaid',
            'totalAssetsValue',
            'recentRevenues',
            'recentExpenses',
            'budgets',
            'bankAccounts'
        ));
    }

    /**
     * 1. Revenue Management (Non-tuition streams)
     */
    public function revenue()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $revenues = FinanceRevenue::with('receiver')->latest()->paginate(15);
        $totalRevenue = FinanceRevenue::sum('amount');

        return view('admin.finance.revenue.index', compact('revenues', 'totalRevenue', 'currencyCode'));
    }

    public function storeRevenue(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'payer_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['revenue_code'] = 'REV-' . date('Ymd') . '-' . rand(100, 999);
        $validated['received_by'] = auth()->id();

        FinanceRevenue::create($validated);

        FinanceAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Create Revenue Record',
            'module' => 'Revenue Management',
            'details' => "Recorded revenue {$validated['revenue_code']} of UGX " . number_format($validated['amount'], 2),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.finance.revenue.index')
            ->with('success', 'Revenue record saved successfully.');
    }

    /**
     * 2. Budget Management
     */
    public function budgets()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $budgets = FinanceBudget::with(['department', 'approver'])->latest()->paginate(15);
        $departments = Department::orderBy('name')->get();

        return view('admin.finance.budgets.index', compact('budgets', 'departments', 'currencyCode'));
    }

    public function storeBudget(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        $validated['budget_code'] = 'BDG-' . date('Y') . '-' . rand(100, 999);
        $validated['status'] = 'approved';
        $validated['approved_by'] = auth()->id();

        FinanceBudget::create($validated);

        return redirect()->route('admin.finance.budgets.index')
            ->with('success', 'Department Budget allocated successfully.');
    }

    /**
     * 3. Expenditure Management
     */
    public function expenses()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $expenses = FinanceExpense::with(['department', 'requester', 'approver'])->latest()->paginate(15);
        $departments = Department::orderBy('name')->get();

        return view('admin.finance.expenses.index', compact('expenses', 'departments', 'currencyCode'));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['expense_number'] = 'EXP-' . date('Ymd') . '-' . rand(100, 999);
        $validated['requested_by'] = auth()->id();
        $validated['approved_by'] = auth()->id();
        $validated['status'] = 'approved';

        FinanceExpense::create($validated);

        // Update budget spent amount
        $budget = FinanceBudget::where('department_id', $validated['department_id'])->first();
        if ($budget) {
            $budget->increment('spent_amount', $validated['amount']);
        }

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Expense recorded & approved successfully.');
    }

    /**
     * 4. Accounts Payable (Suppliers & Vendor Invoices)
     */
    public function payables()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $suppliers = Supplier::withCount('invoices')->latest()->get();
        $invoices = VendorInvoice::with('supplier')->latest()->paginate(15);

        return view('admin.finance.payables.index', compact('suppliers', 'invoices', 'currencyCode'));
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'tax_pin' => 'nullable|string',
        ]);

        $validated['supplier_code'] = 'SUP-' . rand(1000, 9999);

        Supplier::create($validated);

        return redirect()->route('admin.finance.payables.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function storeVendorInvoice(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
        ]);

        VendorInvoice::create($validated);

        return redirect()->route('admin.finance.payables.index')
            ->with('success', 'Vendor Invoice recorded successfully.');
    }

    /**
     * 5. Accounts Receivable (Student Debtors & Sponsors)
     */
    public function receivables()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $debtors = FeeRecord::where('balance_amount', '>', 0)
            ->with('student.studentProfile')
            ->orderBy('balance_amount', 'desc')
            ->paginate(15);

        $totalReceivable = FeeRecord::sum('balance_amount');

        return view('admin.finance.receivables.index', compact('debtors', 'totalReceivable', 'currencyCode'));
    }

    /**
     * 6. Payroll Management
     */
    public function payroll()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $payrolls = PayrollRecord::with('user')->latest()->paginate(15);
        $staffCount = User::whereIn('role', ['admin', 'faculty', 'bursar'])->count();

        return view('admin.finance.payroll.index', compact('payrolls', 'staffCount', 'currencyCode'));
    }

    public function generatePayroll(Request $request)
    {
        $request->validate([
            'month_year' => 'required|string',
        ]);

        $monthYear = $request->month_year;
        $staffMembers = User::whereIn('role', ['admin', 'faculty', 'bursar'])->get();

        foreach ($staffMembers as $staff) {
            $basic = $staff->role === 'admin' ? 3500000 : 2800000;
            $allowances = 500000;
            $gross = $basic + $allowances;
            $tax = $gross * 0.10; // 10% PAYE
            $pension = $gross * 0.05; // 5% NSSF
            $net = $gross - $tax - $pension;

            PayrollRecord::updateOrCreate(
                ['user_id' => $staff->id, 'month_year' => $monthYear],
                [
                    'basic_salary' => $basic,
                    'total_allowances' => $allowances,
                    'gross_salary' => $gross,
                    'tax_deductions' => $tax,
                    'pension_deductions' => $pension,
                    'net_salary' => $net,
                    'payment_date' => now(),
                    'status' => 'processed',
                ]
            );
        }

        return redirect()->route('admin.finance.payroll.index')
            ->with('success', "Payroll generated successfully for {$monthYear}.");
    }

    /**
     * 7. Asset Management
     */
    public function assets()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $assets = UniversityAsset::with('department')->latest()->paginate(15);
        $departments = Department::orderBy('name')->get();
        $totalAssetValue = UniversityAsset::sum('current_value');

        return view('admin.finance.assets.index', compact('assets', 'departments', 'totalAssetValue', 'currencyCode'));
    }

    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'category' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'location' => 'nullable|string',
        ]);

        $validated['asset_tag'] = 'AST-' . strtoupper(substr($validated['category'], 0, 3)) . '-' . rand(1000, 9999);
        $validated['current_value'] = $validated['purchase_cost'];

        UniversityAsset::create($validated);

        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset tagged and registered successfully.');
    }

    /**
     * 8. Banking & Cash Management
     */
    public function banking()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $accounts = BankAccount::latest()->get();

        return view('admin.finance.banking.index', compact('accounts', 'currencyCode'));
    }

    public function storeBankAccount(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number',
            'account_name' => 'required|string|max:255',
            'branch' => 'nullable|string',
            'current_balance' => 'required|numeric|min:0',
        ]);

        BankAccount::create($validated);

        return redirect()->route('admin.finance.banking.index')
            ->with('success', 'Bank Account added successfully.');
    }

    /**
     * 9. Research Grants
     */
    public function grants()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $grants = ResearchGrant::with('principalInvestigator')->latest()->paginate(15);
        $professors = User::whereIn('role', ['admin', 'faculty'])->get();

        return view('admin.finance.grants.index', compact('grants', 'professors', 'currencyCode'));
    }

    public function storeGrant(Request $request)
    {
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'donor_organization' => 'required|string|max:255',
            'total_grant_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'principal_investigator_id' => 'nullable|exists:users,id',
        ]);

        $validated['grant_code'] = 'GNT-' . date('Y') . '-' . rand(100, 999);
        $validated['disbursed_amount'] = $validated['total_grant_amount'];

        ResearchGrant::create($validated);

        return redirect()->route('admin.finance.grants.index')
            ->with('success', 'Research Grant registered successfully.');
    }

    /**
     * 10. Financial Statements & General Ledger
     */
    public function reports()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'UGX');
        $tuitionRevenue = FeeRecord::sum('paid_amount');
        $otherRevenue = FinanceRevenue::sum('amount');
        $totalRevenue = $tuitionRevenue + $otherRevenue;

        $operatingExpenses = FinanceExpense::sum('amount');
        $payrollExpenses = PayrollRecord::sum('net_salary');
        $totalExpenses = $operatingExpenses + $payrollExpenses;

        $netSurplus = $totalRevenue - $totalExpenses;

        return view('admin.finance.reports.index', compact(
            'currencyCode',
            'tuitionRevenue',
            'otherRevenue',
            'totalRevenue',
            'operatingExpenses',
            'payrollExpenses',
            'totalExpenses',
            'netSurplus'
        ));
    }

    /**
     * 11. Audit Trail & Compliance
     */
    public function audit()
    {
        $logs = FinanceAuditLog::with('user')->latest()->paginate(20);
        return view('admin.finance.audit.index', compact('logs'));
    }
}
