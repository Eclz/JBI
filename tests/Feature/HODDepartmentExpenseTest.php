<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\FinanceBudget;
use App\Models\FinanceExpense;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HODDepartmentExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    private function createHODContext(): array
    {
        $hod = User::factory()->create(['role' => 'faculty']);
        $role = Role::where('name', 'hod')->orWhere('slug', 'head_of_department')->first();
        if ($role) {
            $hod->roleCatalog()->associate($role);
            $hod->save();
        }

        $faculty = Faculty::factory()->create();
        $department = Department::factory()->create([
            'head_of_department_id' => $hod->id,
            'is_active' => true,
            'faculty_id' => $faculty->id,
            'name' => 'Computer Science Department',
            'code' => 'CS',
        ]);

        return compact('hod', 'faculty', 'department');
    }

    public function test_hod_can_view_department_expenses_index_with_budget_summary(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $dept = $ctx['department'];

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-CS-001',
            'academic_year' => '2026/2027',
            'department_id' => $dept->id,
            'allocated_amount' => 45000.00,
            'spent_amount' => 5000.00,
            'status' => 'approved',
            'approved_by' => $hod->id,
        ]);

        $myExpense = FinanceExpense::create([
            'expense_number' => 'EXP-20261023-CS01',
            'department_id' => $dept->id,
            'category' => 'Equipment & Repairs',
            'title' => 'Server Maintenance Parts',
            'amount' => 5000.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
            'requested_by' => $hod->id,
        ]);

        $otherDept = Department::factory()->create(['name' => 'History Dept', 'code' => 'HIST']);
        $otherExpense = FinanceExpense::create([
            'expense_number' => 'EXP-20261023-HIST01',
            'department_id' => $otherDept->id,
            'category' => 'Supplies & Stationery',
            'title' => 'History Books and Archives',
            'amount' => 1200.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($hod)->get(route('faculty.hod.expenses.index'));

        $response->assertOk()
            ->assertSee('Department Expenses & Budget Alignment')
            ->assertSee('Computer Science Department')
            ->assertSee('BDG-2026-CS-001')
            ->assertSee('Server Maintenance Parts')
            ->assertSee('EXP-20261023-CS01')
            ->assertDontSee('History Books and Archives');
    }

    public function test_expense_controls_and_routes_require_the_matching_permission(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $department = $ctx['department'];
        $role = Role::where('slug', 'head_of_department')->firstOrFail();
        $role->update(['permissions' => ['expenses' => ['view' => true]]]);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261023-READONLY',
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Read-only expense',
            'amount' => 250.00,
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
            'requested_by' => $hod->id,
        ]);

        $this->actingAs($hod)
            ->get(route('faculty.hod.expenses.index'))
            ->assertOk()
            ->assertSee('View Details')
            ->assertDontSee('id="btn-record-expense-modal"', false)
            ->assertDontSee('id="record-expense-form"', false)
            ->assertDontSee('Edit Expense')
            ->assertDontSee('Delete Expense');

        $this->post(route('faculty.hod.expenses.store'), [])->assertForbidden();
        $this->put(route('faculty.hod.expenses.update', $expense), [])->assertForbidden();
        $this->delete(route('faculty.hod.expenses.destroy', $expense))->assertForbidden();
    }

    public function test_hod_can_record_approved_expense_and_reconciles_department_budget(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $dept = $ctx['department'];

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-CS-101',
            'academic_year' => '2026/2027',
            'department_id' => $dept->id,
            'allocated_amount' => 30000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
            'approved_by' => $hod->id,
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.expenses.store'), [
            'title' => 'Networking Switches and Cables',
            'category' => 'Equipment & Repairs',
            'amount' => 7500.00,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'Bank Transfer',
            'status' => 'approved',
            'description' => 'Upgrading lab network switches as per approved budget line.',
        ]);

        $response->assertRedirect(route('faculty.hod.expenses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('finance_expenses', [
            'department_id' => $dept->id,
            'title' => 'Networking Switches and Cables',
            'amount' => 7500.00,
            'status' => 'approved',
            'requested_by' => $hod->id,
        ]);

        $budget->refresh();
        $this->assertEquals(7500.00, (float) $budget->spent_amount);
    }

    public function test_recording_an_expense_auto_creates_budget_if_missing(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $dept = $ctx['department'];

        $this->assertDatabaseMissing('finance_budgets', [
            'department_id' => $dept->id,
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.expenses.store'), [
            'title' => 'Printer Toner & Paper',
            'category' => 'Supplies & Stationery',
            'amount' => 1800.00,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'Petty Cash',
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('faculty.hod.expenses.index'));

        $budget = FinanceBudget::where('department_id', $dept->id)->first();
        $this->assertNotNull($budget);
        $this->assertEquals(1800.00, (float) $budget->spent_amount);
    }

    public function test_hod_can_record_pending_expense_and_updates_committed_budget(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $dept = $ctx['department'];

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-CS-202',
            'academic_year' => '2026/2027',
            'department_id' => $dept->id,
            'allocated_amount' => 20000.00,
            'spent_amount' => 0.00,
            'committed_amount' => 0.00,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($hod)->post(route('faculty.hod.expenses.store'), [
            'title' => 'Workshop Venue Booking',
            'category' => 'Events & Workshops',
            'amount' => 3200.00,
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
            'description' => 'Awaiting Bursar disbursement approval.',
        ]);

        $response->assertRedirect(route('faculty.hod.expenses.index'));

        $this->assertDatabaseHas('finance_expenses', [
            'department_id' => $dept->id,
            'title' => 'Workshop Venue Booking',
            'status' => 'pending',
        ]);

        $budget->refresh();
        $this->assertEquals(0.00, (float) $budget->spent_amount);
        $this->assertEquals(3200.00, (float) $budget->committed_amount);
    }

    public function test_hod_can_update_expense_and_reconciles_budget(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $dept = $ctx['department'];

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-CS-303',
            'academic_year' => '2026/2027',
            'department_id' => $dept->id,
            'allocated_amount' => 25000.00,
            'spent_amount' => 4000.00,
            'status' => 'approved',
        ]);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261023-CS-303',
            'department_id' => $dept->id,
            'category' => 'Operational',
            'title' => 'Software Licenses',
            'amount' => 4000.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
            'requested_by' => $hod->id,
            'approved_by' => $hod->id,
        ]);

        $response = $this->actingAs($hod)->put(route('faculty.hod.expenses.update', $expense->id), [
            'title' => 'Software Licenses (Full Dept)',
            'category' => 'Operational',
            'amount' => 6500.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('faculty.hod.expenses.index'));

        $budget->refresh();
        $expense->refresh();

        $this->assertEquals(6500.00, (float) $expense->amount);
        $this->assertEquals(6500.00, (float) $budget->spent_amount);
    }

    public function test_hod_can_delete_expense_and_reverts_budget(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];
        $dept = $ctx['department'];

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-CS-404',
            'academic_year' => '2026/2027',
            'department_id' => $dept->id,
            'allocated_amount' => 15000.00,
            'spent_amount' => 2500.00,
            'status' => 'approved',
        ]);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261023-CS-404',
            'department_id' => $dept->id,
            'category' => 'Maintenance',
            'title' => 'Air Conditioner Repair',
            'amount' => 2500.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
            'requested_by' => $hod->id,
        ]);

        $response = $this->actingAs($hod)->delete(route('faculty.hod.expenses.destroy', $expense->id));

        $response->assertRedirect(route('faculty.hod.expenses.index'));
        $this->assertDatabaseMissing('finance_expenses', ['id' => $expense->id]);

        $budget->refresh();
        $this->assertEquals(0.00, (float) $budget->spent_amount);
    }

    public function test_non_hod_cannot_access_hod_expenses(): void
    {
        $lecturer = User::factory()->create(['role' => 'faculty']);

        // No department assignment as HOD
        $response = $this->actingAs($lecturer)->get(route('faculty.hod.expenses.index'));
        $this->assertTrue(in_array($response->status(), [403, 404], true));
    }

    public function test_hod_cannot_modify_other_department_expense(): void
    {
        $ctx = $this->createHODContext();
        $hod = $ctx['hod'];

        $otherDept = Department::factory()->create();
        $otherExpense = FinanceExpense::create([
            'expense_number' => 'EXP-OTHER-001',
            'department_id' => $otherDept->id,
            'category' => 'Operational',
            'title' => 'Other Dept Expense',
            'amount' => 1000.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($hod)->put(route('faculty.hod.expenses.update', $otherExpense->id), [
            'title' => 'Hacked Expense',
            'category' => 'Operational',
            'amount' => 5000.00,
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
        ]);

        $response->assertStatus(404);
        $this->assertEquals(1000.00, (float) $otherExpense->fresh()->amount);
    }
}
