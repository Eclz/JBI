<?php

namespace App\Http\Controllers;

use App\Models\HrOnboarding;
use App\Models\HrOnboardingTask;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\StaffOnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HrOnboardingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'template_name' => ['required', Rule::in(StaffOnboardingService::TEMPLATES)],
            'due_date' => 'required|date|after_or_equal:today',
        ]);
        $onboarding = StaffOnboardingService::ensure(User::findOrFail($data['user_id']), auth()->id(), $data);
        return redirect()->route('human-resources.onboarding.show', $onboarding)
            ->with('success', 'Employee record and onboarding checklist are ready. Assign owners and record evidence as you complete each task.');
    }

    public function show(HrOnboarding $onboarding)
    {
        $onboarding->load(['user.hrProfile', 'hrOfficer', 'tasks.assignee']);
        $assignees = User::where('is_active', true)->whereNotIn('role', ['student', 'applicant', 'parent', ''])->orderBy('name')->get();
        return view('human-resources.onboarding.show', compact('onboarding', 'assignees'));
    }

    public function updateTask(Request $request, HrOnboardingTask $task)
    {
        $data = $request->validate([
            'status' => 'required|in:Pending,Completed',
            'assigned_to' => ['nullable', Rule::exists('users', 'id')->where('is_active', true)->whereNotIn('role', ['student', 'parent', 'applicant', ''])],
            'due_date' => 'nullable|date',
            'comments' => 'nullable|string|max:4000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);
        DB::transaction(function () use ($request, $data, $task) {
            // Serialize changes to all tasks in the same plan, including progress calculation.
            $onboarding = HrOnboarding::whereKey($task->hr_onboarding_id)->lockForUpdate()->firstOrFail();
            $task->refresh();
            $old = $task->getAttributes();
            $comments = trim($data['comments'] ?? $task->comments ?? '');
            if ($data['status'] === 'Completed') {
                if ($task->task_name === 'Account password set' && $onboarding->user->must_change_password) {
                    throw ValidationException::withMessages(['status' => 'The employee must set their account password before this task can be completed.']);
                }
                if ($task->task_name === 'Workspace assigned' && !$onboarding->user->hrProfile?->workspace) {
                    throw ValidationException::withMessages(['status' => 'Record the employee workspace in their HR profile first.']);
                }
                if (!$comments && !$task->attachment_path && !$request->hasFile('attachment')) {
                    throw ValidationException::withMessages(['comments' => 'Add a completion note or supporting attachment before completing this task.']);
                }
            }
            unset($data['attachment']);
            $data['comments'] = $comments;
            if ($request->hasFile('attachment')) {
                $data['attachment_path'] = $request->file('attachment')->store('hr-onboarding/' . $onboarding->id, 'local');
            }
            $data['completed_date'] = $data['status'] === 'Completed' ? ($task->completed_date ?? now()) : null;
            $task->update($data);
            $total = $onboarding->tasks()->count();
            $completed = $onboarding->tasks()->where('status', 'Completed')->count();
            $percentage = $total ? (int) round(($completed / $total) * 100) : 0;
            $onboarding->update([
                'progress_percentage' => $percentage,
                'status' => $percentage === 100 ? 'Completed' : 'In Progress',
                'completed_date' => $percentage === 100 ? now() : null,
            ]);
            if ($task->wasChanged('assigned_to') && $task->assigned_to && $task->assigned_to !== auth()->id()) {
                \App\Models\Notification::create([
                    'user_id' => $task->assigned_to, 'type' => 'info', 'priority' => 'high',
                    'title' => 'Staff onboarding task assigned',
                    'message' => $task->task_name . ' for ' . $onboarding->user->name,
                    'action_url' => route('human-resources.onboarding.show', $onboarding),
                ]);
            }
            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'onboarding_task_updated',
                'model_type' => HrOnboardingTask::class, 'model_id' => $task->id,
                'old_values' => $old, 'new_values' => $task->getAttributes(),
            ]);
        });
        return back()->with('success', 'Task saved and onboarding progress updated.');
    }

    public function attachment(HrOnboardingTask $task)
    {
        abort_unless($task->attachment_path && Storage::disk('local')->exists($task->attachment_path), 404);
        return Storage::disk('local')->download($task->attachment_path);
    }
}
