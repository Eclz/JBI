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
use App\Models\HrJobRole;
use Illuminate\Http\Request;

class BursarFinanceController extends Controller
{
    /**
     * Executive Finance Dashboard
     */
    public function dashboard()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        
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
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');

        FinanceAuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Create Revenue Record',
            'module' => 'Revenue Management',
            'details' => "Recorded revenue {$validated['revenue_code']} of {$currencyCode} " . number_format($validated['amount'], 2),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.finance.revenue.index')
            ->with('success', 'Revenue record saved successfully.');
    }

    public function showRevenue($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $revenue = FinanceRevenue::with('receiver')->findOrFail($id);
        return view('admin.finance.revenue.show', compact('revenue', 'currencyCode'));
    }

    public function editRevenue($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $revenue = FinanceRevenue::findOrFail($id);
        return view('admin.finance.revenue.edit', compact('revenue', 'currencyCode'));
    }

    public function updateRevenue(Request $request, $id)
    {
        $revenue = FinanceRevenue::findOrFail($id);
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
        
        $revenue->update($validated);

        return redirect()->route('admin.finance.revenue.index')
            ->with('success', 'Revenue record updated successfully.');
    }

    public function destroyRevenue($id)
    {
        $revenue = FinanceRevenue::findOrFail($id);
        $revenue->delete();

        return redirect()->route('admin.finance.revenue.index')
            ->with('success', 'Revenue record deleted successfully.');
    }

    /**
     * 2. Budget Management
     */
    public function budgets()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

    public function showBudget($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $budget = FinanceBudget::with(['department', 'approver'])->findOrFail($id);
        return view('admin.finance.budgets.show', compact('budget', 'currencyCode'));
    }

    public function editBudget($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $budget = FinanceBudget::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        return view('admin.finance.budgets.edit', compact('budget', 'departments', 'currencyCode'));
    }

    public function updateBudget(Request $request, $id)
    {
        $budget = FinanceBudget::findOrFail($id);
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        $budget->update($validated);

        return redirect()->route('admin.finance.budgets.index')
            ->with('success', 'Department Budget updated successfully.');
    }

    public function destroyBudget($id)
    {
        $budget = FinanceBudget::findOrFail($id);
        $budget->delete();

        return redirect()->route('admin.finance.budgets.index')
            ->with('success', 'Department Budget deleted successfully.');
    }

    /**
     * 3. Expenditure Management
     */
    public function expenses()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

    public function showExpense($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $expense = FinanceExpense::with(['department', 'requester', 'approver'])->findOrFail($id);
        return view('admin.finance.expenses.show', compact('expense', 'currencyCode'));
    }

    public function editExpense($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $expense = FinanceExpense::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        return view('admin.finance.expenses.edit', compact('expense', 'departments', 'currencyCode'));
    }

    public function updateExpense(Request $request, $id)
    {
        $expense = FinanceExpense::findOrFail($id);
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroyExpense($id)
    {
        $expense = FinanceExpense::findOrFail($id);
        $expense->delete();

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * 4. Accounts Payable (Suppliers & Vendor Invoices)
     */
    public function payables()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

    public function showSupplier($id)
    {
        $supplier = Supplier::with('invoices')->findOrFail($id);
        return view('admin.finance.payables.suppliers.show', compact('supplier'));
    }

    public function editSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.finance.payables.suppliers.edit', compact('supplier'));
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'tax_pin' => 'nullable|string',
        ]);
        $supplier->update($validated);
        return redirect()->route('admin.finance.payables.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('admin.finance.payables.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function showVendorInvoice($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $invoice = VendorInvoice::with('supplier')->findOrFail($id);
        return view('admin.finance.payables.invoices.show', compact('invoice', 'currencyCode'));
    }

    public function editVendorInvoice($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $invoice = VendorInvoice::findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.finance.payables.invoices.edit', compact('invoice', 'suppliers', 'currencyCode'));
    }

    public function updateVendorInvoice(Request $request, $id)
    {
        $invoice = VendorInvoice::findOrFail($id);
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
        ]);
        $invoice->update($validated);
        return redirect()->route('admin.finance.payables.index')
            ->with('success', 'Vendor Invoice updated successfully.');
    }

    public function destroyVendorInvoice($id)
    {
        $invoice = VendorInvoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('admin.finance.payables.index')
            ->with('success', 'Vendor Invoice deleted successfully.');
    }

    /**
     * 5. Accounts Receivable (Student Debtors & Sponsors)
     */
    public function receivables()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $debtors = FeeRecord::where('balance_amount', '>', 0)
            ->with('student.studentProfile')
            ->orderBy('balance_amount', 'desc')
            ->paginate(15);

        $totalReceivable = FeeRecord::sum('balance_amount');

        return view('admin.finance.receivables.index', compact('debtors', 'totalReceivable', 'currencyCode'));
    }

    public function showReceivable($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $record = FeeRecord::with('student.studentProfile')->findOrFail($id);
        return view('admin.finance.receivables.show', compact('record', 'currencyCode'));
    }

    /**
     * 6. Payroll Management
     */
    public function payroll()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $payrolls = PayrollRecord::with(['user.roleCatalog', 'user.hrProfile'])->latest()->paginate(15);
        $staffCount = User::whereIn('role', ['admin', 'faculty', 'bursar'])->count();

        return view('admin.finance.payroll.index', compact('payrolls', 'staffCount', 'currencyCode'));
    }

    public function generatePayroll(Request $request)
    {
        $request->validate([
            'month_year' => 'required|string',
        ]);

        $monthYear = $request->month_year;
        $staffMembers = User::whereIn('role', ['admin', 'faculty', 'bursar'])->with(['roleCatalog', 'hrProfile'])->get();

        foreach ($staffMembers as $staff) {
            // Check for configured salary band in HrJobRole
            $jobRole = null;
            if ($staff->role_id) {
                $jobRole = HrJobRole::where('role_id', $staff->role_id)->first();
            }
            if (!$jobRole && $staff->hrProfile?->salary_band) {
                $jobRole = HrJobRole::where('title', $staff->hrProfile->salary_band)->first();
            }
            if (!$jobRole && $staff->hrProfile?->job_title) {
                $jobRole = HrJobRole::where('title', $staff->hrProfile->job_title)->first();
            }
            if (!$jobRole && $staff->roleCatalog) {
                $jobRole = HrJobRole::where('title', $staff->roleCatalog->name)->first();
            }

            if ($staff->hrProfile && is_numeric($staff->hrProfile->salary_band) && (float)$staff->hrProfile->salary_band > 0) {
                $basic = (float) $staff->hrProfile->salary_band;
            } elseif ($jobRole && ($jobRole->salary_band_min || $jobRole->salary_band_max)) {
                $basic = (float) ($jobRole->salary_band_min ?: $jobRole->salary_band_max);
            } else {
                $basic = $staff->role === 'admin' ? 3500000 : 2800000;
            }

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

    public function editPayroll($id)
    {
        $payroll = PayrollRecord::with(['user.roleCatalog', 'user.hrProfile'])->findOrFail($id);
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');

        $jobRole = null;
        if ($payroll->user) {
            $staff = $payroll->user;
            if ($staff->role_id) {
                $jobRole = HrJobRole::where('role_id', $staff->role_id)->first();
            }
            if (!$jobRole && $staff->hrProfile?->salary_band) {
                $jobRole = HrJobRole::where('title', $staff->hrProfile->salary_band)->first();
            }
            if (!$jobRole && $staff->hrProfile?->job_title) {
                $jobRole = HrJobRole::where('title', $staff->hrProfile->job_title)->first();
            }
            if (!$jobRole && $staff->roleCatalog) {
                $jobRole = HrJobRole::where('title', $staff->roleCatalog->name)->first();
            }
        }

        return view('admin.finance.payroll.edit', compact('payroll', 'currencyCode', 'jobRole'));
    }

    public function showPayroll($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $payroll = PayrollRecord::with('user')->findOrFail($id);
        return view('admin.finance.payroll.show', compact('payroll', 'currencyCode'));
    }

    public function updatePayroll(Request $request, $id)
    {
        $payroll = PayrollRecord::findOrFail($id);
        
        if ($payroll->user_id === \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'You cannot edit your own salary.');
        }
        
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'total_allowances' => 'required|numeric|min:0',
            'tax_deductions' => 'required|numeric|min:0',
            'pension_deductions' => 'required|numeric|min:0',
        ]);
        
        $net_salary = $validated['basic_salary'] + $validated['total_allowances'] - $validated['tax_deductions'] - $validated['pension_deductions'];
        
        $payroll->update([
            'basic_salary' => $validated['basic_salary'],
            'total_allowances' => $validated['total_allowances'],
            'tax_deductions' => $validated['tax_deductions'],
            'pension_deductions' => $validated['pension_deductions'],
            'net_salary' => $net_salary,
        ]);
        
        return redirect()->route('admin.finance.payroll.index')
            ->with('success', 'Payroll record updated successfully.');
    }

    public function destroyPayroll($id)
    {
        $payroll = PayrollRecord::findOrFail($id);
        $payroll->delete();
        return redirect()->route('admin.finance.payroll.index')
            ->with('success', 'Payroll record deleted successfully.');
    }

    /**
     * 7. Asset Management
     */
    public function assets()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

    public function showAsset($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $asset = UniversityAsset::with('department')->findOrFail($id);
        return view('admin.finance.assets.show', compact('asset', 'currencyCode'));
    }

    public function editAsset($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $asset = UniversityAsset::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        return view('admin.finance.assets.edit', compact('asset', 'departments', 'currencyCode'));
    }

    public function updateAsset(Request $request, $id)
    {
        $asset = UniversityAsset::findOrFail($id);
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'category' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'location' => 'nullable|string',
        ]);
        $asset->update($validated);
        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroyAsset($id)
    {
        $asset = UniversityAsset::findOrFail($id);
        $asset->delete();
        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * 8. Banking & Cash Management
     */
    public function banking()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

        $validated['currency'] = SystemSetting::getSetting('default_currency', 'USD');

        BankAccount::create($validated);

        return redirect()->route('admin.finance.banking.index')
            ->with('success', 'Bank Account added successfully.');
    }

    public function showBankAccount($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $account = BankAccount::findOrFail($id);
        return view('admin.finance.banking.show', compact('account', 'currencyCode'));
    }

    public function editBankAccount($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $account = BankAccount::findOrFail($id);
        return view('admin.finance.banking.edit', compact('account', 'currencyCode'));
    }

    public function updateBankAccount(Request $request, $id)
    {
        $account = BankAccount::findOrFail($id);
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string',
            'account_name' => 'required|string|max:255',
            'branch' => 'nullable|string',
            'current_balance' => 'required|numeric|min:0',
        ]);
        $account->update($validated);
        return redirect()->route('admin.finance.banking.index')
            ->with('success', 'Bank Account updated successfully.');
    }

    public function destroyBankAccount($id)
    {
        $account = BankAccount::findOrFail($id);
        $account->delete();
        return redirect()->route('admin.finance.banking.index')
            ->with('success', 'Bank Account deleted successfully.');
    }

    /**
     * 9. Research Grants
     */
    public function grants()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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

    public function showGrant($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $grant = ResearchGrant::with('principalInvestigator')->findOrFail($id);
        return view('admin.finance.grants.show', compact('grant', 'currencyCode'));
    }

    public function editGrant($id)
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $grant = ResearchGrant::findOrFail($id);
        $professors = User::whereIn('role', ['admin', 'faculty'])->get();
        return view('admin.finance.grants.edit', compact('grant', 'professors', 'currencyCode'));
    }

    public function updateGrant(Request $request, $id)
    {
        $grant = ResearchGrant::findOrFail($id);
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'donor_organization' => 'required|string|max:255',
            'total_grant_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'principal_investigator_id' => 'nullable|exists:users,id',
        ]);
        $grant->update($validated);
        return redirect()->route('admin.finance.grants.index')
            ->with('success', 'Research Grant updated successfully.');
    }

    public function destroyGrant($id)
    {
        $grant = ResearchGrant::findOrFail($id);
        $grant->delete();
        return redirect()->route('admin.finance.grants.index')
            ->with('success', 'Research Grant deleted successfully.');
    }

    /**
     * 10. Financial Statements & General Ledger
     */
    public function reports()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
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
