<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::whereHas('course.enrollments', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('status', 'enrolled');
            })
            ->with(['course', 'submissions' => function ($query) {
                $query->where('user_id', Auth::id());
            }])
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        return view('student.assignments.index', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        $enrollment = $assignment->course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $submission = $assignment->submissions()
            ->where('user_id', Auth::id())
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $assignment->course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        if (!$request->submission_text && !$request->hasFile('file')) {
            return back()->withErrors(['error' => 'Please provide a submission text or upload a file.']);
        }

        $existingSubmission = AssignmentSubmission::where([
            'assignment_id' => $assignment->id,
            'user_id' => Auth::id(),
        ])->first();

        if ($existingSubmission && $existingSubmission->status !== 'draft') {
            return back()->withErrors(['error' => 'You have already submitted this assignment.']);
        }

        if (now() > $assignment->due_date) {
            return back()->withErrors(['error' => 'Assignment submission deadline has passed.']);
        }

        $files = $existingSubmission ? ($existingSubmission->submitted_files ?? []) : [];
        if ($request->hasFile('file')) {
            // Delete old file if updating draft
            if (count($files) > 0) {
                Storage::disk('public')->delete($files[0]);
                $files = [];
            }
            $files[] = $request->file('file')->store('submissions', 'public');
        }

        $status = $request->action === 'draft' ? 'draft' : 'submitted';

        if ($existingSubmission) {
            $existingSubmission->update([
                'submission_text' => $request->submission_text,
                'submitted_files' => $files,
                'submitted_at' => now(),
                'status' => $status,
            ]);
        } else {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'submission_text' => $request->submission_text,
                'submitted_files' => $files,
                'submitted_at' => now(),
                'status' => $status,
            ]);
        }

        $message = $status === 'draft' ? 'Draft saved successfully.' : 'Assignment submitted successfully.';
        return back()->with('success', $message);
    }

    public function updateSubmission(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        return $this->submit($request, $assignment);
    }

    public function download(Assignment $assignment)
    {
        $submission = $assignment->submissions()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$submission->attachment_path) {
            return back()->withErrors(['error' => 'No attachment found.']);
        }

        return Storage::disk('public')->download($submission->attachment_path);
    }
}
