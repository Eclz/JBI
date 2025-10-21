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
                $query->where('student_id', Auth::id())
                      ->where('status', 'enrolled');
            })
            ->with(['course', 'submissions' => function ($query) {
                $query->where('student_id', Auth::id());
            }])
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        return view('student.assignments.index', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        // Check if student is enrolled in the course
        $enrollment = $assignment->course->enrollments()
            ->where('student_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $submission = $assignment->submissions()
            ->where('student_id', Auth::id())
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        // Check if student is enrolled
        $assignment->course->enrollments()
            ->where('student_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $existingSubmission = AssignmentSubmission::where([
            'assignment_id' => $assignment->id,
            'student_id' => Auth::id(),
        ])->first();

        if ($existingSubmission) {
            return back()->withErrors(['error' => 'You have already submitted this assignment.']);
        }

        if (now() > $assignment->due_date) {
            return back()->withErrors(['error' => 'Assignment submission deadline has passed.']);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('submissions', 'public');
        }

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => Auth::id(),
            'content' => $request->content,
            'attachment_path' => $attachmentPath,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        return back()->with('success', 'Assignment submitted successfully.');
    }

    public function download(Assignment $assignment)
    {
        $submission = $assignment->submissions()
            ->where('student_id', Auth::id())
            ->firstOrFail();

        if (!$submission->attachment_path) {
            return back()->withErrors(['error' => 'No attachment found.']);
        }

        return Storage::disk('public')->download($submission->attachment_path);
    }
}
