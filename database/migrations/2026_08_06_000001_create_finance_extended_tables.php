<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for the 17 financial sub-modules.
     */
    public function up(): void
    {
        // 1. Revenue Management (Non-tuition income)
        Schema::create('finance_revenues', function (Blueprint $table) {
            $table->id();
            $table->string('revenue_code')->unique();
            $table->string('category'); // Govt Funding, Research Grant, Donation, Consultancy, Rental, Misc
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('payer_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Budget Management
        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('budget_code')->unique();
            $table->string('academic_year');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0.00);
            $table->decimal('committed_amount', 15, 2)->default(0.00);
            $table->string('status')->default('approved'); // draft, pending, approved, revised
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 3. Expenditure Management
        Schema::create('finance_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('category'); // Operational, Capital, Maintenance, Supplies, Utility, Travel
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('status')->default('approved'); // pending, approved, rejected, paid
            $table->string('payment_method')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Accounts Payable (Suppliers & Vendors)
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('supplier_code')->unique();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_pin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vendor_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('invoice_number');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('status')->default('unpaid'); // unpaid, partial, paid, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Accounts Receivable (Sponsors & Corporate Debtors)
        Schema::create('sponsor_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('sponsor_name');
            $table->string('contact_email')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('status')->default('pending'); // pending, partial, paid
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 6. Payroll Management
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('housing_allowance', 15, 2)->default(0.00);
            $table->decimal('transport_allowance', 15, 2)->default(0.00);
            $table->decimal('other_allowances', 15, 2)->default(0.00);
            $table->decimal('tax_deduction_rate', 5, 2)->default(10.00); // PAYE %
            $table->decimal('pension_deduction_rate', 5, 2)->default(5.00); // NSSF %
            $table->timestamps();
        });

        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('month_year'); // e.g. 2026-08
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('total_allowances', 15, 2);
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('tax_deductions', 15, 2);
            $table->decimal('pension_deductions', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->date('payment_date');
            $table->string('status')->default('processed'); // draft, processed, paid
            $table->timestamps();
        });

        // 7. Procurement Finance
        Schema::create('procurement_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_number')->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('item_description');
            $table->integer('quantity');
            $table->decimal('estimated_cost', 15, 2);
            $table->string('status')->default('approved'); // pending, verified, approved, ordered, fulfilled
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 8. Asset Management
        Schema::create('university_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique(); // Barcode/QR tag
            $table->string('asset_name');
            $table->string('category'); // Furniture, Computer, Equipment, Vehicle, Building
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->decimal('purchase_cost', 15, 2);
            $table->date('purchase_date');
            $table->decimal('current_value', 15, 2);
            $table->decimal('annual_depreciation_rate', 5, 2)->default(10.00); // %
            $table->string('status')->default('in_use'); // in_use, under_maintenance, disposed
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // 9. Banking and Cash Management
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number')->unique();
            $table->string('account_name');
            $table->string('branch')->nullable();
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->string('currency')->default('UGX');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Research Grants
        Schema::create('research_grants', function (Blueprint $table) {
            $table->id();
            $table->string('grant_code')->unique();
            $table->string('project_title');
            $table->string('donor_organization');
            $table->decimal('total_grant_amount', 15, 2);
            $table->decimal('disbursed_amount', 15, 2)->default(0.00);
            $table->decimal('spent_amount', 15, 2)->default(0.00);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('principal_investigator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('active'); // active, completed, closed
            $table->timestamps();
        });

        // 11. General Ledger & Financial Reporting
        Schema::create('general_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_code')->unique();
            $table->string('account_name'); // Assets, Liabilities, Equity, Revenue, Expenses
            $table->string('account_type'); // Debit, Credit
            $table->decimal('debit_amount', 15, 2)->default(0.00);
            $table->decimal('credit_amount', 15, 2)->default(0.00);
            $table->date('entry_date');
            $table->string('reference_module'); // Fees, Revenue, Expenses, Payroll, Payable, Asset
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 12. Audit & Compliance Trail
        Schema::create('finance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('module');
            $table->string('reference_id')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_audit_logs');
        Schema::dropIfExists('general_ledger_entries');
        Schema::dropIfExists('research_grants');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('university_assets');
        Schema::dropIfExists('procurement_requisitions');
        Schema::dropIfExists('payroll_records');
        Schema::dropIfExists('salary_structures');
        Schema::dropIfExists('sponsor_invoices');
        Schema::dropIfExists('vendor_invoices');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('finance_expenses');
        Schema::dropIfExists('finance_budgets');
        Schema::dropIfExists('finance_revenues');
    }
};
