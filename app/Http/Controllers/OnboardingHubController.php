<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\HrOnboarding;
use App\Models\Notification;
use App\Models\Semester;
use App\Models\User;

class OnboardingHubController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $admissions = $user->hasPermission('applications', 'view');
        $hr = $user->hasPermission('hr_onboarding', 'view');
        abort_unless($admissions || $hr, 403);
        $applications = $admissions ? Application::where('status', 'pending')->with('programRecord')->oldest()->limit(50)->get() : collect();
        $staff = $hr ? User::where('is_active', true)->whereNotIn('role', ['student', 'parent', 'applicant', ''])
            ->with('hrProfile')->latest()->limit(50)->get() : collect();
        $onboardings = $hr ? HrOnboarding::where('status', '!=', 'Completed')->with('user')->orderBy('due_date')->get() : collect();
        $semesters = Semester::where('is_current', true)->get();
        $emailCounts = Notification::where('user_id', $user->id)->where('created_at', '>=', now()->subDays(7))->get()
            ->countBy(fn ($notification) => $notification->email_sent ? 'sent' : ($notification->data['email_status'] ?? 'not sent'));

        return view('onboarding-hub', compact('applications', 'staff', 'onboardings', 'semesters', 'admissions', 'hr', 'emailCounts'));
    }

    public function prepareStaff(User $user)
    {
        abort_unless(auth()->user()->hasPermission('hr_onboarding', 'create'), 403);
        $onboarding = \App\Services\StaffOnboardingService::ensure($user, auth()->id());

        return redirect()->route('human-resources.onboarding.show', $onboarding)->with('success', 'Employee record and onboarding checklist are ready.');
    }

    public function staffStatus()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isFullyOnboarded()) {
            if ($user->hasRole('registrar') || $user->hasRole('academic_registrar')) {
                return redirect()->route('admin.registrar.dashboard');
            }
            if ($user->isFinanceOfficer()) {
                return redirect()->route('admin.finance.dashboard');
            }
            if ($user->isHrStaff()) {
                return redirect()->route('human-resources.index');
            }
            if ($user->hasRole('dean')) {
                return redirect()->route('faculty.dean.quality.index');
            }
            if ($user->isHeadOfDepartment()) {
                return redirect()->route('faculty.hod.department.index');
            }
            return redirect()->route('dashboard');
        }

        $onboarding = $user->hrOnboardings()->with('tasks', 'hrOfficer')->latest('id')->first();
        $tasks = $onboarding ? $onboarding->tasks : collect();

        return view('onboarding.staff-status', compact('user', 'onboarding', 'tasks'));
    }
}
