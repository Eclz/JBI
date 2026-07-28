<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        $enrolledCourseIds = $student->courseEnrollments()
            ->where('status', 'enrolled')
            ->pluck('course_id');

        $upcomingExams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '>', Carbon::now())
            ->with('course')
            ->orderBy('start_time', 'asc')
            ->get();

        $activeExams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '<=', Carbon::now())
            ->where('end_time', '>=', Carbon::now())
            ->with([
                'course',
                'attempts' => function ($query) use ($student) {
                    $query->where('user_id', $student->id);
                },
            ])
            ->get();

        $completedAttempts = ExamAttempt::where('user_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->with('exam.course')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        $expiredAttempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'in_progress')
            ->with('exam.course')
            ->get()
            ->filter(function ($attempt) {
                if (!$attempt->started_at || !$attempt->exam) {
                    return false;
                }
                $byDuration = $attempt->started_at->copy()->addMinutes($attempt->exam->duration_minutes);
                $deadline = $attempt->exam->end_time;
                $effectiveEnd = $deadline && $deadline->lt($byDuration) ? $deadline : $byDuration;
                return $effectiveEnd->lte(\Carbon\Carbon::now());
            });

        $timeLeftByExam = [];
        $remainingSubmissionsByExam = [];
        $now = Carbon::now();
        $activeExams = $activeExams->filter(function ($exam) use ($now, &$timeLeftByExam, &$remainingSubmissionsByExam) {
            $attempt = $exam->attempts->first();
            if (!$attempt) {
                return true;
            }

            if (in_array($attempt->status, ['submitted', 'graded'], true)) {
                return false;
            }

            if (!$attempt->started_at) {
                return true;
            }

            $byDuration = $attempt->started_at->copy()->addMinutes($exam->duration_minutes);
            $deadline = $exam->end_time;
            $effectiveEnd = $deadline && $deadline->lt($byDuration) ? $deadline : $byDuration;

            if ($effectiveEnd->lte($now)) {
                return false;
            }

            $timeLeftByExam[$exam->id] = $effectiveEnd->diffInSeconds($now);
            $currentCount = $attempt->submission_count ?? 0;
            if ($currentCount === 0 && $attempt->submitted_at) {
                $currentCount = 1;
            }
            $remaining = 2 - $currentCount;
            if ($remaining > 0) {
                $remainingSubmissionsByExam[$exam->id] = $remaining;
            }

            return true;
        })->values();

        return view('student.exams.index', compact(
            'upcomingExams',
            'activeExams',
            'completedAttempts',
            'timeLeftByExam',
            'remainingSubmissionsByExam',
            'expiredAttempts'
        ));
    }

    public function show(Exam $exam)
    {
        $student = Auth::user();

        // Check if student is enrolled
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $exam->course_id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.exams.index')
                ->with('error', 'You must be enrolled in this course to view this exam.');
        }

        $attempt = $exam->studentAttempt($student->id);
        $attempts = $exam->attempts()
            ->where('user_id', $student->id)
            ->orderBy('started_at', 'desc')
            ->get();
        $attemptsCount = $attempt ? ($attempt->submission_count ?? 0) : 0;
        if ($attemptsCount === 0 && $attempt && $attempt->submitted_at) {
            $attemptsCount = 1;
        }
        $maxAttempts = 2;
        $canAttempt = $attemptsCount < $maxAttempts || ($attempt && $attempt->status === 'not_started');
        $hasPaid = $exam->required_payment <= 0 || ($attempt && $attempt->payment_verified);

        $now = Carbon::now();
        if ($exam->start_time && $exam->start_time->gt($now)) {
            $status = 'upcoming';
        } elseif ($exam->end_time && $exam->end_time->lt($now)) {
            $status = 'completed';
        } else {
            $status = 'active';
        }

        $canResubmit = false;
        if ($attempt && $attempt->started_at) {
            $byDuration = $attempt->started_at->copy()->addMinutes($exam->duration_minutes);
            $deadline = $exam->end_time;
            $effectiveEnd = $deadline && $deadline->lt($byDuration) ? $deadline : $byDuration;
            $currentCount = $attempt->submission_count ?? 0;
            if ($currentCount === 0 && $attempt->submitted_at) {
                $currentCount = 1;
            }
            $canResubmit = $effectiveEnd->gt($now) && $currentCount < $maxAttempts;
        }

        return view('student.exams.show', compact(
            'exam',
            'attempt',
            'attempts',
            'attemptsCount',
            'maxAttempts',
            'canAttempt',
            'hasPaid',
            'status',
            'canResubmit'
        ));
    }

    public function startExam(Exam $exam)
    {
        $student = Auth::user();

        // Validations
        if (!$exam->isActive()) {
            return back()->with('error', 'This exam is not currently available.');
        }

        $attempt = ExamAttempt::firstOrCreate(
            [
                'exam_id' => $exam->id,
                'user_id' => $student->id,
            ],
            [
                'status' => 'not_started',
                'payment_verified' => false,
            ]
        );

        // Check payment requirement
        if ($exam->required_payment > 0 && !$attempt->payment_verified) {
            $currency = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
            return back()->with('error', 'You must complete the payment of ' . $currency . ' ' . number_format($exam->required_payment, 2) . ' before attempting this exam.');
        }

        if ($attempt->status !== 'not_started') {
            return redirect()->route('student.exams.take', $exam)
                ->with('info', 'Resuming your exam attempt...');
        }

        // Start the exam
        $attempt->update([
            'started_at' => Carbon::now(),
            'time_remaining_seconds' => $exam->duration_minutes * 60,
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.exams.take', $exam);
    }

    public function take(Exam $exam)
    {
        $student = Auth::user();

        $attempt = $exam->studentAttempt($student->id);

        if (!$attempt || $attempt->status === 'not_started') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Please start the exam first.');
        }

        if ($attempt->isSubmitted()) {
            $now = Carbon::now();
            $byDuration = $attempt->started_at->copy()->addMinutes($exam->duration_minutes);
            $deadline = $exam->end_time;
            $effectiveEnd = $deadline && $deadline->lt($byDuration) ? $deadline : $byDuration;

            if ($effectiveEnd->lt($now)) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('info', 'The exam time has ended.');
            }

            $currentCount = $attempt->submission_count ?? 0;
            if ($currentCount === 0 && $attempt->submitted_at) {
                $currentCount = 1;
            }
            if ($currentCount >= 2) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('info', 'You have reached the maximum number of submissions.');
            }
        }

        return view('student.exams.take', compact('exam', 'attempt'));
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $student = Auth::user();

        $attempt = $exam->studentAttempt($student->id);

        if (!$attempt || !in_array($attempt->status, ['in_progress', 'submitted'], true)) {
            return back()->with('error', 'Invalid exam attempt.');
        }

        $now = Carbon::now();
        $byDuration = $attempt->started_at->copy()->addMinutes($exam->duration_minutes);
        $deadline = $exam->end_time;
        $effectiveEnd = $deadline && $deadline->lt($byDuration) ? $deadline : $byDuration;

        if ($effectiveEnd->lt($now)) {
            return back()->with('error', 'The exam time has ended.');
        }

        $currentCount = $attempt->submission_count ?? 0;
        if ($currentCount === 0 && $attempt->submitted_at) {
            $currentCount = 1;
        }
        if ($currentCount >= 2) {
            return back()->with('error', 'You have reached the maximum number of submissions.');
        }

        $validated = $request->validate([
            'answers' => 'nullable|string',
            'submission_file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $submissionFileUrl = null;
        if ($request->hasFile('submission_file')) {
            $submissionFileUrl = $request->file('submission_file')->store('exam-submissions', 'public');
        }

        $attempt->update([
            'answers' => $validated['answers'] ?? $attempt->answers,
            'submission_file_url' => $submissionFileUrl ?? $attempt->submission_file_url,
            'submitted_at' => Carbon::now(),
            'status' => 'submitted',
            'submission_count' => $currentCount + 1,
        ]);

        return redirect()->route('student.exams.show', $exam)
            ->with('success', 'Exam submitted successfully! Your instructor will grade it soon.');
    }

    public function autosave(Request $request, Exam $exam)
    {
        $student = Auth::user();

        $attempt = $exam->studentAttempt($student->id);

        if (!$attempt || !in_array($attempt->status, ['in_progress', 'submitted'], true)) {
            return response()->json(['message' => 'Invalid exam attempt.'], 422);
        }

        $validated = $request->validate([
            'answers' => 'nullable|string',
            'time_remaining_seconds' => 'nullable|integer|min:0',
        ]);

        $attempt->update([
            'answers' => $validated['answers'] ?? $attempt->answers,
            'time_remaining_seconds' => $validated['time_remaining_seconds'] ?? $attempt->time_remaining_seconds,
        ]);

        return response()->json(['saved' => true]);
    }

    public function downloadPaper(Exam $exam)
    {
        if (!$exam->exam_paper_url) {
            return back()->with('error', 'Exam paper is not available.');
        }

        return Storage::disk('public')->download($exam->exam_paper_url);
    }

    public function downloadAnswerBooklet(Exam $exam)
    {
        if (!$exam->answer_booklet_url) {
            return back()->with('error', 'Answer booklet is not available.');
        }

        return Storage::disk('public')->download($exam->answer_booklet_url);
    }
}
