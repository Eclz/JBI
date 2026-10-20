<?php

namespace App\Services;

use App\Models\HrEmployee;
use App\Models\HrOnboarding;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffOnboardingService
{
    public const TEMPLATES = ['Standard', 'Management', 'Faculty'];

    public static function ensure(User $user, ?int $officerId, array $details = []): HrOnboarding
    {
        if (in_array($user->role, ['student', 'applicant', 'parent', null, ''], true)) {
            throw ValidationException::withMessages(['user_id' => 'Choose a staff account.']);
        }

        return DB::transaction(function () use ($user, $officerId, $details) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $faculty = $user->facultyProfile;
            $departmentId = $details['department_id'] ?? $faculty?->department_id;
            $department = $departmentId ? \App\Models\Department::find($departmentId) : null;
            HrEmployee::firstOrCreate(['user_id' => $user->id], [
                'employee_number' => sprintf('EMP-%s-%06d', now()->format('Y'), $user->id),
                'job_title' => $details['job_title'] ?? $faculty?->position ?? $user->roleCatalog?->name ?? ucfirst($user->role),
                'department_id' => $departmentId,
                'department' => $department?->name,
                'employment_type' => $details['employment_type'] ?? $faculty?->employment_type ?? 'full_time',
                'status' => 'Active',
            ]);
            $existing = HrOnboarding::where('user_id', $user->id)->latest('id')->first();
            if ($existing) {
                if (! $existing->hr_officer_id && $officerId) {
                    $existing->update(['hr_officer_id' => $officerId]);
                    $existing->tasks()->whereNull('assigned_to')->update(['assigned_to' => $officerId]);
                }

                return $existing;
            }
            $template = $details['template_name'] ?? ($user->role === 'faculty' ? 'Faculty' : 'Standard');
            $dueDate = $details['due_date'] ?? today()->addDays(14)->toDateString();
            $onboarding = HrOnboarding::create([
                'user_id' => $user->id, 'hr_officer_id' => $officerId,
                'template_name' => $template, 'due_date' => $dueDate, 'status' => 'In Progress',
                'progress_percentage' => 0,
            ]);
            $tasks = [
                'Employment documents verified', 'Employment contract signed',
                'Identification documents verified', 'Payroll information collected',
                'Bank details submitted', 'Account password set', 'ID card issued',
                'Workspace assigned', 'Department introduction completed',
            ];
            if ($template === 'Faculty') {
                $tasks[] = 'Teaching courses and timetable assigned';
                $tasks[] = 'Learning management system orientation completed';
            } elseif ($template === 'Management') {
                $tasks[] = 'Team and reporting responsibilities confirmed';
                $tasks[] = 'Approval permissions reviewed';
            }
            foreach ($tasks as $name) {
                $onboarding->tasks()->create([
                    'task_name' => $name, 'assigned_to' => $officerId,
                    'due_date' => $dueDate, 'status' => 'Pending',
                ]);
            }

            return $onboarding;
        });
    }
}
