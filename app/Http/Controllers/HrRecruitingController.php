<?php

namespace App\Http\Controllers;

use App\Models\HrVacancy;
use App\Models\HrApplicant;
use App\Models\HrEmployee;
use App\Models\HrOnboarding;
use App\Models\Department;
use App\Models\FacultyProfile;
use App\Models\Role;
use App\Models\User;
use App\Mail\AccountCreatedSetupPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HrRecruitingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'position_title' => 'required|string|max:150',
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where('is_active', true)],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('is_active', true)],
            'job_description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'num_openings' => 'required|integer|min:1',
            'employment_type' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'closing_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:Draft,Open',
        ]);

        $data['hiring_manager_id'] = auth()->id();
        $data['opening_date'] = now();
        $data['published_at'] = $data['status'] === 'Open' ? now() : null;
        $data['department'] = $data['department_id']
            ? Department::findOrFail($data['department_id'])->name
            : null;

        $vacancy = HrVacancy::create($data);

        return redirect()->route('human-resources.recruiting.show', $vacancy->id)
                         ->with('success', 'Vacancy created successfully.');
    }

    public function show(HrVacancy $vacancy)
    {
        $vacancy->load(['hiringManager', 'role', 'departmentRecord', 'applicants' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        $roles = Role::where('is_active', true)
            ->whereNotIn('guard_role', ['student', 'parent', 'applicant'])
            ->orderBy('name')
            ->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        // Group applicants by status for the Kanban board
        $kanban = [
            'Applied' => [],
            'Screening' => [],
            'Shortlisted' => [],
            'Interview' => [],
            'Assessment' => [],
            'Reference Check' => [],
            'Offer' => [],
            'Hired' => [],
            'Rejected' => [],
        ];

        foreach ($vacancy->applicants as $applicant) {
            if (isset($kanban[$applicant->status])) {
                $kanban[$applicant->status][] = $applicant;
            } else {
                // Fallback for custom statuses
                $kanban['Applied'][] = $applicant;
            }
        }

        $existingAccounts = User::whereIn('email', $vacancy->applicants->pluck('email'))->get()->keyBy(fn ($user) => strtolower($user->email));
        return view('human-resources.recruiting.show', compact('vacancy', 'kanban', 'roles', 'departments', 'existingAccounts'));
    }

    public function storeFromSuccession(Request $request, \App\Models\HrSuccessionPlan $succession)
    {
        abort_unless(auth()->user()->hasPermission('hr_recruiting', 'create'), 403);
        abort_if($succession->hr_vacancy_id, 409, 'A vacancy is already linked to this succession plan.');

        $data = $request->validate([
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('is_active', true)],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where('is_active', true)],
            'job_description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'closing_date' => ['nullable', 'date', 'after_or_equal:today'],
            'employment_type' => ['required', 'string', 'max:50'],
        ]);

        $vacancy = DB::transaction(function () use ($succession, $data) {
            $succession = \App\Models\HrSuccessionPlan::query()->lockForUpdate()->findOrFail($succession->id);
            abort_if($succession->hr_vacancy_id, 409, 'A vacancy is already linked to this succession plan.');
            $department = Department::where('is_active', true)->findOrFail($data['department_id']);

            $vacancy = HrVacancy::create([
                'position_title' => $succession->position_name,
                'department' => $department->name,
                'department_id' => $department->id,
                'role_id' => $data['role_id'],
                'job_description' => $data['job_description'] ?? $succession->notes,
                'requirements' => $data['requirements'] ?? null,
                'num_openings' => 1,
                'employment_type' => $data['employment_type'],
                'hiring_manager_id' => auth()->id(),
                'source_succession_plan_id' => $succession->id,
                'opening_date' => now(),
                'published_at' => now(),
                'closing_date' => $data['closing_date'] ?? null,
                'status' => 'Open',
            ]);

            $succession->update(['hr_vacancy_id' => $vacancy->id]);

            return $vacancy;
        });

        return redirect()->route('human-resources.recruiting.show', $vacancy)
            ->with('success', 'Vacancy created and linked to the succession plan.');
    }

    public function updateApplicantStatus(Request $request, HrApplicant $applicant)
    {
        $request->validate([
            'status' => 'required|string|in:Applied,Screening,Shortlisted,Interview,Assessment,Reference Check,Offer,Rejected',
        ]);

        abort_if($applicant->hired_user_id, 409, 'This applicant has already been hired.');
        $applicant->update(['status' => $request->status]);

        return back()->with('success', 'Applicant status updated successfully.');
    }

    public function downloadDocument(HrApplicant $applicant, string $document)
    {
        abort_unless(auth()->user()->hasPermission('hr_recruiting', 'view'), 403);

        if ($document === 'cv') {
            $path = $applicant->cv_path;
            $downloadName = 'cv-' . Str::slug($applicant->full_name) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        } elseif (ctype_digit($document)) {
            $path = $applicant->documents[(int) $document] ?? null;
            $downloadName = $path ? basename($path) : null;
        } else {
            abort(404);
        }

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $downloadName);
    }

    public function approveHire(Request $request, HrApplicant $applicant)
    {
        abort_unless(auth()->user()->hasPermission('hr_recruiting', 'approve'), 403);

        $data = $request->validate([
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('is_active', true)],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where('is_active', true)],
            'existing_user_id' => 'nullable|integer|exists:users,id',
            'workspace' => ['required', 'string', 'max:150'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $user = DB::transaction(function () use ($applicant, $data) {
            $applicant = HrApplicant::query()->lockForUpdate()->findOrFail($applicant->id);
            abort_if($applicant->hired_user_id, 409, 'This applicant already has an account.');
            abort_unless($applicant->status === 'Offer', 422, 'Move the applicant to Offer before approving a hire.');

            $role = Role::where('is_active', true)->findOrFail($data['role_id']);
            abort_if(in_array($role->guard_role, ['student', 'parent', 'applicant'], true), 422, 'Select a staff role.');
            $existingUser = User::whereRaw('LOWER(email) = ?', [Str::lower($applicant->email)])->lockForUpdate()->first();
            if ($existingUser) {
                abort_unless((int) ($data['existing_user_id'] ?? 0) === $existingUser->id, 422,
                    'An account already exists. Confirm linking it instead of creating a duplicate.');
                abort_if(in_array($existingUser->role, ['student', 'parent', 'applicant', null, ''], true), 422, 'Only an existing staff account can be linked.');
                abort_if($existingUser->hrProfile?->source_applicant_id, 422, 'This employee is already linked to another approved hire.');
            } else {
                abort_if(!empty($data['existing_user_id']), 422, 'The selected account does not match this applicant email.');
            }

            $department = Department::where('is_active', true)->findOrFail($data['department_id']);
            $name = trim($applicant->first_name . ' ' . $applicant->last_name);
            $user = $existingUser ?? User::create([
                'name' => $name,
                'first_name' => $applicant->first_name,
                'last_name' => $applicant->last_name,
                'email' => $applicant->email,
                'phone' => $applicant->phone,
                'password' => Hash::make(Str::random(64)),
                'role' => $role->guard_role,
                'role_id' => $role->id,
                'is_active' => true,
                'must_change_password' => true,
            ]);

            $employeeNumber = sprintf('EMP-%s-%06d', now()->format('Y'), $user->id);
            HrEmployee::updateOrCreate(['user_id' => $user->id], [
                'source_applicant_id' => $applicant->id,
                'employee_number' => $employeeNumber,
                'job_title' => $applicant->vacancy->position_title,
                'department' => $department->name,
                'department_id' => $department->id,
                'workspace' => trim($data['workspace']),
                'employment_type' => $applicant->vacancy->employment_type,
                'status' => 'Active',
            ]);

            if ($role->guard_role === 'faculty') {
                FacultyProfile::updateOrCreate(['user_id' => $user->id], [
                    'employee_id' => $employeeNumber,
                    'department_id' => $department->id,
                    'designation' => $role->name,
                    'position' => $role->name,
                    'joining_date' => today(),
                    'hire_date' => today(),
                    'employment_type' => Str::lower(str_replace('-', '_', $applicant->vacancy->employment_type)),
                    'employment_status' => 'active',
                    'status' => 'active',
                ]);
            }

            $roleIds = $user->roles->pluck('id')->push($role->id)->unique()->all();
            $user->update(['role' => $role->guard_role, 'role_id' => $role->id]);
            $user->syncRoles($roleIds, $role->id);
            \App\Services\StaffOnboardingService::ensure($user, auth()->id(), [
                'due_date' => $data['due_date'],
            ]);

            $applicant->update([
                'status' => 'Hired',
                'hired_user_id' => $user->id,
                'hired_at' => now(),
                'hired_by' => auth()->id(),
            ]);

            return $user;
        });

        if (!$user->must_change_password) {
            return back()->with('success', 'Hire approved and linked to the existing staff account. Their current password is unchanged; onboarding is ready.');
        }
        $sent = \App\Services\AccountInvitationService::send($user);
        return back()->with($sent ? 'success' : 'warning', $sent
            ? 'Hire approved. Staff account, employee record and onboarding checklist created. Password setup email sent.'
            : 'Hire approved and onboarding created, but the setup email could not be sent. Ask an administrator to resend the invitation.');
    }
}
