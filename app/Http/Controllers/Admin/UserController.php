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
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\AccountCreatedSetupPassword;

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
                'password' => Hash::make('ABCxyz,.?123'),
                'role' => $role->guard_role,
                'role_id' => $role->id,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'is_active' => $request->input('status', 'active') === 'active',
                'email_verified_at' => null,
                'must_change_password' => false,
            ];

            if ($request->hasFile('profile_picture')) {
                $userData['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
            }

            $user = User::create($userData);

            // Create role-specific profile
            if ($role->guard_role === 'student') {
                $deptId = $request->department_id ?: Department::where('is_active', true)->value('id');
                $admissionNumber = $request->student_id ?: ('ADM' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT));
                StudentProfile::create([
                    'user_id' => $user->id,
                    'admission_number' => $admissionNumber,
                    'student_id' => $request->student_id ?: $admissionNumber,
                    'department_id' => $deptId,
                    'program' => $request->program ?: 'General Studies',
                    'admission_date' => $request->admission_date ?: now(),
                    'status' => 'active',
                    'application_status' => 'approved',
                ]);
            } elseif ($role->guard_role === 'faculty') {
                $deptId = $request->department_id ?: Department::where('is_active', true)->value('id');
                FacultyProfile::create([
                    'user_id' => $user->id,
                    'employee_id' => $request->employee_id ?: ('EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT)),
                    'department_id' => $deptId,
                    'designation' => $role->name ?: 'Lecturer',
                    'position' => $role->name ?: 'Lecturer',
                    'qualification' => 'Master\'s / Bachelor\'s',
                    'joining_date' => $request->hire_date ?: now(),
                    'hire_date' => $request->hire_date ?: now(),
                    'employment_type' => 'full_time',
                    'employment_status' => 'active',
                    'status' => 'active',
                ]);
            }

            DB::commit();

            // Send account creation invitation email with password setup token
            try {
                $token = Password::broker()->createToken($user);
                Mail::to($user->email)->send(new AccountCreatedSetupPassword($user, $token));
            } catch (\Throwable $mailEx) {
                \Illuminate\Support\Facades\Log::warning('Failed to send account setup email: ' . $mailEx->getMessage());
            }

            return redirect()->route('admin.users.show', $user)
                ->with('success', "User created successfully. An activation link has been sent to {$user->email} to set their password.");

        } catch (\Throwable $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Failed to create user: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
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
            $role = $request->filled('role_id') ? Role::find($request->role_id) : null;
            $nameParts = preg_split('/\s+/', trim($request->name), 2);

            $userData = [
                'name' => $request->name,
                'first_name' => $nameParts[0] ?? $request->name,
                'last_name' => $nameParts[1] ?? '',
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'is_active' => $request->input('status', 'active') === 'active',
            ];

            if ($role) {
                $userData['role'] = $role->guard_role;
                $userData['role_id'] = $role->id;
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

    /**
     * Send or resend password setup / reset link to user.
     */
    public function sendResetLink(User $user)
    {
        return $this->resetPassword($user, request());
    }

    /**
     * Trigger password reset link to user's email.
     */
    public function resetPassword(User $user, Request $request)
    {
        $this->authorize('update', $user);

        try {
            $token = Password::broker()->createToken($user);
            Mail::to($user->email)->send(new AccountCreatedSetupPassword($user, $token));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Password setup link sent to {$user->email} successfully.",
                ]);
            }

            return back()->with('success', "Password setup & activation link sent to {$user->email} successfully.");
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to send setup link: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to send setup link: ' . $e->getMessage()]);
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
