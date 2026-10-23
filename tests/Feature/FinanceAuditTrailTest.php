<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_audit_page_loads_for_users_with_finance_hub_permission(): void
    {
        $role = Role::create([
            'name' => 'Finance Audit Viewer',
            'slug' => 'finance_audit_viewer',
            'guard_role' => 'admin',
            'permissions' => [
                'finance_hub' => ['view' => true],
            ],
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);

        $this->actingAs($user)
            ->get(route('admin.finance.audit.index'))
            ->assertOk()
            ->assertSee('Financial Audit Trail & Compliance');
    }
}
