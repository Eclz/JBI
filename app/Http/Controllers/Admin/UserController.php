<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['roleCatalog', 'studentProfile.department', 'facultyProfile.department'])
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%")
                      ->orWhereHas('roleCatalog', function ($roleQuery) use ($search) {
                          $roleQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('slug', 'like', "%{$search}%");
                      });
                });
            })
            ->when(request('role'), function ($query, $role) {
                $query->where(function ($q) use ($role) {
                    $q->where('role', $role)->orWhereHas('roleCatalog', fn ($roleQuery) => $roleQuery->where('slug', $role));
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['studentProfile.department', 'facultyProfile.department']);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $departments = Department::where('is_active', true)->get();
        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.create', compact('departments', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        DB::beginTransaction();

        try {
            $role = Role::findOrFail($request->role_id);
            $nameParts = preg_split('/\s+/', trim($request->name), 2);

            $userData = [
                'name' => $request->name,
                'first_name' => $nameParts[0] ?? $request->name,
                'last_name' => $nameParts[1] ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role->guard_role,
                'role_id' => $role->id,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'is_active' => $request->input('status', 'active') === 'active',
            ];

            if ($request->hasFile('profile_picture')) {
                $userData['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
            }

            $user = User::create($userData);

            // Create role-specific profile
            if ($role->guard_role === 'student') {
                StudentProfile::create([
                    'user_id' => $user->id,
                    'student_id' => $request->student_id,
                    'department_id' => $request->department_id,
                    'program' => $request->program,
                    'admission_date' => $request->admission_date,
                    'academic_status' => 'active',
                ]);
            } elseif ($role->guard_role === 'faculty') {
                FacultyProfile::create([
                    'user_id' => $user->id,
                    'employee_id' => $request->employee_id,
                    'department_id' => $request->department_id,
                    'position' => $request->position,
                    'hire_date' => $request->hire_date,
                    'employment_status' => 'active',
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create user.']);
        }
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $departments = Department::where('is_active', true)->get();
        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'departments', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        DB::beginTransaction();

        try {
            $role = Role::findOrFail($request->role_id);
            $nameParts = preg_split('/\s+/', trim($request->name), 2);

            $userData = [
                'name' => $request->name,
                'first_name' => $nameParts[0] ?? $request->name,
                'last_name' => $nameParts[1] ?? '',
                'email' => $request->email,
                'role' => $role->guard_role,
                'role_id' => $role->id,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'is_active' => $request->input('status', 'active') === 'active',
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
                }
                $userData['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
            }

            $user->update($userData);

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update user.']);
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deactivated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully.");
    }
}
