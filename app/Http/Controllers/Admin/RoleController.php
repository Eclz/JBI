<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create', [
            'role' => new Role(['guard_role' => 'admin', 'is_active' => true]),
            'modules' => config('university_permissions.modules'),
            'actions' => config('university_permissions.actions'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name'], '_');
        $data['permissions'] = $this->normalizePermissions($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_system'] = false;

        Role::create($data);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'modules' => config('university_permissions.modules'),
            'actions' => config('university_permissions.actions'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->validated($request, $role);
        $data['permissions'] = $this->normalizePermissions($request);
        $data['is_active'] = $request->boolean('is_active');

        if (!$role->is_system) {
            $data['slug'] = Str::slug($data['name'], '_');
        } else {
            unset($data['name']);
            unset($data['slug']);
            unset($data['guard_role']);
        }

        $role->update($data);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system || $role->users()->exists()) {
            return back()->withErrors(['role' => 'This role cannot be deleted while it is a system role or assigned to users.']);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'guard_role' => ['required', Rule::in(['admin', 'faculty', 'student', 'parent'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
        ]);
    }

    private function normalizePermissions(Request $request): array
    {
        $allowedModules = array_keys(config('university_permissions.modules'));
        $allowedActions = array_keys(config('university_permissions.actions'));
        $input = $request->input('permissions', []);
        $permissions = [];

        foreach ($allowedModules as $module) {
            foreach ($allowedActions as $action) {
                $permissions[$module][$action] = isset($input[$module][$action]);
            }
        }

        return $permissions;
    }
}
