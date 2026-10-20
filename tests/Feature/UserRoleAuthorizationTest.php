<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_permissions_are_combined_only_from_active_roles_assigned_to_the_user(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $financeRole = Role::where('slug', 'finance_officer')->firstOrFail();
        $lecturerRole = Role::where('slug', 'lecturer')->firstOrFail();
        $unassignedRole = Role::where('slug', 'registrar')->firstOrFail();

        $user->syncRoles([$financeRole->id, $lecturerRole->id], $financeRole->id);

        $this->assertTrue($user->hasPermission('finance_hub', 'view'));
        $this->assertTrue($user->hasPermission('courses', 'edit'));
        $this->assertFalse($user->hasPermission('applications', 'view'));
        $this->assertFalse($user->hasPermission('registrar_hub', 'view'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->isAdmin());
        $this->assertSame(0, User::role('admin')->whereKey($user->id)->count());

        $user->load('roles');
        $user->syncRoles([$lecturerRole->id], $lecturerRole->id);

        $this->assertFalse($user->hasPermission('finance_hub', 'view'));
        $this->assertTrue($user->hasPermission('courses', 'edit'));
        $this->assertFalse($user->hasRole('finance_officer'));
        $this->assertFalse($user->hasRole('registrar'));

        $user->removeRole($lecturerRole);
        $this->assertNull($user->role_id);
        $this->assertFalse($user->hasPermission('courses', 'edit'));
        $this->assertFalse($user->hasRole('lecturer'));
    }

    public function test_clearing_all_roles_cannot_retain_super_admin_access_from_legacy_role_value(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $superAdminRole = Role::where('slug', 'super_administrator')->firstOrFail();
        $staffRole = Role::where('slug', 'finance_officer')->firstOrFail();

        $user->syncRoles([$superAdminRole->id, $staffRole->id], $superAdminRole->id);
        $this->assertTrue($user->isSuperAdmin());

        $user->syncRoles([]);
        $this->assertSame('student', $user->fresh()->role);
        $this->assertFalse(User::role('student')->whereKey($user->id)->exists());
        $user->forceFill(['role' => 'admin'])->save();
        $user->unsetRelation('roles')->unsetRelation('roleCatalog');

        $this->assertNull($user->role_id);
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->hasPermission('finance_hub', 'view'));
        $this->assertFalse($user->hasPermission('users', 'view'));
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_inactive_roles_do_not_authorize_the_user(): void
    {
        $user = User::factory()->create(['role' => 'student', 'role_id' => null]);
        $role = Role::where('slug', 'registrar')->firstOrFail();
        $user->syncRoles([$role->id], $role->id);
        $role->update(['is_active' => false]);
        $user->load('roles');

        $this->assertFalse($user->hasRole('registrar'));
        $this->assertFalse($user->hasPermission('registrar_hub', 'view'));
    }

    public function test_admin_category_role_cannot_open_another_roles_restricted_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $financeRole = Role::where('slug', 'finance_officer')->firstOrFail();
        $user->syncRoles([$financeRole->id], $financeRole->id);

        $this->actingAs($user)
            ->get(route('admin.finance.dashboard'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.registrar.dashboard'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.approval-hub.index'))
            ->assertForbidden();
    }

    public function test_user_edit_can_remove_every_assigned_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $superAdminRole = Role::where('slug', 'super_administrator')->firstOrFail();
        $admin->assignRole($superAdminRole, true);

        $user = User::factory()->create(['role' => 'admin', 'role_id' => null]);
        $role = Role::where('slug', 'finance_officer')->firstOrFail();
        $user->syncRoles([$role->id], $role->id);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'date_of_birth' => $user->date_of_birth?->toDateString(),
            'gender' => $user->gender,
            'address' => $user->address,
            'emergency_contact' => $user->emergency_contact,
            'emergency_phone' => $user->emergency_phone,
            'status' => 'active',
            'role_id' => '',
            'role_ids_present' => '1',
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $this->assertSame([], $user->fresh()->roles()->pluck('roles.id')->all());
        $this->assertNull($user->fresh()->role_id);
        $this->assertFalse($user->fresh()->hasPermission('finance_hub', 'view'));
    }
}
