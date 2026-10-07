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

        return view('human-resources.recruiting.show', compact('vacancy', 'kanban', 'roles', 'departments'));
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
            'workspace' => ['required', 'string', 'max:150'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $user = DB::transaction(function () use ($applicant, $data) {
            $applicant = HrApplicant::query()->lockForUpdate()->findOrFail($applicant->id);
            abort_if($applicant->hired_user_id, 409, 'This applicant already has an account.');
            abort_unless($applicant->status === 'Offer', 422, 'Move the applicant to Offer before approving a hire.');

            $role = Role::where('is_active', true)->findOrFail($data['role_id']);
            abort_if(in_array($role->guard_role, ['student', 'parent', 'applicant'], true), 422, 'Select a staff role.');
            abort_if(
                User::whereRaw('LOWER(email) = ?', [Str::lower($applicant->email)])->exists(),
                422,
                'A user account already exists for this email address.'
            );

            $department = Department::where('is_active', true)->findOrFail($data['department_id']);
            $name = trim($applicant->first_name . ' ' . $applicant->last_name);
            $user = User::create([
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
            HrEmployee::create([
                'user_id' => $user->id,
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
                FacultyProfile::create([
                    'user_id' => $user->id,
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

            $onboarding = HrOnboarding::create([
                'user_id' => $user->id,
                'status' => 'In Progress',
                'hr_officer_id' => auth()->id(),
                'due_date' => $data['due_date'],
                'template_name' => 'New Employee',
            ]);

            foreach ([
                'Employment documents verified',
                'Employment contract signed',
                'Identification documents verified',
                'Payroll information collected',
                'Bank details submitted',
                'Account password set',
                'ID card issued',
                'Workspace assigned',
                'Department introduction completed',
            ] as $taskName) {
                $onboarding->tasks()->create([
                    'task_name' => $taskName,
                    'status' => 'Pending',
                    'assigned_to' => auth()->id(),
                    'due_date' => $data['due_date'],
                ]);
            }

            $applicant->update([
                'status' => 'Hired',
                'hired_user_id' => $user->id,
                'hired_at' => now(),
                'hired_by' => auth()->id(),
            ]);

            return $user;
        });

        try {
            $token = Password::broker()->createToken($user);
            Mail::to($user->email)->send(new AccountCreatedSetupPassword($user, $token));
        } catch (\Throwable $exception) {
            Log::error('New employee setup email could not be sent.', [
                'user_id' => $user->id,
                'applicant_id' => $applicant->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('warning', 'The hire was approved and onboarding was created, but the password setup email could not be sent. Ask an administrator to resend the setup link.');
        }

        return back()->with('success', 'Hire approved. The staff account, employee profile, ID/workspace checklist, and onboarding plan were created. A password setup link was sent to the applicant.');
    }
}
