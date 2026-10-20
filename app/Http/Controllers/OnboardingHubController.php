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
}
