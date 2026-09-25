<?php

namespace App\Http\Controllers;

use App\Models\HrOnboarding;
use App\Models\HrOnboardingTask;
use App\Models\User;
use Illuminate\Http\Request;

class HrOnboardingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'template_name' => 'required|string',
            'due_date' => 'required|date',
        ]);

        // Check if an active onboarding already exists
        $existing = HrOnboarding::where('user_id', $request->user_id)->whereNotIn('status', ['Completed'])->first();
        if ($existing) {
            return back()->with('error', 'An active onboarding process already exists for this employee.');
        }

        $onboarding = HrOnboarding::create([
            'user_id' => $request->user_id,
            'status' => 'In Progress',
            'hr_officer_id' => auth()->id(),
            'due_date' => $request->due_date,
            'template_name' => $request->template_name,
        ]);

        // Generate default tasks based on template
        $defaultTasks = [
            'Employment documents submitted',
            'Contract signed',
            'Identification documents verified',
            'Payroll information collected',
            'Bank details submitted',
            'Staff account created',
            'Email account created',
            'ID card issued',
            'Workspace assigned',
            'Department introduction completed'
        ];

        foreach ($defaultTasks as $taskName) {
            $onboarding->tasks()->create([
                'task_name' => $taskName,
                'status' => 'Pending',
                'assigned_to' => auth()->id(),
            ]);
        }

        return redirect()->route('human-resources.onboarding.show', $onboarding->id)
                         ->with('success', 'Onboarding process started successfully.');
    }

    public function show(HrOnboarding $onboarding)
    {
        $onboarding->load(['user.hrProfile', 'hrOfficer', 'tasks.assignee']);
        return view('human-resources.onboarding.show', compact('onboarding'));
    }

    public function updateTask(Request $request, HrOnboardingTask $task)
    {
        $request->validate([
            'status' => 'required|in:Pending,Completed',
        ]);

        $task->update([
            'status' => $request->status,
            'completed_date' => $request->status === 'Completed' ? now() : null,
        ]);

        // Update total progress
        $onboarding = $task->onboarding;
        $total = $onboarding->tasks()->count();
        $completed = $onboarding->tasks()->where('status', 'Completed')->count();
        
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        $onboarding->update([
            'progress_percentage' => $percentage,
            'status' => $percentage === 100 ? 'Completed' : 'In Progress',
            'completed_date' => $percentage === 100 ? now() : null,
        ]);

        return back()->with('success', 'Task status updated.');
    }
}
