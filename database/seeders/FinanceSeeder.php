<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceRevenue;
use App\Models\FinanceBudget;
use App\Models\FinanceExpense;
use App\Models\Supplier;
use App\Models\VendorInvoice;
use App\Models\PayrollRecord;
use App\Models\UniversityAsset;
use App\Models\BankAccount;
use App\Models\ResearchGrant;
use App\Models\Department;
use App\Models\User;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds for Finance Sub-Modules.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $dept = Department::first();
        $deptId = $dept?->id;

        // 1. Seed Bank Accounts
        BankAccount::updateOrCreate(
            ['account_number' => '9030001234567'],
            [
                'bank_name' => 'Stanbic Bank Uganda',
                'account_name' => 'JBI University Main Collection Account',
                'branch' => 'Kampala Main Branch',
                'current_balance' => 485000000.00,
                'currency' => 'UGX',
                'is_active' => true,
            ]
        );

        BankAccount::updateOrCreate(
            ['account_number' => '1020009876543'],
            [
                'bank_name' => 'Centenary Bank',
                'account_name' => 'JBI University Payroll & Operations',
                'branch' => 'Makerere Branch',
                'current_balance' => 150000000.00,
                'currency' => 'UGX',
                'is_active' => true,
            ]
        );

        // 2. Seed Non-Tuition Revenues
        $revenues = [
            [
                'revenue_code' => 'REV-20260801-001',
                'category' => 'Government Funding',
                'title' => 'National Higher Education Research Grant FY2026',
                'amount' => 120000000.00,
                'transaction_date' => now()->subDays(10),
                'payment_method' => 'Bank Transfer',
                'reference_number' => 'MOES-GRANT-991',
                'payer_name' => 'Ministry of Education & Sports',
                'notes' => 'Government annual subvention for STEM labs.',
                'received_by' => $admin?->id,
            ],
            [
                'revenue_code' => 'REV-20260802-002',
                'category' => 'Facility Rental',
                'title' => 'Main Auditorium Rental - Leadership Summit',
                'amount' => 8500000.00,
                'transaction_date' => now()->subDays(5),
                'payment_method' => 'EFT',
                'reference_number' => 'EFT-883921',
                'payer_name' => 'Global Leadership Institute',
                'notes' => 'Weekend facility rental income.',
                'received_by' => $admin?->id,
            ],
            [
                'revenue_code' => 'REV-20260803-003',
                'category' => 'Consultancy Income',
                'title' => 'Corporate IT Training & Advisory Project',
                'amount' => 15000000.00,
                'transaction_date' => now()->subDays(2),
                'payment_method' => 'Cheque',
                'reference_number' => 'CHQ-002931',
                'payer_name' => 'Enterprise Uganda',
                'notes' => 'Faculty consultancy services.',
                'received_by' => $admin?->id,
            ],
        ];

        foreach ($revenues as $rev) {
            FinanceRevenue::updateOrCreate(['revenue_code' => $rev['revenue_code']], $rev);
        }

        // 3. Seed Budgets
        if ($deptId) {
            FinanceBudget::updateOrCreate(
                ['department_id' => $deptId, 'academic_year' => '2026/2027'],
                [
                    'budget_code' => 'BDG-2026-' . $deptId,
                    'allocated_amount' => 250000000.00,
                    'spent_amount' => 45000000.00,
                    'committed_amount' => 12000000.00,
                    'status' => 'approved',
                    'approved_by' => $admin?->id,
                ]
            );
        }

        // 4. Seed Expenses
        if ($deptId) {
            FinanceExpense::updateOrCreate(
                ['expense_number' => 'EXP-20260801-101'],
                [
                    'department_id' => $deptId,
                    'category' => 'Supplies & Stationery',
                    'title' => 'Examination Paper Rolls & Toner Supplies',
                    'amount' => 4500000.00,
                    'expense_date' => now()->subDays(7),
                    'status' => 'approved',
                    'payment_method' => 'Bank Transfer',
                    'requested_by' => $admin?->id,
                    'approved_by' => $admin?->id,
                    'description' => 'Approved semester exam printing stationery.',
                ]
            );
        }

        // 5. Seed Suppliers & Accounts Payable
        $supplier = Supplier::updateOrCreate(
            ['supplier_code' => 'SUP-1001'],
            [
                'company_name' => 'Dell Technologies East Africa',
                'contact_person' => 'James Mugisha',
                'email' => 'sales@delleastafrica.com',
                'phone' => '+256 700 112233',
                'address' => 'Plot 45 Kampala Road, Uganda',
                'tax_pin' => 'TIN-90029102',
                'is_active' => true,
            ]
        );

        VendorInvoice::updateOrCreate(
            ['invoice_number' => 'INV-DELL-9012'],
            [
                'supplier_id' => $supplier->id,
                'amount' => 38000000.00,
                'paid_amount' => 15000000.00,
                'invoice_date' => now()->subDays(15),
                'due_date' => now()->addDays(15),
                'status' => 'partial',
                'notes' => 'Supply of 15 i7 Server Racks for Computer Lab 2.',
            ]
        );

        // 6. Seed University Assets
        UniversityAsset::updateOrCreate(
            ['asset_tag' => 'AST-COM-9901'],
            [
                'asset_name' => 'High-Performance AI Server Workstation',
                'category' => 'Computer Hardware',
                'department_id' => $deptId,
                'purchase_cost' => 18500000.00,
                'purchase_date' => now()->subMonths(3),
                'current_value' => 16650000.00,
                'annual_depreciation_rate' => 10.00,
                'status' => 'in_use',
                'location' => 'Tech Hub, Room 102',
            ]
        );

        // 7. Seed Research Grant
        ResearchGrant::updateOrCreate(
            ['grant_code' => 'GNT-2026-01'],
            [
                'project_title' => 'AI for Agricultural Yield Optimization in East Africa',
                'donor_organization' => 'African Development Bank & USAID',
                'total_grant_amount' => 180000000.00,
                'disbursed_amount' => 180000000.00,
                'spent_amount' => 35000000.00,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addYears(2),
                'principal_investigator_id' => $admin?->id,
                'status' => 'active',
            ]
        );
    }
}
