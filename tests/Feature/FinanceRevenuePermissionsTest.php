<?php

namespace Tests\Feature;

use App\Models\FinanceRevenue;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceRevenuePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_revenue_page_hides_actions_the_finance_user_cannot_perform(): void
    {
        $role = Role::create([
            'name' => 'Revenue Viewer',
            'slug' => 'revenue_viewer',
            'guard_role' => 'admin',
            'permissions' => ['revenue' => ['view' => true]],
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);
        FinanceRevenue::create([
            'revenue_code' => 'REV-20261023-101',
            'category' => 'Donation',
            'title' => 'Viewer-visible revenue',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.finance.revenue.index'))
            ->assertOk()
            ->assertSee('Viewer-visible revenue')
            ->assertSee('title="View"', false)
            ->assertDontSee('title="Edit"', false)
            ->assertDontSee('title="Delete"', false)
            ->assertDontSee('id="addRevenueModal"', false);
    }

    public function test_revenue_mutations_require_their_matching_permissions(): void
    {
        $role = Role::create([
            'name' => 'Revenue Viewer',
            'slug' => 'revenue_viewer',
            'guard_role' => 'admin',
            'permissions' => ['revenue' => ['view' => true]],
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);
        $revenue = FinanceRevenue::create([
            'revenue_code' => 'REV-20261023-102',
            'category' => 'Donation',
            'title' => 'Protected revenue',
            'amount' => 150,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->post(route('admin.finance.revenue.store'), [])
            ->assertForbidden();

        $this->put(route('admin.finance.revenue.update', $revenue->id), [])
            ->assertForbidden();

        $this->delete(route('admin.finance.revenue.destroy', $revenue->id))
            ->assertForbidden();

        $this->get(route('admin.finance.revenue.edit', $revenue->id))
            ->assertForbidden();

        $this->get(route('admin.finance.revenue.show', $revenue->id))
            ->assertOk();
    }
}
