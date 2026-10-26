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
        $now = Carbon::now();
        $activeExams = $activeExams->filter(function ($exam) use ($now, &$timeLeftByExam) {
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

            return true;
        })->values();

        $isTuitionCleared = $student->hasZeroTuitionBalance();
        $tuitionBalance = $student->getTuitionBalance();
        $totalBalance = $student->getTotalFeeBalance();
        $currencyCode = \App\Models\SystemSetting::getSetting('default_currency', 'USD');

        $closedExams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->where('end_time', '<', Carbon::now())
            ->where('is_published', true)
            ->with([
                'course',
                'attempts' => function ($query) use ($student) {
                    $query->where('user_id', $student->id);
                },
            ])
            ->orderBy('end_time', 'desc')
            ->take(10)
            ->get();

        return view('student.exams.index', compact(
            'upcomingExams',
            'activeExams',
            'closedExams',
            'completedAttempts',
            'timeLeftByExam',
            'expiredAttempts',
            'isTuitionCleared',
            'tuitionBalance',
            'totalBalance',
            'currencyCode'
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

        $now = Carbon::now();
        $status = $exam->effective_status;
        $mode = strtolower($exam->exam_mode ?? 'online');

        // Check if existing in-progress attempt has expired
        if ($attempt && $attempt->status === 'in_progress') {
            $byDuration = $attempt->started_at ? $attempt->started_at->copy()->addMinutes($exam->duration_minutes) : null;
            $deadline = $exam->end_time;
            $effectiveEnd = ($byDuration && $deadline) ? ($deadline->lt($byDuration) ? $deadline : $byDuration) : ($byDuration ?: $deadline);
            if ($effectiveEnd && $effectiveEnd->lte($now)) {
                $attempt->update([
                    'status' => 'submitted',
                    'submitted_at' => $effectiveEnd,
                    'submission_count' => max($attempt->submission_count ?? 0, 1),
                ]);
                $attempt->refresh();
            }
        }

        $isAttempted = $attempt && in_array($attempt->status, ['submitted', 'graded'], true);
        $isClosed = $exam->isPast();

        $maxAttempts = 1;
        $attemptsCount = $isAttempted ? 1 : 0;

        // Candidate can only start if they have not yet made/submitted an attempt and exam is active
        $canAttempt = (!$attempt || $attempt->status === 'not_started') && $exam->isActive();
        $hasPaid = $exam->required_payment <= 0 || ($attempt && $attempt->payment_verified);

        // Download permissions
        // Rule: Question papers cannot be downloaded for online exams or physical exams. For hybrid exams, only after timer starts.
        $canDownloadPaper = false;
        if ($mode === 'hybrid') {
            $canDownloadPaper = $attempt && $attempt->status === 'in_progress';
        }

        // Rule: Answer booklets cannot be downloaded for physical exams, and for online/hybrid only after attempted or closed.
        $canDownloadAnswerBooklet = false;
        if ($mode !== 'offline' && $mode !== 'physical') {
            $canDownloadAnswerBooklet = ($isAttempted || $isClosed);
        }

        $canResubmit = false;

        $isTuitionCleared = $student->hasZeroTuitionBalance();
        $tuitionBalance = $student->getTuitionBalance();
        $currencyCode = \App\Models\SystemSetting::getSetting('default_currency', 'USD');

        if (!$isTuitionCleared || in_array($mode, ['offline', 'physical'], true)) {
            $canAttempt = false;
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
            'canResubmit',
            'isTuitionCleared',
            'tuitionBalance',
            'currencyCode',
            'canDownloadPaper',
            'canDownloadAnswerBooklet'
        ));
    }

    public function startExam(Exam $exam)
    {
        $student = Auth::user();

        // Enforce limitation: physical exams cannot be started online
        $mode = strtolower($exam->exam_mode ?? 'online');
        if (in_array($mode, ['offline', 'physical'], true)) {
            $room = $exam->room_number ? " in Room {$exam->room_number}" : '';
            return back()->with('error', "This is a physical/offline examination. Candidates must sit for this exam in person{$room}. Online examination access is not applicable.");
        }

        // Enforce limitation: students who have not completed tuition do not qualify to sit for exams
        if (!$student->hasZeroTuitionBalance()) {
            $tuitionBalance = $student->getTuitionBalance();
            $currency = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
            return redirect()->route('student.exams.show', $exam)
                ->with('error', "You do not qualify to sit for examinations because you have an outstanding tuition balance of {$currency} " . number_format($tuitionBalance, 2) . ". Please clear your tuition fees in the Fee Portal to sit for exams and obtain your Examination Permit.");
        }

        // If the candidate has already completed or attempted the exam, block starting again
        $attempt = $exam->studentAttempt($student->id);
        if ($attempt && in_array($attempt->status, ['submitted', 'graded'], true)) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'You have already completed and submitted this examination. You cannot start or retake it.');
        }

        // If the student already has an in-progress attempt, check whether time has expired
        if ($attempt && $attempt->status === 'in_progress') {
            $now = Carbon::now();
            $byDuration = $attempt->started_at ? $attempt->started_at->copy()->addMinutes($exam->duration_minutes) : null;
            $deadline = $exam->end_time;
            $effectiveEnd = ($byDuration && $deadline) ? ($deadline->lt($byDuration) ? $deadline : $byDuration) : ($byDuration ?: $deadline);

            if ($effectiveEnd && $effectiveEnd->lte($now)) {
                $attempt->update([
                    'status' => 'submitted',
                    'submitted_at' => $effectiveEnd,
                    'submission_count' => max($attempt->submission_count ?? 0, 1),
                ]);
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', 'The examination time has expired. You cannot start or attempt this exam again.');
            }

            return redirect()->route('student.exams.take', $exam)
                ->with('info', 'Resuming your exam attempt...');
        }

        // Validations: exam must be active
        if (!$exam->isActive()) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This exam is not currently available.');
        }

        if (!$attempt) {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $student->id,
                'status' => 'not_started',
                'payment_verified' => false,
            ]);
        }

        // Check payment requirement
        if ($exam->required_payment > 0 && !$attempt->payment_verified) {
            $currency = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'You must complete the payment of ' . $currency . ' ' . number_format($exam->required_payment, 2) . ' before attempting this exam.');
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

        // Enforce limitation: physical exams cannot be taken online
        $mode = strtolower($exam->exam_mode ?? 'online');
        if (in_array($mode, ['offline', 'physical'], true)) {
            $room = $exam->room_number ? " in Room {$exam->room_number}" : '';
            return redirect()->route('student.exams.show', $exam)
                ->with('error', "This is a physical/offline examination. Candidates must sit for this exam in person{$room}.");
        }

        // Enforce limitation: students who have not completed tuition do not qualify to sit for exams
        if (!$student->hasZeroTuitionBalance()) {
            $tuitionBalance = $student->getTuitionBalance();
            $currency = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
            return redirect()->route('student.exams.index')
                ->with('error', "Examination access blocked: You have an outstanding tuition balance of {$currency} " . number_format($tuitionBalance, 2) . ". Please clear your tuition fees to sit for exams.");
        }

        $attempt = $exam->studentAttempt($student->id);

        if (!$attempt || $attempt->status === 'not_started') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Please start the exam first.');
        }

        // If the candidate has already submitted or completed the exam, deny access to take it again
        if (in_array($attempt->status, ['submitted', 'graded'], true)) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'You have already submitted this examination and cannot restart or retake it.');
        }

        $now = Carbon::now();
        $byDuration = $attempt->started_at ? $attempt->started_at->copy()->addMinutes($exam->duration_minutes) : null;
        $deadline = $exam->end_time;
        $effectiveEnd = ($byDuration && $deadline) ? ($deadline->lt($byDuration) ? $deadline : $byDuration) : ($byDuration ?: $deadline);

        if ($effectiveEnd && $effectiveEnd->lte($now)) {
            if ($attempt->status === 'in_progress') {
                $attempt->update([
                    'status' => 'submitted',
                    'submitted_at' => $effectiveEnd,
                    'submission_count' => max($attempt->submission_count ?? 0, 1),
                ]);
            }
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'The examination time has ended.');
        }

        return view('student.exams.take', compact('exam', 'attempt'));
    }

    public function submitExam(Request $request, Exam $exam)
    {
        $student = Auth::user();

        // Enforce limitation: physical exams cannot be submitted online
        $mode = strtolower($exam->exam_mode ?? 'online');
        if (in_array($mode, ['offline', 'physical'], true)) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Physical examinations are conducted in person and cannot be submitted online.');
        }

        // Enforce limitation: students who have not completed tuition do not qualify to submit exams
        if (!$student->hasZeroTuitionBalance()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Examination submission blocked: Tuition fees must be fully settled with a zero balance.');
        }

        $attempt = $exam->studentAttempt($student->id);

        if (!$attempt) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Invalid exam attempt.');
        }

        if (in_array($attempt->status, ['submitted', 'graded'], true)) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'You have already submitted this examination and cannot submit again.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Invalid exam attempt.');
        }

        $now = Carbon::now();
        $byDuration = $attempt->started_at ? $attempt->started_at->copy()->addMinutes($exam->duration_minutes) : null;
        $deadline = $exam->end_time;
        if ($byDuration && $deadline) {
            $effectiveEnd = $deadline->lt($byDuration) ? $deadline : $byDuration;
        } else {
            $effectiveEnd = $byDuration ?: $deadline;
        }

        // Submitting beyond the exam's time limit is not allowed (with a 30s network grace period)
        if ($effectiveEnd && $now->gt($effectiveEnd->copy()->addSeconds(30))) {
            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => $effectiveEnd,
                'submission_count' => max($attempt->submission_count ?? 0, 1),
            ]);
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'The examination time has expired. Submissions beyond the exam time limit are not accepted.');
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
            'submission_count' => max($attempt->submission_count ?? 0, 1),
        ]);

        return redirect()->route('student.exams.show', $exam)
            ->with('success', 'Exam submitted successfully! Your instructor will grade it soon.');
    }

    public function autosave(Request $request, Exam $exam)
    {
        $student = Auth::user();

        $attempt = $exam->studentAttempt($student->id);

        if (!$attempt || $attempt->status !== 'in_progress') {
            return response()->json(['message' => 'Invalid exam attempt.'], 422);
        }

        $now = Carbon::now();
        $byDuration = $attempt->started_at ? $attempt->started_at->copy()->addMinutes($exam->duration_minutes) : null;
        $deadline = $exam->end_time;
        $effectiveEnd = ($byDuration && $deadline) ? ($deadline->lt($byDuration) ? $deadline : $byDuration) : ($byDuration ?: $deadline);
        if ($effectiveEnd && $effectiveEnd->lt($now)) {
            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => $effectiveEnd,
                'submission_count' => max($attempt->submission_count ?? 0, 1),
            ]);
            return response()->json(['message' => 'The examination time has ended.'], 422);
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
        $student = Auth::user();

        $enrolled = $student->courseEnrollments()
            ->where('course_id', $exam->course_id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled) {
            abort(403, 'You must be enrolled in this course to download examination materials.');
        }

        if (!$student->hasZeroTuitionBalance()) {
            return back()->with('error', 'You must complete your tuition payment (zero balance) to access examination materials.');
        }

        $mode = strtolower($exam->exam_mode ?? 'online');

        // Rule: Question papers cannot be viewed/downloaded for physical examinations
        if (in_array($mode, ['offline', 'physical'], true)) {
            return back()->with('error', 'Question papers for physical/offline examinations cannot be downloaded.');
        }

        // Rule: Question papers cannot be downloaded for online examinations
        if ($mode === 'online') {
            return back()->with('error', 'Question papers for online examinations cannot be downloaded. Please attempt the exam online in the system.');
        }

        // Rule: For hybrid exams, question paper can only be downloaded after the exam has been started (timer is running)
        $attempt = $exam->studentAttempt($student->id);
        if (!$attempt || $attempt->status === 'not_started' || !$attempt->started_at) {
            return back()->with('error', 'You must start the examination first before downloading the question paper. The timer will begin upon starting.');
        }

        if (!$exam->exam_paper_url) {
            return back()->with('error', 'Exam paper is not available.');
        }

        return Storage::disk('public')->download($exam->exam_paper_url);
    }

    public function downloadAnswerBooklet(Exam $exam)
    {
        $student = Auth::user();

        $enrolled = $student->courseEnrollments()
            ->where('course_id', $exam->course_id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled) {
            abort(403, 'You must be enrolled in this course to download examination materials.');
        }

        if (!$student->hasZeroTuitionBalance()) {
            return back()->with('error', 'You must complete your tuition payment (zero balance) to access examination materials.');
        }

        $mode = strtolower($exam->exam_mode ?? 'online');

        // Rule: Answers cannot be viewed/downloaded for physical examinations
        if (in_array($mode, ['offline', 'physical'], true)) {
            return back()->with('error', 'Answer booklets for physical/offline examinations cannot be downloaded.');
        }

        // Rule: Answers cannot be downloaded before exams are attempted or in status of closed
        $attempt = $exam->studentAttempt($student->id);
        $isAttempted = $attempt && in_array($attempt->status, ['submitted', 'graded']);
        $isClosed = $exam->isPast();

        if (!$isAttempted && !$isClosed) {
            return back()->with('error', 'Answer booklets cannot be downloaded before the exam has been attempted or closed.');
        }

        if (!$exam->answer_booklet_url) {
            return back()->with('error', 'Answer booklet is not available.');
        }

        return Storage::disk('public')->download($exam->answer_booklet_url);
    }

    /**
     * Display and permit printing of the official Examination Clearance Permit / Pass.
     * Accessible only when the student's tuition balance is zero (0.00).
     */
    public function examinationPermit(Request $request)
    {
        $student = Auth::user();

        // Enforce limitation: students who have not completed tuition do not qualify to sit for exams / receive exam permit
        if (!$student->hasZeroTuitionBalance()) {
            $tuitionBalance = $student->getTuitionBalance();
            $currency = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
            return redirect()->route('student.exams.index')
                ->with('error', "Access Denied: Examination Permits are only available to candidates who have completed tuition fees with a zero (0.00) balance. Your current outstanding tuition balance is {$currency} " . number_format($tuitionBalance, 2) . ". Please clear your tuition fees in the Fee Portal to print your permit.");
        }

        $studentProfile = $student->studentProfile;
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->orWhere('is_current', true)->first();
        $semester = \App\Models\Semester::where('is_current', true)->orWhere('is_active', true)->first();

        // Retrieve registered courses for the current semester
        $enrolledCourses = $student->courseEnrollments()
            ->with(['course.department', 'course.faculty', 'course.semester'])
            ->where('status', 'enrolled')
            ->get();

        $currencyCode = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
        $tuitionBalance = 0.00;
        $totalBalance = $student->getTotalFeeBalance();

        $permitNumber = 'JBI-EXM-' . date('Y') . '-' . strtoupper(substr(md5($student->id . '-permit-token-' . date('Y')), 0, 8));

        return view('student.exams.permit', compact(
            'student',
            'studentProfile',
            'academicYear',
            'semester',
            'enrolledCourses',
            'currencyCode',
            'tuitionBalance',
            'totalBalance',
            'permitNumber'
        ));
    }
}
