<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->with(['course', 'course.semester'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return view('faculty.exams.index', compact('exams'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->with('semester')
            ->orderBy('name')
            ->get();

        return view('faculty.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_type' => 'required|in:midterm,final,quiz,assignment',
            'exam_mode' => 'required|in:online,offline,hybrid',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'instructions' => 'nullable|string',
            'allow_online_editor' => 'boolean',
            'require_payment' => 'nullable|boolean',
            'payment_amount' => 'nullable|numeric|min:0',
            'is_published' => 'boolean',
            'exam_paper' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'answer_booklet' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Verify faculty teaches this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $data = $request->except(['exam_paper', 'answer_booklet', 'exam_date']);

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->exam_date . ' ' . $request->start_time);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->exam_date . ' ' . $request->end_time);

        if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
            return back()
                ->withErrors(['end_time' => 'End time must be after start time.'])
                ->withInput();
        }

        $data['start_time'] = $startDateTime;
        $data['end_time'] = $endDateTime;
        $data['is_published'] = $request->boolean('is_published');
        $data['required_payment'] = $request->boolean('require_payment')
            ? (float) $request->input('payment_amount', 0)
            : 0;

        // Upload question paper
        if ($request->hasFile('exam_paper')) {
            $data['exam_paper_url'] = $request->file('exam_paper')
                ->store('exams/question-papers', 'public');
        }

        // Upload answer booklet
        if ($request->hasFile('answer_booklet')) {
            $data['answer_booklet_url'] = $request->file('answer_booklet')
                ->store('exams/answer-booklets', 'public');
        }

        Exam::create($data);

        return redirect()->route('faculty.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        $this->authorizeExam($exam);

        $exam->load(['course', 'course.semester', 'attempts.user']);

        $statistics = [
            'total_attempts' => $exam->attempts()->count(),
            'completed' => $exam->attempts()->where('status', 'graded')->count(),
            'in_progress' => $exam->attempts()->where('status', 'in_progress')->count(),
            'average_score' => $exam->attempts()->where('status', 'graded')->avg('marks_obtained'),
            'highest_score' => $exam->attempts()->where('status', 'graded')->max('marks_obtained'),
            'lowest_score' => $exam->attempts()->where('status', 'graded')->min('marks_obtained'),
        ];

        return view('faculty.exams.show', compact('exam', 'statistics'));
    }

    public function edit(Exam $exam)
    {
        $this->authorizeExam($exam);

        $courses = Course::where('instructor_id', Auth::id())
            ->with('semester')
            ->orderBy('name')
            ->get();

        return view('faculty.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorizeExam($exam);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_type' => 'required|in:midterm,final,quiz,assignment',
            'exam_mode' => 'required|in:online,offline,hybrid',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'instructions' => 'nullable|string',
            'allow_online_editor' => 'boolean',
            'required_payment' => 'nullable|numeric|min:0',
            'exam_paper' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'answer_booklet' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->except(['exam_paper', 'answer_booklet']);

        // Upload new question paper
        if ($request->hasFile('exam_paper')) {
            if ($exam->exam_paper_url) {
                Storage::disk('public')->delete($exam->exam_paper_url);
            }
            $data['exam_paper_url'] = $request->file('exam_paper')
                ->store('exams/question-papers', 'public');
        }

        // Upload new answer booklet
        if ($request->hasFile('answer_booklet')) {
            if ($exam->answer_booklet_url) {
                Storage::disk('public')->delete($exam->answer_booklet_url);
            }
            $data['answer_booklet_url'] = $request->file('answer_booklet')
                ->store('exams/answer-booklets', 'public');
        }

        $exam->update($data);

        return redirect()->route('faculty.exams.show', $exam)
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorizeExam($exam);

        // Delete files
        if ($exam->exam_paper_url) {
            Storage::disk('public')->delete($exam->exam_paper_url);
        }
        if ($exam->answer_booklet_url) {
            Storage::disk('public')->delete($exam->answer_booklet_url);
        }

        $exam->delete();

        return redirect()->route('faculty.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function attempts(Exam $exam)
    {
        $this->authorizeExam($exam);

        $attempts = $exam->attempts()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('faculty.exams.attempts', compact('exam', 'attempts'));
    }

    public function submission(Exam $exam, $attemptId)
    {
        $this->authorizeExam($exam);

        $attempt = $exam->attempts()
            ->with('user')
            ->findOrFail($attemptId);

        return view('faculty.exams.submission', compact('exam', 'attempt'));
    }

    public function gradeAttempt(Request $request, Exam $exam, $attemptId)
    {
        $this->authorizeExam($exam);

        $attempt = $exam->attempts()->findOrFail($attemptId);

        $request->validate([
            'marks_obtained' => 'required|numeric|min:0|max:' . $exam->total_marks,
            'feedback' => 'nullable|string',
        ]);

        $attempt->update([
            'marks_obtained' => $request->marks_obtained,
            'feedback' => $request->feedback,
            'graded_at' => now(),
            'graded_by' => Auth::id(),
            'status' => 'graded',
        ]);

        return back()->with('success', 'Exam graded successfully.');
    }

    private function authorizeExam(Exam $exam)
    {
        if ($exam->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
