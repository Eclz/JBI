<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $faculty = Auth::user();

        // Get all courses taught by this faculty member
        $courses = Course::where('instructor_id', $faculty->id)->get();

        // Get all assignments for these courses
        $assignments = Assignment::whereIn('course_id', $courses->pluck('id'))
            ->with(['course', 'submissions'])
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return view('faculty.assignments.index', compact('assignments', 'courses'));
    }

    public function create()
    {
        $faculty = Auth::user();
        $courses = Course::where('instructor_id', $faculty->id)->get();

        return view('faculty.assignments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|numeric|min:0',
            'submission_type' => 'required|in:file,text,both',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Verify faculty teaches this course
        $course = Course::where('id', $validated['course_id'])
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment = Assignment::create($validated);

        return redirect()->route('faculty.assignments.show', $assignment)
            ->with('success', 'Assignment created successfully');
    }

    public function show(Assignment $assignment)
    {
        // Verify faculty teaches this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $assignment->load(['course', 'submissions.student']);

        $submittedCount = $assignment->submissions()->whereNotNull('submitted_at')->count();
        $gradedCount = $assignment->submissions()->whereNotNull('score')->count();
        $averageScore = $assignment->submissions()->whereNotNull('score')->avg('score');

        return view('faculty.assignments.show', compact(
            'assignment',
            'submittedCount',
            'gradedCount',
            'averageScore'
        ));
    }

    public function edit(Assignment $assignment)
    {
        // Verify faculty teaches this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $faculty = Auth::user();
        $courses = Course::where('instructor_id', $faculty->id)->get();

        return view('faculty.assignments.edit', compact('assignment', 'courses'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        // Verify faculty teaches this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|numeric|min:0',
            'submission_type' => 'required|in:file,text,both',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('assignments', 'public');
        }

        $assignment->update($validated);

        return redirect()->route('faculty.assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully');
    }

    public function destroy(Assignment $assignment)
    {
        // Verify faculty teaches this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();

        return redirect()->route('faculty.assignments.index')
            ->with('success', 'Assignment deleted successfully');
    }

    public function submissions(Assignment $assignment)
    {
        // Verify faculty teaches this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $submissions = $assignment->submissions()
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        return view('faculty.assignments.submissions', compact('assignment', 'submissions'));
    }

    public function gradeSubmission(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        // Verify faculty teaches this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $validated['graded_at'] = now();
        $validated['graded_by'] = Auth::id();

        $submission->update($validated);

        return back()->with('success', 'Submission graded successfully');
    }

    public function courseAssignments(Course $course)
    {
        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $assignments = $course->assignments()
            ->with('submissions')
            ->orderBy('due_date', 'desc')
            ->get();

        return view('faculty.courses.assignments', compact('course', 'assignments'));
    }
}
