<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['course', 'course.instructor'])
            ->when(Auth::user()->role === 'faculty', function ($query) {
                $query->whereHas('course', function ($q) {
                    $q->where('instructor_id', Auth::id());
                });
            })
            ->when(Auth::user()->role === 'student', function ($query) {
                $query->whereHas('course.enrollments', function ($q) {
                    $q->where('student_id', Auth::id());
                });
            })
            ->when(request('course'), function ($query, $course) {
                $query->where('course_id', $course);
            })
            ->when(request('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        $courses = Course::when(Auth::user()->role === 'faculty', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->when(Auth::user()->role === 'student', function ($query) {
                $query->whereHas('enrollments', function ($q) {
                    $q->where('student_id', Auth::id());
                });
            })
            ->get();

        return view('assignments.index', compact('assignments', 'courses'));
    }

    public function show(Assignment $assignment)
    {
        $assignment->load([
            'course',
            'submissions.student',
            'submissions' => function ($query) {
                $query->orderBy('submitted_at', 'desc');
            }
        ]);

        $userSubmission = null;
        if (Auth::user()->role === 'student') {
            $userSubmission = $assignment->submissions()
                ->where('student_id', Auth::id())
                ->first();
        }

        $submissionStats = [
            'total_students' => $assignment->course->enrollments()->count(),
            'submitted' => $assignment->submissions()->count(),
            'graded' => $assignment->submissions()->whereNotNull('grade')->count(),
            'average_grade' => $assignment->submissions()->avg('grade'),
        ];

        return view('assignments.show', compact('assignment', 'userSubmission', 'submissionStats'));
    }

    public function create()
    {
        $this->authorize('create', Assignment::class);
        
        $courses = Course::where('instructor_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        return view('assignments.create', compact('courses'));
    }

    public function store(StoreAssignmentRequest $request)
    {
        $this->authorize('create', Assignment::class);
        
        $assignment = Assignment::create($request->validated());
        
        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment created successfully.');
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        
        $courses = Course::where('instructor_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        return view('assignments.edit', compact('assignment', 'courses'));
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        
        $assignment->update($request->validated());
        
        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);
        
        if ($assignment->submissions()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete assignment with submissions.']);
        }
        
        $assignment->delete();
        
        return redirect()->route('assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
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

    public function grade(Request $request, AssignmentSubmission $submission)
    {
        $this->authorize('update', $submission->assignment);
        
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'graded_at' => now(),
            'graded_by' => Auth::id(),
            'status' => 'graded',
        ]);

        return back()->with('success', 'Assignment graded successfully.');
    }
}
