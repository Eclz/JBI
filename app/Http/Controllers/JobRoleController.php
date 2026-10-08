<?php

namespace App\Http\Controllers;

use App\Models\HrJobRole;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->isStudent()) {
                abort(403, 'Students do not have access to the Human Resources module.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Automatically sync system roles so they are always included in HR job roles
        $systemRoles = Role::whereNotIn('slug', ['parent_guardian'])->get();
        foreach ($systemRoles as $sysRole) {
            $existing = HrJobRole::where('role_id', $sysRole->id)
                ->orWhere('title', $sysRole->name)
                ->first();

            $isFaculty = $sysRole->guard_role === 'faculty';
            $minBand = $isFaculty ? 2500000 : 3000000;
            $maxBand = $isFaculty ? 3500000 : 4500000;

            if (!$existing) {
                HrJobRole::create([
                    'title' => $sysRole->name,
                    'role_id' => $sysRole->id,
                    'description' => $sysRole->description,
                    'departments' => [],
                    'salary_band_min' => $minBand,
                    'salary_band_max' => $maxBand,
                    'is_active' => true,
                ]);
            } else {
                if (!$existing->role_id) {
                    $existing->role_id = $sysRole->id;
                    $existing->save();
                }
            }
        }

        $jobRoles = HrJobRole::with('role')->latest()->paginate(25);
        $user = Auth::user();
        $canCreate = $user && ($user->isSuperAdmin() || $user->hasPermission('roles', 'create') || $user->hasPermission('hr_core', 'create'));

        return view('human-resources.job-roles.index', compact('jobRoles', 'canCreate'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasPermission('roles', 'create') && !$user->hasPermission('hr_core', 'create'))) {
            abort(403, 'Role creation is reserved for the Super Administrator and authorized personnel.');
        }

        $roles = Role::whereNotIn('slug', ['parent_guardian'])->orderBy('name')->get();
        $departments = $this->getAvailableDepartments();

        return view('human-resources.job-roles.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasPermission('roles', 'create') && !$user->hasPermission('hr_core', 'create'))) {
            abort(403, 'Role creation is reserved for the Super Administrator and authorized personnel.');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'role_id' => 'nullable|exists:roles,id',
            'departments' => 'nullable|array',
            'departments.*' => 'string|max:255',
            'salary_band_min' => 'nullable|numeric|min:0',
            'salary_band_max' => 'nullable|numeric|min:0|gte:salary_band_min',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['departments'] = array_values(array_filter($request->departments ?? []));

        HrJobRole::create($data);

        return redirect()->route('human-resources.job-roles.index')->with('success', 'Job role created successfully.');
    }

    public function edit(HrJobRole $jobRole)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasPermission('roles', 'edit') && !$user->hasPermission('hr_core', 'edit') && !$user->hasPermission('human_resources', 'edit'))) {
            abort(403, 'You do not have permission to edit job roles.');
        }

        $roles = Role::whereNotIn('slug', ['parent_guardian'])->orderBy('name')->get();
        $departments = $this->getAvailableDepartments();

        return view('human-resources.job-roles.edit', compact('jobRole', 'roles', 'departments'));
    }

    public function update(Request $request, HrJobRole $jobRole)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasPermission('roles', 'edit') && !$user->hasPermission('hr_core', 'edit') && !$user->hasPermission('human_resources', 'edit'))) {
            abort(403, 'You do not have permission to edit job roles.');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'role_id' => 'nullable|exists:roles,id',
            'departments' => 'nullable|array',
            'departments.*' => 'string|max:255',
            'salary_band_min' => 'nullable|numeric|min:0',
            'salary_band_max' => 'nullable|numeric|min:0|gte:salary_band_min',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['departments'] = array_values(array_filter($request->departments ?? []));

        $jobRole->update($data);

        return redirect()->route('human-resources.job-roles.index')->with('success', 'Job role updated successfully.');
    }

    public function destroy(HrJobRole $jobRole)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasPermission('roles', 'delete') && !$user->hasPermission('hr_core', 'delete'))) {
            abort(403, 'Deleting job roles is reserved for the Super Administrator and authorized personnel.');
        }

        $jobRole->delete();
        return redirect()->route('human-resources.job-roles.index')->with('success', 'Job role deleted successfully.');
    }

    /**
     * Get unique sorted list of departments across academic and administrative departments
     */
    private function getAvailableDepartments(): \Illuminate\Support\Collection
    {
        $dbDepartments = Department::orderBy('name')->pluck('name')->toArray();
        $commonDepartments = [
            'Administration',
            'Finance & Bursar',
            'Human Resources',
            'Estates & Facilities',
            'Library Services',
            'Academic Affairs',
            'Admissions & Records',
            'ICT & Computing',
            'Student Affairs',
        ];

        return collect(array_merge($dbDepartments, $commonDepartments))->unique()->sort()->values();
    }
}
