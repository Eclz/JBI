<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\FinanceBudget;
use App\Models\FinanceExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentBudgetExpenseReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recording_an_expense_reconciles_department_budget(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Science Department',
            'code' => 'SCI',
            'is_active' => true,
        ]);

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-SCI',
            'academic_year' => '2026/2027',
            'department_id' => $department->id,
            'allocated_amount' => 50000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.finance.expenses.store'), [
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Lab Equipment & Reagents',
            'amount' => 12500.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'description' => 'Science Lab supplies',
        ]);

        $response->assertRedirect(route('admin.finance.expenses.index'));

        $this->assertDatabaseHas('finance_expenses', [
            'title' => 'Lab Equipment & Reagents',
            'department_id' => $department->id,
            'status' => 'approved',
        ]);

        $budget->refresh();
        $this->assertEquals(12500.00, (float) $budget->spent_amount);
    }

    public function test_recording_an_expense_auto_creates_budget_if_missing(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Art Department',
            'code' => 'ART',
            'is_active' => true,
        ]);

        $this->assertDatabaseMissing('finance_budgets', [
            'department_id' => $department->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.finance.expenses.store'), [
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Canvas & Paints',
            'amount' => 4500.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.finance.expenses.index'));

        $budget = FinanceBudget::where('department_id', $department->id)->first();
        $this->assertNotNull($budget);
        $this->assertEquals(4500.00, (float) $budget->spent_amount);
    }

    public function test_approving_a_pending_expense_reconciles_budget(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'History Department',
            'code' => 'HIST',
            'is_active' => true,
        ]);

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-HIST',
            'academic_year' => '2026/2027',
            'department_id' => $department->id,
            'allocated_amount' => 20000.00,
            'spent_amount' => 0.00,
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261019-777',
            'department_id' => $department->id,
            'category' => 'Travel & Fuel',
            'title' => 'Field Trip Transport',
            'amount' => 3000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'requested_by' => $admin->id,
        ]);

        $this->assertEquals(0.00, (float) $budget->spent_amount);

        // Approve via quick approve route
        $response = $this->actingAs($admin)->post(route('admin.finance.expenses.approve', $expense->id));
        $response->assertRedirect(route('admin.finance.expenses.index'));

        $budget->refresh();
        $expense->refresh();

        $this->assertEquals('approved', $expense->status);
        $this->assertEquals(3000.00, (float) $budget->spent_amount);
    }

    public function test_deleting_an_approved_expense_reverts_budget_spent_amount(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Math Department',
            'code' => 'MATH',
            'is_active' => true,
        ]);

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-MATH',
            'academic_year' => '2026/2027',
            'department_id' => $department->id,
            'allocated_amount' => 30000.00,
            'spent_amount' => 5000.00,
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261019-888',
            'department_id' => $department->id,
            'category' => 'Equipment & Repairs',
            'title' => 'Calculators Repair',
            'amount' => 5000.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'requested_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        $this->assertEquals(5000.00, (float) $budget->spent_amount);

        $response = $this->actingAs($admin)->delete(route('admin.finance.expenses.destroy', $expense->id));
        $response->assertRedirect(route('admin.finance.expenses.index'));

        $budget->refresh();
        $this->assertEquals(0.00, (float) $budget->spent_amount);
    }

    public function test_reconcile_budgets_endpoint_syncs_all_departments(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Music Department',
            'code' => 'MUS',
            'is_active' => true,
        ]);

        $budget = FinanceBudget::create([
            'budget_code' => 'BDG-2026-MUS',
            'academic_year' => '2026/2027',
            'department_id' => $department->id,
            'allocated_amount' => 40000.00,
            'spent_amount' => 0.00, // Deliberately out of sync
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        FinanceExpense::create([
            'expense_number' => 'EXP-20261019-001',
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Sheet Music',
            'amount' => 1200.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'requested_by' => $admin->id,
        ]);

        FinanceExpense::create([
            'expense_number' => 'EXP-20261019-002',
            'department_id' => $department->id,
            'category' => 'Equipment & Repairs',
            'title' => 'Piano Tuning',
            'amount' => 800.00,
            'expense_date' => now()->format('Y-m-d'),
            'status' => 'approved',
            'requested_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.finance.budgets.reconcile'));
        $response->assertRedirect(route('admin.finance.budgets.index'));

        $budget->refresh();
        $this->assertEquals(2000.00, (float) $budget->spent_amount);
    }
}
