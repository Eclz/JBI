<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\FinanceExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseDateRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_create_modal_and_edit_page_have_max_date_attribute_set_to_today(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Finance Department',
            'code' => 'FIN',
            'is_active' => true,
        ]);

        $today = date('Y-m-d');

        $response = $this->actingAs($admin)->get(route('admin.finance.expenses.index'));
        $response->assertOk();
        $response->assertSee('max="' . $today . '"', false);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261019-123',
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Office Paper',
            'amount' => 150.00,
            'expense_date' => $today,
            'requested_by' => $admin->id,
            'status' => 'pending',
        ]);

        $editResponse = $this->actingAs($admin)->get(route('admin.finance.expenses.edit', $expense->id));
        $editResponse->assertOk();
        $editResponse->assertSee('max="' . $today . '"', false);
    }

    public function test_cannot_create_expense_with_date_past_current_date(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Finance Department',
            'code' => 'FIN',
            'is_active' => true,
        ]);

        $futureDate = now()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($admin)->post(route('admin.finance.expenses.store'), [
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Future Stationery',
            'amount' => 500.00,
            'expense_date' => $futureDate,
            'description' => 'Should fail',
        ]);

        $response->assertSessionHasErrors(['expense_date']);
        $this->assertDatabaseMissing('finance_expenses', [
            'title' => 'Future Stationery',
        ]);
    }

    public function test_can_create_expense_with_today_or_past_date(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Finance Department',
            'code' => 'FIN',
            'is_active' => true,
        ]);

        $today = now()->format('Y-m-d');
        $pastDate = now()->subDays(5)->format('Y-m-d');

        $responseToday = $this->actingAs($admin)->post(route('admin.finance.expenses.store'), [
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Today Expense',
            'amount' => 200.00,
            'expense_date' => $today,
            'description' => 'Today expense notes',
        ]);

        $responseToday->assertRedirect(route('admin.finance.expenses.index'));
        $this->assertDatabaseHas('finance_expenses', [
            'title' => 'Today Expense',
        ]);

        $responsePast = $this->actingAs($admin)->post(route('admin.finance.expenses.store'), [
            'department_id' => $department->id,
            'category' => 'Equipment & Repairs',
            'title' => 'Past Expense',
            'amount' => 350.00,
            'expense_date' => $pastDate,
            'description' => 'Past expense notes',
        ]);

        $responsePast->assertRedirect(route('admin.finance.expenses.index'));
        $this->assertDatabaseHas('finance_expenses', [
            'title' => 'Past Expense',
        ]);
    }

    public function test_cannot_update_expense_with_date_past_current_date(): void
    {
        $admin = User::factory()->admin()->create(['role_id' => null]);
        $department = Department::create([
            'name' => 'Finance Department',
            'code' => 'FIN',
            'is_active' => true,
        ]);

        $expense = FinanceExpense::create([
            'expense_number' => 'EXP-20261019-999',
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Initial Expense',
            'amount' => 100.00,
            'expense_date' => now()->format('Y-m-d'),
            'requested_by' => $admin->id,
            'status' => 'pending',
        ]);

        $futureDate = now()->addDays(3)->format('Y-m-d');

        $response = $this->actingAs($admin)->put(route('admin.finance.expenses.update', $expense->id), [
            'department_id' => $department->id,
            'category' => 'Supplies & Stationery',
            'title' => 'Updated Future Expense',
            'amount' => 100.00,
            'expense_date' => $futureDate,
        ]);

        $response->assertSessionHasErrors(['expense_date']);
        $this->assertDatabaseMissing('finance_expenses', [
            'title' => 'Updated Future Expense',
        ]);
    }
}
