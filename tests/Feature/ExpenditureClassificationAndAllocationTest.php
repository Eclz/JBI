<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\FinanceBudget;
use App\Models\FinanceExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenditureClassificationAndAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    private function createBursarUser(): User
    {
        return User::factory()->admin()->create(['role_id' => null]);
    }

    /**
     * 1. Test taxonomy definition and auto-classification inference.
     */
    public function test_expenditure_taxonomy_and_auto_classification_logic(): void
    {
        $taxonomy = FinanceExpense::getCategoriesByFunction();

        $this->assertArrayHasKey(FinanceExpense::FUNCTION_ACADEMIC, $taxonomy);
        $this->assertArrayHasKey(FinanceExpense::FUNCTION_ADMINISTRATIVE, $taxonomy);
        $this->assertArrayHasKey(FinanceExpense::FUNCTION_OPERATIONAL, $taxonomy);

        $this->assertContains('Instructional Materials & Curriculum', $taxonomy[FinanceExpense::FUNCTION_ACADEMIC]);
        $this->assertContains('Finance, Audit & Compliance', $taxonomy[FinanceExpense::FUNCTION_ADMINISTRATIVE]);
        $this->assertContains('Campus Utilities (Electricity & Water)', $taxonomy[FinanceExpense::FUNCTION_OPERATIONAL]);

        // Category inference
        $this->assertEquals(
            FinanceExpense::FUNCTION_ACADEMIC,
            FinanceExpense::getFunctionForCategory('Examinations & Assessment')
        );

        $this->assertEquals(
            FinanceExpense::FUNCTION_ADMINISTRATIVE,
            FinanceExpense::getFunctionForCategory('Human Resources & Staff Welfare')
        );

        $this->assertEquals(
            FinanceExpense::FUNCTION_OPERATIONAL,
            FinanceExpense::getFunctionForCategory('Campus Utilities (Electricity & Water)')
        );

        // Department-driven inference
        $adminDept = Department::firstOrCreate(
            ['code' => 'HR-DIR'],
            [
                'name' => 'Human Resources Directorate',
                'type' => Department::TYPE_ADMINISTRATIVE,
                'is_active' => true,
            ]
        );

        $this->assertEquals(
            FinanceExpense::FUNCTION_ADMINISTRATIVE,
            FinanceExpense::getFunctionForCategory('Supplies & Stationery', $adminDept->id)
        );

        $opsDept = Department::firstOrCreate(
            ['code' => 'OPS-SEC'],
            [
                'name' => 'Campus Safety & Security',
                'type' => Department::TYPE_OPERATIONAL,
                'is_active' => true,
            ]
        );

        $this->assertEquals(
            FinanceExpense::FUNCTION_OPERATIONAL,
            FinanceExpense::getFunctionForCategory('Supplies & Stationery', $opsDept->id)
        );
    }

    /**
     * 2. Test costs are appropriately allocated across academic, administrative, and operational functions.
     */
    public function test_costs_are_appropriately_allocated_across_functional_departments(): void
    {
        $bursar = $this->createBursarUser();

        // Academic Department & Budget
        $academicDept = Department::firstOrCreate(
            ['code' => 'CS-ALLOC'],
            [
                'name' => 'Computer Science Department',
                'type' => Department::TYPE_ACADEMIC,
                'is_active' => true,
            ]
        );
        $academicBudget = FinanceBudget::create([
            'budget_code' => 'BDG-CS-ALLOC',
            'academic_year' => '2026/2027',
            'department_id' => $academicDept->id,
            'allocated_amount' => 50000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
            'approved_by' => $bursar->id,
        ]);

        // Administrative Department & Budget
        $adminDept = Department::firstOrCreate(
            ['code' => 'ADM-VC-ALLOC'],
            [
                'name' => 'Office of the Vice-Chancellor',
                'type' => Department::TYPE_ADMINISTRATIVE,
                'is_active' => true,
            ]
        );
        $adminBudget = FinanceBudget::create([
            'budget_code' => 'BDG-ADM-ALLOC',
            'academic_year' => '2026/2027',
            'department_id' => $adminDept->id,
            'allocated_amount' => 80000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
            'approved_by' => $bursar->id,
        ]);

        // Operational Department & Budget
        $opsDept = Department::firstOrCreate(
            ['code' => 'OPS-EST-ALLOC'],
            [
                'name' => 'Estates, Works & Facilities Management',
                'type' => Department::TYPE_OPERATIONAL,
                'is_active' => true,
            ]
        );
        $opsBudget = FinanceBudget::create([
            'budget_code' => 'BDG-OPS-ALLOC',
            'academic_year' => '2026/2027',
            'department_id' => $opsDept->id,
            'allocated_amount' => 100000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
            'approved_by' => $bursar->id,
        ]);

        // 1. Record Academic Expense
        $this->actingAs($bursar)->post(route('admin.finance.expenses.store'), [
            'department_id' => $academicDept->id,
            'classification' => 'academic',
            'category' => 'Instructional Materials & Curriculum',
            'title' => 'Software Engineering Lab Kits',
            'amount' => 12000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'description' => 'Academic lab kits for semester practicals',
        ])->assertRedirect(route('admin.finance.expenses.index'));

        // 2. Record Administrative Expense
        $this->actingAs($bursar)->post(route('admin.finance.expenses.store'), [
            'department_id' => $adminDept->id,
            'classification' => 'administrative',
            'category' => 'Finance, Audit & Compliance',
            'title' => 'University Annual Compliance Audit',
            'amount' => 20000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'description' => 'Retainer fee for external financial audit',
        ])->assertRedirect(route('admin.finance.expenses.index'));

        // 3. Record Operational Expense
        $this->actingAs($bursar)->post(route('admin.finance.expenses.store'), [
            'department_id' => $opsDept->id,
            'classification' => 'operational',
            'category' => 'Campus Utilities (Electricity & Water)',
            'title' => 'Campus Electricity Grid & Water Bill',
            'amount' => 35000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'description' => 'Main electrical transformer and water supply bill',
        ])->assertRedirect(route('admin.finance.expenses.index'));

        // Assert all three expenditures are recorded with correct classifications
        $this->assertDatabaseHas('finance_expenses', [
            'title' => 'Software Engineering Lab Kits',
            'department_id' => $academicDept->id,
            'classification' => 'academic',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('finance_expenses', [
            'title' => 'University Annual Compliance Audit',
            'department_id' => $adminDept->id,
            'classification' => 'administrative',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('finance_expenses', [
            'title' => 'Campus Electricity Grid & Water Bill',
            'department_id' => $opsDept->id,
            'classification' => 'operational',
            'status' => 'approved',
        ]);

        // Assert department budgets reconciled accurately
        $this->assertEquals(12000.00, (float) $academicBudget->fresh()->spent_amount);
        $this->assertEquals(20000.00, (float) $adminBudget->fresh()->spent_amount);
        $this->assertEquals(35000.00, (float) $opsBudget->fresh()->spent_amount);
    }

    /**
     * 3. Test that operational and administrative costs are not restricted to academic departments.
     */
    public function test_operational_and_administrative_costs_are_not_restricted_to_academic_departments(): void
    {
        $bursar = $this->createBursarUser();

        $academicDept = Department::firstOrCreate(
            ['code' => 'THEO-TEST'],
            [
                'name' => 'Theology Department',
                'type' => Department::TYPE_ACADEMIC,
                'is_active' => true,
            ]
        );
        $academicBudget = FinanceBudget::create([
            'budget_code' => 'BDG-THEO-TEST',
            'academic_year' => '2026/2027',
            'department_id' => $academicDept->id,
            'allocated_amount' => 40000.00,
            'spent_amount' => 15000.00,
            'status' => 'approved',
        ]);

        $estatesDept = Department::firstOrCreate(
            ['code' => 'EST-TEST'],
            [
                'name' => 'Estates & Facilities',
                'type' => Department::TYPE_OPERATIONAL,
                'is_active' => true,
            ]
        );
        $estatesBudget = FinanceBudget::create([
            'budget_code' => 'BDG-EST-TEST',
            'academic_year' => '2026/2027',
            'department_id' => $estatesDept->id,
            'allocated_amount' => 70000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
        ]);

        // Previously, utility bills were logged under academic departments
        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-MIGR-001',
            'department_id' => $academicDept->id,
            'classification' => 'operational',
            'category' => 'Campus Utilities (Electricity & Water)',
            'title' => 'Campus High Voltage Transformer Repair',
            'amount' => 15000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'requested_by' => $bursar->id,
            'approved_by' => $bursar->id,
        ]);

        // Re-allocate this expense to the operational department
        $response = $this->actingAs($bursar)->put(route('admin.finance.expenses.update', $expense->id), [
            'department_id' => $estatesDept->id,
            'classification' => 'operational',
            'category' => 'Facilities Repairs & Maintenance',
            'title' => 'Campus High Voltage Transformer Repair',
            'amount' => 15000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'description' => 'Correctly re-allocated from Academic Theology to Estates Facilities',
        ]);

        $response->assertRedirect(route('admin.finance.expenses.index'));

        // Assert expense is re-attributed to operational department
        $expense->refresh();
        $this->assertEquals($estatesDept->id, $expense->department_id);
        $this->assertEquals('operational', $expense->classification);

        // Academic budget spent amount is relieved, estates budget updated
        $this->assertEquals(0.00, (float) $academicBudget->fresh()->spent_amount);
        $this->assertEquals(15000.00, (float) $estatesBudget->fresh()->spent_amount);
    }

    /**
     * 4. Test Bursar index view displays institutional allocation breakdown and KPI cards.
     */
    public function test_bursar_expenses_index_displays_functional_allocation_breakdown(): void
    {
        $bursar = $this->createBursarUser();

        $academicDept = Department::firstOrCreate(['code' => 'BIB-TEST'], ['name' => 'Biblical Studies Department', 'type' => Department::TYPE_ACADEMIC]);
        $adminDept = Department::firstOrCreate(['code' => 'REG-TEST'], ['name' => 'Registry & Admissions Directorate', 'type' => Department::TYPE_ADMINISTRATIVE]);
        $opsDept = Department::firstOrCreate(['code' => 'TRN-TEST'], ['name' => 'Transport & Fleet Logistics', 'type' => Department::TYPE_OPERATIONAL]);

        FinanceExpense::create([
            'expense_number' => 'EXP-ACA-01',
            'department_id' => $academicDept->id,
            'classification' => 'academic',
            'category' => 'Examinations & Assessment',
            'title' => 'Exam Answer Booklets',
            'amount' => 10000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
        ]);

        FinanceExpense::create([
            'expense_number' => 'EXP-ADM-01',
            'department_id' => $adminDept->id,
            'classification' => 'administrative',
            'category' => 'Admissions & Student Registry',
            'title' => 'Student ID Smart Cards',
            'amount' => 15000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
        ]);

        FinanceExpense::create([
            'expense_number' => 'EXP-OPS-01',
            'department_id' => $opsDept->id,
            'classification' => 'operational',
            'category' => 'Transport, Fleet Maintenance & Fuel',
            'title' => 'Campus Shuttle Fleet Diesel',
            'amount' => 25000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($bursar)->get(route('admin.finance.expenses.index'));

        $response->assertOk();
        $response->assertSeeText('Expenditure Allocation', false);
        $response->assertSeeText('Classification Hub', false);
        $response->assertSeeText('Academic Functions');
        $response->assertSeeText('Administrative Functions');
        $response->assertSeeText('Operational Functions');
        $response->assertSeeText('Exam Answer Booklets');
        $response->assertSeeText('Student ID Smart Cards');
        $response->assertSeeText('Campus Shuttle Fleet Diesel');
        $response->assertSeeText('50,000.00'); // Grand total: 10k + 15k + 25k
    }

    /**
     * 5. Test filtering expenditures by institutional function.
     */
    public function test_bursar_can_filter_expenses_by_functional_area(): void
    {
        $bursar = $this->createBursarUser();

        $academicDept = Department::firstOrCreate(['code' => 'HIST-TEST'], ['name' => 'History Department', 'type' => Department::TYPE_ACADEMIC]);
        $adminDept = Department::firstOrCreate(['code' => 'LEG-TEST'], ['name' => 'Legal Directorate', 'type' => Department::TYPE_ADMINISTRATIVE]);

        FinanceExpense::create([
            'expense_number' => 'EXP-ACA-99',
            'department_id' => $academicDept->id,
            'classification' => 'academic',
            'category' => 'Instructional Materials & Curriculum',
            'title' => 'Ancient History Manuscripts',
            'amount' => 5000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
        ]);

        FinanceExpense::create([
            'expense_number' => 'EXP-ADM-99',
            'department_id' => $adminDept->id,
            'classification' => 'administrative',
            'category' => 'Legal, Professional & Consultancy',
            'title' => 'Legal Counsel Retainer',
            'amount' => 9000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
        ]);

        // Filter: Academic only
        $resAcademic = $this->actingAs($bursar)->get(route('admin.finance.expenses.index', ['classification' => 'academic']));
        $resAcademic->assertOk();
        $resAcademic->assertSeeText('Ancient History Manuscripts');
        $resAcademic->assertDontSeeText('Legal Counsel Retainer');

        // Filter: Administrative only
        $resAdmin = $this->actingAs($bursar)->get(route('admin.finance.expenses.index', ['classification' => 'administrative']));
        $resAdmin->assertOk();
        $resAdmin->assertSeeText('Legal Counsel Retainer');
        $resAdmin->assertDontSeeText('Ancient History Manuscripts');
    }

    /**
     * 6. Test expense voucher displays institutional functional classification and responsible department.
     */
    public function test_bursar_can_view_expense_voucher_with_functional_classification(): void
    {
        $bursar = $this->createBursarUser();

        $opsDept = Department::firstOrCreate(
            ['code' => 'OPS-HST-TEST'],
            [
                'name' => 'Hostels & Accommodation Department',
                'type' => Department::TYPE_OPERATIONAL,
                'is_active' => true,
            ]
        );

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-HST-2026',
            'department_id' => $opsDept->id,
            'classification' => 'operational',
            'category' => 'Hostels & Accommodation Operations',
            'title' => 'Student Hostel Bedding & Mattress Replacement',
            'amount' => 8400.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'requested_by' => $bursar->id,
            'approved_by' => $bursar->id,
            'description' => 'Replacing worn bedding in North Block hostel rooms',
        ]);

        $response = $this->actingAs($bursar)->get(route('admin.finance.expenses.show', $expense->id));

        $response->assertOk();
        $response->assertSeeText('EXPENDITURE VOUCHER');
        $response->assertSeeText('EXP-HST-2026');
        $response->assertSeeText('Operational Function');
        $response->assertSeeText('Hostels & Accommodation Department');
        $response->assertSeeText('Hostels & Accommodation Operations');
        $response->assertSeeText('8,400.00');
    }

    /**
     * 7. Test model scopes and helpers for functional classification and department.
     */
    public function test_model_scopes_and_helpers_for_functional_classification(): void
    {
        $academicDept = Department::firstOrCreate(['code' => 'DA-TEST'], ['name' => 'Dept A', 'type' => Department::TYPE_ACADEMIC]);
        $adminDept = Department::firstOrCreate(['code' => 'DB-TEST'], ['name' => 'Dept B', 'type' => Department::TYPE_ADMINISTRATIVE]);
        $opsDept = Department::firstOrCreate(['code' => 'DC-TEST'], ['name' => 'Dept C', 'type' => Department::TYPE_OPERATIONAL]);

        $this->assertTrue($academicDept->isAcademic());
        $this->assertFalse($academicDept->isAdministrative());
        $this->assertTrue($adminDept->isAdministrative());
        $this->assertTrue($opsDept->isOperational());

        $expA = FinanceExpense::create([
            'expense_number' => 'EXP-TEST-A',
            'department_id' => $academicDept->id,
            'classification' => 'academic',
            'category' => 'Instructional Materials & Curriculum',
            'title' => 'Curriculum Books',
            'amount' => 100.00,
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $expB = FinanceExpense::create([
            'expense_number' => 'EXP-TEST-B',
            'department_id' => $adminDept->id,
            'classification' => 'administrative',
            'category' => 'Executive & Institutional Governance',
            'title' => 'Council Meeting Expenses',
            'amount' => 200.00,
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $expC = FinanceExpense::create([
            'expense_number' => 'EXP-TEST-C',
            'department_id' => $opsDept->id,
            'classification' => 'operational',
            'category' => 'Campus Safety, Security & Surveillance',
            'title' => 'CCTV Repairs',
            'amount' => 300.00,
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $this->assertTrue($expA->isAcademic());
        $this->assertTrue($expB->isAdministrative());
        $this->assertTrue($expC->isOperational());

        $this->assertEquals(1, FinanceExpense::where('id', $expA->id)->academic()->count());
        $this->assertEquals(1, FinanceExpense::where('id', $expB->id)->administrative()->count());
        $this->assertEquals(1, FinanceExpense::where('id', $expC->id)->operational()->count());
        $this->assertEquals(1, FinanceExpense::where('id', $expC->id)->function('operational')->count());
    }
}
