<?php

namespace App\Http\Controllers\Faculty\HOD;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Grade;
use App\Models\CourseEnrollment;
use App\Models\LeaveRequest;
use App\Models\ProgramChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OperationsController extends Controller
{
    /**
     * Get the department for the current operations view.
     */
    private function getDepartment()
    {
        $user = auth()->user();

        // 1. Allow explicit department selection if user is Admin, Super Admin, Dean, or assigned HOD
        if (request()->filled('department_id')) {
            $reqDept = Department::with(['facultyMembers.user', 'programs', 'courses.instructor'])->find(request('department_id'));
            if ($reqDept) {
                if ($user->isAdmin() || $user->isSuperAdmin() || $user->hasRole('dean') || $reqDept->head_of_department_id === $user->id) {
                    return $reqDept;
                }
            }
        }

        // 2. Department where user is HOD
        $department = Department::with(['facultyMembers.user', 'programs', 'courses.instructor'])
            ->where('head_of_department_id', $user->id)
            ->first();

        // 3. Fallback for Administrators, Superadmins, or Deans without a direct HOD assignment
        if (!$department && ($user->isSuperAdmin() || $user->isAdmin() || $user->hasRole('dean') || $user->hasRole('faculty'))) {
            // Pick the first department with courses or results, or first academic department
            $department = Department::with(['facultyMembers.user', 'programs', 'courses.instructor'])
                ->whereHas('courses')
                ->first() ?? Department::first();
        }

        if (!$department) {
            abort(404, 'No departmental assignment found for this Head of Department.');
        }

        return $department;
    }

    /**
     * Display the HOD Operations Hub with comprehensive Result Moderation, Leave, and Program Transfers.
     */
    public function index(Request $request)
    {
        $department = $this->getDepartment();

        // Accessible departments for switching if user has administrative or broad privileges
        $accessibleDepartments = [];
        if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->hasRole('dean')) {
            $accessibleDepartments = Department::where('type', Department::TYPE_ACADEMIC)->orderBy('name')->get();
        }

        // 1. Leave Requests from Faculty in this department
        $facultyUserIds = $department->facultyMembers->pluck('user_id');
        $pendingLeaves = LeaveRequest::whereIn('user_id', $facultyUserIds)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        // 2. Program Change Requests coming into this department's programs
        $programIds = $department->programs->pluck('id');
        $pendingProgramChanges = ProgramChangeRequest::whereIn('requested_program_id', $programIds)
            ->where('status', 'pending')
            ->with(['student', 'currentProgram', 'requestedProgram'])
            ->get();

        // 3. Result Moderation: Discover all Coursework, Exams, and Quizzes with results
        $departmentCourses = $department->courses()->with('instructor')->get();
        $courseIds = $departmentCourses->pluck('id');

        $moderationItems = collect();

        // (a) Coursework Assignments
        $assignments = Assignment::whereIn('course_id', $courseIds)
            ->with(['course.instructor', 'submissions' => function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('score')->orWhere('status', 'graded');
                });
            }])
            ->whereHas('submissions', function ($q) {
                $q->whereNotNull('score')->orWhere('status', 'graded');
            })
            ->get();

        foreach ($assignments as $assignment) {
            $gradedCount = $assignment->submissions->count();
            $publishedCount = Grade::where('assignment_id', $assignment->id)->where('is_published', true)->count();
            $unpublishedGrades = Grade::where('assignment_id', $assignment->id)->where('is_published', false)->count();
            $pendingCount = max($unpublishedGrades, max(0, $gradedCount - $publishedCount));
            $isModerated = ($pendingCount === 0 && $gradedCount > 0);
            $avgScore = $assignment->submissions->avg('score') ?? 0;
            $maxPoints = (float) ($assignment->max_points ?: ($assignment->max_score ?: 100));
            $avgPercentage = $maxPoints > 0 ? round(($avgScore / $maxPoints) * 100, 1) : 0;

            $moderationItems->push((object) [
                'id' => $assignment->id,
                'type' => 'assignment',
                'type_label' => 'Coursework Assignment',
                'type_badge' => 'badge bg-primary text-white',
                'type_icon' => 'bi-journal-text',
                'title' => $assignment->title,
                'course_id' => $assignment->course_id,
                'course_code' => $assignment->course->code,
                'course_name' => $assignment->course->name,
                'lecturer_name' => $assignment->course->instructor->name ?? 'Course Lecturer',
                'total_submissions' => $assignment->submissions()->count(),
                'graded_count' => $gradedCount,
                'unmoderated_count' => $pendingCount,
                'is_moderated' => $isModerated,
                'avg_score' => round($avgScore, 1),
                'avg_percentage' => $avgPercentage,
                'max_points' => $maxPoints,
                'date' => $assignment->due_date,
            ]);
        }

        // (b) Semester Exams
        $exams = Exam::whereIn('course_id', $courseIds)
            ->with(['course.instructor', 'attempts' => function ($q) {
                $q->whereNotNull('marks_obtained')->orWhere('status', 'graded');
            }])
            ->whereHas('attempts', function ($q) {
                $q->whereNotNull('marks_obtained')->orWhere('status', 'graded');
            })
            ->get();

        foreach ($exams as $exam) {
            $gradedCount = $exam->attempts->count();
            $publishedCount = Grade::where('course_id', $exam->course_id)
                ->where('grade_type', 'exam')
                ->where('is_published', true)
                ->count();
            $unpublishedGrades = Grade::where('course_id', $exam->course_id)
                ->where('grade_type', 'exam')
                ->where('is_published', false)
                ->count();
            $pendingCount = max($unpublishedGrades, max(0, $gradedCount - $publishedCount));
            $isModerated = ($pendingCount === 0 && $gradedCount > 0);
            $avgScore = $exam->attempts->avg('marks_obtained') ?? 0;
            $maxPoints = (float) ($exam->total_marks ?: 100);
            $avgPercentage = $maxPoints > 0 ? round(($avgScore / $maxPoints) * 100, 1) : 0;

            $moderationItems->push((object) [
                'id' => $exam->id,
                'type' => 'exam',
                'type_label' => 'Semester Examination',
                'type_badge' => 'badge bg-danger text-white',
                'type_icon' => 'bi-file-earmark-medical',
                'title' => $exam->title,
                'course_id' => $exam->course_id,
                'course_code' => $exam->course->code,
                'course_name' => $exam->course->name,
                'lecturer_name' => $exam->course->instructor->name ?? 'Exam Officer',
                'total_submissions' => $exam->attempts()->count(),
                'graded_count' => $gradedCount,
                'unmoderated_count' => $pendingCount,
                'is_moderated' => $isModerated,
                'avg_score' => round($avgScore, 1),
                'avg_percentage' => $avgPercentage,
                'max_points' => $maxPoints,
                'date' => $exam->start_time ?? $exam->created_at,
            ]);
        }

        // (c) Quizzes & Tests
        $quizzes = Quiz::whereIn('course_id', $courseIds)
            ->with(['course.instructor', 'attempts' => function ($q) {
                $q->whereNotNull('score')->orWhere('status', 'graded');
            }])
            ->whereHas('attempts', function ($q) {
                $q->whereNotNull('score')->orWhere('status', 'graded');
            })
            ->get();

        foreach ($quizzes as $quiz) {
            $gradedCount = $quiz->attempts->count();
            $publishedCount = Grade::where('course_id', $quiz->course_id)
                ->where('grade_type', 'quiz')
                ->where('is_published', true)
                ->count();
            $unpublishedGrades = Grade::where('course_id', $quiz->course_id)
                ->where('grade_type', 'quiz')
                ->where('is_published', false)
                ->count();
            $pendingCount = max($unpublishedGrades, max(0, $gradedCount - $publishedCount));
            $isModerated = ($pendingCount === 0 && $gradedCount > 0);
            $avgScore = $quiz->attempts->avg('score') ?? 0;
            $maxPoints = (float) ($quiz->total_points ?: 100);
            $avgPercentage = $maxPoints > 0 ? round(($avgScore / $maxPoints) * 100, 1) : 0;

            $moderationItems->push((object) [
                'id' => $quiz->id,
                'type' => 'quiz',
                'type_label' => 'Quiz / Continuous Test',
                'type_badge' => 'badge bg-warning text-dark',
                'type_icon' => 'bi-patch-question',
                'title' => $quiz->title,
                'course_id' => $quiz->course_id,
                'course_code' => $quiz->course->code,
                'course_name' => $quiz->course->name,
                'lecturer_name' => $quiz->course->instructor->name ?? 'Course Lecturer',
                'total_submissions' => $quiz->attempts()->count(),
                'graded_count' => $gradedCount,
                'unmoderated_count' => $pendingCount,
                'is_moderated' => $isModerated,
                'avg_score' => round($avgScore, 1),
                'avg_percentage' => $avgPercentage,
                'max_points' => $maxPoints,
                'date' => $quiz->created_at,
            ]);
        }

        // Summary Counts across all assessments
        $allModerationItems = clone $moderationItems;
        $totalAssessmentsCount = $allModerationItems->count();
        $pendingItemsCount = $allModerationItems->where('is_moderated', false)->count();
        $totalPendingGrades = $allModerationItems->sum('unmoderated_count');
        $moderatedItemsCount = $allModerationItems->where('is_moderated', true)->count();

        // Apply UI Filters
        $filterType = $request->query('type', 'all');
        $filterStatus = $request->query('status', 'all');
        $filterCourse = $request->query('course_id', 'all');

        if ($filterType !== 'all') {
            $moderationItems = $moderationItems->where('type', $filterType);
        }
        if ($filterStatus === 'pending') {
            $moderationItems = $moderationItems->where('is_moderated', false);
        } elseif ($filterStatus === 'moderated') {
            $moderationItems = $moderationItems->where('is_moderated', true);
        }
        if ($filterCourse !== 'all' && is_numeric($filterCourse)) {
            $moderationItems = $moderationItems->where('course_id', (int) $filterCourse);
        }

        // Backwards compatibility variable for existing views
        $assignmentsToModerate = $assignments;
        $pendingGradesCount = $totalPendingGrades;

        return view('faculty.hod.operations.index', compact(
            'department',
            'accessibleDepartments',
            'departmentCourses',
            'pendingLeaves',
            'pendingProgramChanges',
            'moderationItems',
            'totalAssessmentsCount',
            'pendingItemsCount',
            'totalPendingGrades',
            'moderatedItemsCount',
            'filterType',
            'filterStatus',
            'filterCourse',
            'assignmentsToModerate',
            'pendingGradesCount'
        ));
    }

    /**
     * Show detailed result moderation screen with student roster, score distribution, and mark adjustments.
     */
    public function showModeration($type, $id)
    {
        $department = $this->getDepartment();
        $courseIds = $department->courses()->pluck('id')->toArray();

        $item = null;
        $candidates = collect();
        $maxPoints = 100;
        $title = '';
        $course = null;

        if ($type === 'assignment') {
            $assignment = Assignment::with(['course.instructor', 'submissions.student.studentProfile'])->findOrFail($id);
            if (!in_array($assignment->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized department access.');
            }
            $course = $assignment->course;
            $title = $assignment->title;
            $maxPoints = (float) ($assignment->max_points ?: ($assignment->max_score ?: 100));

            foreach ($assignment->submissions()->with('student.studentProfile')->get() as $sub) {
                if ($sub->score === null && $sub->status !== 'graded') {
                    continue;
                }
                $existingGrade = Grade::where('assignment_id', $assignment->id)->where('user_id', $sub->user_id)->first();
                $score = (float) ($sub->adjusted_score ?? $sub->score);
                $percentage = $maxPoints > 0 ? round(($score / $maxPoints) * 100, 1) : 0;
                $letter = self::calculateLetterGrade($percentage);

                $candidates->push((object) [
                    'record_id' => $sub->id,
                    'user_id' => $sub->user_id,
                    'student_name' => $sub->student->name ?? 'Unknown Student',
                    'student_email' => $sub->student->email ?? 'N/A',
                    'admission_number' => $sub->student->studentProfile?->admission_number ?? ('ADM-' . $sub->user_id),
                    'raw_score' => (float) $sub->score,
                    'moderated_score' => $score,
                    'percentage' => $percentage,
                    'letter_grade' => $letter,
                    'feedback' => $sub->feedback,
                    'is_published' => (bool) ($existingGrade?->is_published ?? false),
                    'graded_at' => $sub->graded_at,
                ]);
            }
            $item = $assignment;
        } elseif ($type === 'exam') {
            $exam = Exam::with(['course.instructor', 'attempts.user.studentProfile'])->findOrFail($id);
            if (!in_array($exam->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized department access.');
            }
            $course = $exam->course;
            $title = $exam->title;
            $maxPoints = (float) ($exam->total_marks ?: 100);

            foreach ($exam->attempts()->with('user.studentProfile')->get() as $attempt) {
                if ($attempt->marks_obtained === null && $attempt->status !== 'graded') {
                    continue;
                }
                $existingGrade = Grade::where('course_id', $exam->course_id)
                    ->where('user_id', $attempt->user_id)
                    ->where('grade_type', 'exam')
                    ->first();
                $score = (float) $attempt->marks_obtained;
                $percentage = $maxPoints > 0 ? round(($score / $maxPoints) * 100, 1) : 0;
                $letter = self::calculateLetterGrade($percentage);

                $candidates->push((object) [
                    'record_id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'student_name' => $attempt->user->name ?? 'Unknown Student',
                    'student_email' => $attempt->user->email ?? 'N/A',
                    'admission_number' => $attempt->user->studentProfile?->admission_number ?? ('ADM-' . $attempt->user_id),
                    'raw_score' => $score,
                    'moderated_score' => $score,
                    'percentage' => $percentage,
                    'letter_grade' => $letter,
                    'feedback' => $attempt->feedback,
                    'is_published' => (bool) ($existingGrade?->is_published ?? false),
                    'graded_at' => $attempt->graded_at ?? $attempt->submitted_at,
                ]);
            }
            $item = $exam;
        } elseif ($type === 'quiz') {
            $quiz = Quiz::with(['course.instructor', 'attempts.user.studentProfile'])->findOrFail($id);
            if (!in_array($quiz->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized department access.');
            }
            $course = $quiz->course;
            $title = $quiz->title;
            $maxPoints = (float) ($quiz->total_points ?: 100);

            foreach ($quiz->attempts()->with('user.studentProfile')->get() as $attempt) {
                if ($attempt->score === null && $attempt->status !== 'graded') {
                    continue;
                }
                $existingGrade = Grade::where('course_id', $quiz->course_id)
                    ->where('user_id', $attempt->user_id)
                    ->where('grade_type', 'quiz')
                    ->first();
                $score = (float) $attempt->score;
                $percentage = $maxPoints > 0 ? round(($score / $maxPoints) * 100, 1) : (float) ($attempt->percentage ?? 0);
                $letter = self::calculateLetterGrade($percentage);

                $candidates->push((object) [
                    'record_id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'student_name' => $attempt->user->name ?? 'Unknown Student',
                    'student_email' => $attempt->user->email ?? 'N/A',
                    'admission_number' => $attempt->user->studentProfile?->admission_number ?? ('ADM-' . $attempt->user_id),
                    'raw_score' => $score,
                    'moderated_score' => $score,
                    'percentage' => $percentage,
                    'letter_grade' => $letter,
                    'feedback' => $attempt->feedback,
                    'is_published' => (bool) ($existingGrade?->is_published ?? false),
                    'graded_at' => $attempt->created_at,
                ]);
            }
            $item = $quiz;
        } else {
            abort(404, 'Invalid assessment moderation type.');
        }

        // Calculate Grade Distribution & Statistics
        $totalCandidates = $candidates->count();
        $averagePercentage = $totalCandidates > 0 ? round($candidates->avg('percentage'), 1) : 0;
        $highestPercentage = $totalCandidates > 0 ? $candidates->max('percentage') : 0;
        $lowestPercentage = $totalCandidates > 0 ? $candidates->min('percentage') : 0;

        $gradeDistribution = [
            'A' => $candidates->filter(fn($c) => $c->percentage >= 80)->count(),
            'B' => $candidates->filter(fn($c) => $c->percentage >= 70 && $c->percentage < 80)->count(),
            'C' => $candidates->filter(fn($c) => $c->percentage >= 60 && $c->percentage < 70)->count(),
            'D' => $candidates->filter(fn($c) => $c->percentage >= 50 && $c->percentage < 60)->count(),
            'F' => $candidates->filter(fn($c) => $c->percentage < 50)->count(),
        ];

        $passCount = $candidates->filter(fn($c) => $c->percentage >= 50)->count();
        $passRate = $totalCandidates > 0 ? round(($passCount / $totalCandidates) * 100, 1) : 0;
        $publishedCount = $candidates->where('is_published', true)->count();
        $isFullyPublished = ($publishedCount === $totalCandidates && $totalCandidates > 0);

        return view('faculty.hod.operations.moderate', compact(
            'department',
            'type',
            'id',
            'item',
            'title',
            'course',
            'maxPoints',
            'candidates',
            'totalCandidates',
            'averagePercentage',
            'highestPercentage',
            'lowestPercentage',
            'gradeDistribution',
            'passRate',
            'publishedCount',
            'isFullyPublished'
        ));
    }

    /**
     * Approve and publish moderated results for an assessment, synchronizing all grades to student portals.
     */
    public function approveResults($type, $id)
    {
        $department = $this->getDepartment();
        $courseIds = $department->courses()->pluck('id')->toArray();

        $publishedCount = 0;
        $courseId = null;
        $assessmentTitle = '';

        if ($type === 'assignment') {
            $assignment = Assignment::with(['course', 'submissions'])->findOrFail($id);
            if (!in_array($assignment->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $courseId = $assignment->course_id;
            $assessmentTitle = $assignment->title;
            $maxPoints = (float) ($assignment->max_points ?: ($assignment->max_score ?: 100));

            DB::transaction(function () use ($assignment, $maxPoints, &$publishedCount) {
                foreach ($assignment->submissions()->whereNotNull('score')->get() as $sub) {
                    $score = (float) ($sub->adjusted_score ?? $sub->score);
                    $percentage = $maxPoints > 0 ? ($score / $maxPoints) * 100 : 0;
                    $letter = self::calculateLetterGrade($percentage);
                    $gradePoints = self::calculateGradePoints($percentage);

                    Grade::updateOrCreate(
                        [
                            'user_id' => $sub->user_id,
                            'course_id' => $assignment->course_id,
                            'assignment_id' => $assignment->id,
                        ],
                        [
                            'grade_type' => 'assignment',
                            'points_earned' => $score,
                            'points_possible' => $maxPoints,
                            'percentage' => round($percentage, 2),
                            'letter_grade' => $letter,
                            'grade_points' => $gradePoints,
                            'comments' => $sub->feedback,
                            'is_published' => true,
                            'graded_at' => $sub->graded_at ?? now(),
                            'graded_by' => auth()->id(),
                        ]
                    );

                    $sub->update([
                        'status' => 'graded',
                    ]);

                    $publishedCount++;
                }

                // Also publish any pre-existing Grade records for this assignment
                Grade::where('assignment_id', $assignment->id)->update(['is_published' => true]);
            });
        } elseif ($type === 'exam') {
            $exam = Exam::with(['course', 'attempts'])->findOrFail($id);
            if (!in_array($exam->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $courseId = $exam->course_id;
            $assessmentTitle = $exam->title;
            $maxPoints = (float) ($exam->total_marks ?: 100);

            DB::transaction(function () use ($exam, $maxPoints, &$publishedCount) {
                foreach ($exam->attempts()->whereNotNull('marks_obtained')->get() as $attempt) {
                    $score = (float) $attempt->marks_obtained;
                    $percentage = $maxPoints > 0 ? ($score / $maxPoints) * 100 : 0;
                    $letter = self::calculateLetterGrade($percentage);
                    $gradePoints = self::calculateGradePoints($percentage);

                    Grade::updateOrCreate(
                        [
                            'user_id' => $attempt->user_id,
                            'course_id' => $exam->course_id,
                            'grade_type' => 'exam',
                        ],
                        [
                            'assignment_id' => null,
                            'points_earned' => $score,
                            'points_possible' => $maxPoints,
                            'percentage' => round($percentage, 2),
                            'letter_grade' => $letter,
                            'grade_points' => $gradePoints,
                            'comments' => $attempt->feedback ?? ("Exam: " . $exam->title),
                            'is_published' => true,
                            'graded_at' => $attempt->graded_at ?? now(),
                            'graded_by' => auth()->id(),
                        ]
                    );

                    $attempt->update(['status' => 'graded']);
                    $publishedCount++;
                }

                Grade::where('course_id', $exam->course_id)->where('grade_type', 'exam')->update(['is_published' => true]);
            });
        } elseif ($type === 'quiz') {
            $quiz = Quiz::with(['course', 'attempts'])->findOrFail($id);
            if (!in_array($quiz->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $courseId = $quiz->course_id;
            $assessmentTitle = $quiz->title;
            $maxPoints = (float) ($quiz->total_points ?: 100);

            DB::transaction(function () use ($quiz, $maxPoints, &$publishedCount) {
                foreach ($quiz->attempts()->whereNotNull('score')->get() as $attempt) {
                    $score = (float) $attempt->score;
                    $percentage = $maxPoints > 0 ? ($score / $maxPoints) * 100 : (float) ($attempt->percentage ?? 0);
                    $letter = self::calculateLetterGrade($percentage);
                    $gradePoints = self::calculateGradePoints($percentage);

                    Grade::updateOrCreate(
                        [
                            'user_id' => $attempt->user_id,
                            'course_id' => $quiz->course_id,
                            'grade_type' => 'quiz',
                        ],
                        [
                            'assignment_id' => null,
                            'points_earned' => $score,
                            'points_possible' => $maxPoints,
                            'percentage' => round($percentage, 2),
                            'letter_grade' => $letter,
                            'grade_points' => $gradePoints,
                            'comments' => $attempt->feedback ?? ("Quiz: " . $quiz->title),
                            'is_published' => true,
                            'graded_at' => now(),
                            'graded_by' => auth()->id(),
                        ]
                    );

                    $attempt->update(['status' => 'graded']);
                    $publishedCount++;
                }

                Grade::where('course_id', $quiz->course_id)->where('grade_type', 'quiz')->update(['is_published' => true]);
            });
        } else {
            abort(404);
        }

        // Reconcile and update overall course enrollments final grades
        if ($courseId) {
            $this->reconcileCourseGrades($courseId);
        }

        return redirect()->route('faculty.hod.operations.index')
            ->with('success', "Results for '{$assessmentTitle}' successfully moderated, endorsed, and published for {$publishedCount} candidates.");
    }

    /**
     * Adjust individual student marks or apply uniform grade curves during HOD moderation.
     */
    public function adjustResults(Request $request, $type, $id)
    {
        $department = $this->getDepartment();
        $courseIds = $department->courses()->pluck('id')->toArray();

        if ($type === 'assignment') {
            $assignment = Assignment::findOrFail($id);
            if (!in_array($assignment->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $maxPoints = (float) ($assignment->max_points ?: ($assignment->max_score ?: 100));

            // Uniform Curve / Scale
            if ($request->filled('scale_points')) {
                $scale = (float) $request->input('scale_points');
                foreach ($assignment->submissions()->whereNotNull('score')->get() as $sub) {
                    $newScore = min($maxPoints, max(0, ((float) $sub->score) + $scale));
                    $sub->update([
                        'adjusted_score' => $newScore,
                        'feedback' => trim(($sub->feedback ? $sub->feedback . "\n" : "") . "HOD Moderation: Applied uniform mark adjustment of " . ($scale >= 0 ? "+{$scale}" : "{$scale}") . " points."),
                    ]);
                }
                return back()->with('success', "Applied uniform score adjustment of {$scale} marks across all candidates.");
            }

            // Individual candidate adjustments
            if ($request->has('scores') && is_array($request->scores)) {
                foreach ($request->scores as $subId => $newScore) {
                    $submission = AssignmentSubmission::where('id', $subId)->where('assignment_id', $assignment->id)->first();
                    if ($submission && is_numeric($newScore)) {
                        $bounded = min($maxPoints, max(0, (float) $newScore));
                        $submission->update([
                            'adjusted_score' => $bounded,
                            'score' => $bounded,
                        ]);
                    }
                }
                return back()->with('success', 'Candidate marks adjusted and saved successfully.');
            }
        } elseif ($type === 'exam') {
            $exam = Exam::findOrFail($id);
            if (!in_array($exam->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $maxPoints = (float) ($exam->total_marks ?: 100);

            if ($request->filled('scale_points')) {
                $scale = (float) $request->input('scale_points');
                foreach ($exam->attempts()->whereNotNull('marks_obtained')->get() as $attempt) {
                    $newScore = min($maxPoints, max(0, ((float) $attempt->marks_obtained) + $scale));
                    $attempt->update(['marks_obtained' => $newScore]);
                }
                return back()->with('success', "Applied uniform exam adjustment of {$scale} marks across candidates.");
            }

            if ($request->has('scores') && is_array($request->scores)) {
                foreach ($request->scores as $attemptId => $newScore) {
                    $attempt = ExamAttempt::where('id', $attemptId)->where('exam_id', $exam->id)->first();
                    if ($attempt && is_numeric($newScore)) {
                        $bounded = min($maxPoints, max(0, (float) $newScore));
                        $attempt->update(['marks_obtained' => $bounded]);
                    }
                }
                return back()->with('success', 'Candidate exam marks updated successfully.');
            }
        } elseif ($type === 'quiz') {
            $quiz = Quiz::findOrFail($id);
            if (!in_array($quiz->course_id, $courseIds) && !auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
                abort(403);
            }

            $maxPoints = (float) ($quiz->total_points ?: 100);

            if ($request->has('scores') && is_array($request->scores)) {
                foreach ($request->scores as $attemptId => $newScore) {
                    $attempt = QuizAttempt::where('id', $attemptId)->where('quiz_id', $quiz->id)->first();
                    if ($attempt && is_numeric($newScore)) {
                        $bounded = min($maxPoints, max(0, (float) $newScore));
                        $attempt->update(['score' => $bounded]);
                    }
                }
                return back()->with('success', 'Candidate quiz marks updated successfully.');
            }
        }

        return back()->with('info', 'No score adjustments were applied.');
    }

    /**
     * Return assessment results back to the lecturer for revision.
     */
    public function returnResults(Request $request, $type, $id)
    {
        $request->validate([
            'moderation_feedback' => 'required|string|max:1000',
        ]);

        $feedback = $request->input('moderation_feedback');

        if ($type === 'assignment') {
            $assignment = Assignment::with('course.instructor')->findOrFail($id);
            $assignment->submissions()->update([
                'status' => 'submitted',
                'feedback' => DB::raw("CONCAT(COALESCE(feedback, ''), '\n[HOD Moderation Revision Required]: " . addslashes($feedback) . "')"),
            ]);
            Grade::where('assignment_id', $assignment->id)->update(['is_published' => false]);
        }

        return redirect()->route('faculty.hod.operations.index')
            ->with('success', 'Assessment results returned to course lecturer with moderation feedback for revision.');
    }

    /**
     * Legacy single-click grade moderation endpoint for backwards compatibility.
     */
    public function moderateGrades(Request $request, Assignment $assignment)
    {
        return $this->approveResults('assignment', $assignment->id);
    }

    /**
     * Reconcile cumulative course grades for all enrolled students.
     */
    private function reconcileCourseGrades(int $courseId): void
    {
        $enrollments = CourseEnrollment::where('course_id', $courseId)->get();

        foreach ($enrollments as $enrollment) {
            $grades = Grade::where('course_id', $courseId)
                ->where('user_id', $enrollment->user_id)
                ->where('is_published', true)
                ->get();

            if ($grades->isNotEmpty()) {
                $avgPercentage = round($grades->avg('percentage'), 2);
                $letter = self::calculateLetterGrade($avgPercentage);
                $points = self::calculateGradePoints($avgPercentage);

                $enrollment->update([
                    'final_grade' => $avgPercentage,
                    'letter_grade' => $letter,
                    'grade_points' => $points,
                ]);
            }
        }
    }

    /**
     * Helper to compute letter grades according to university academic standards.
     */
    public static function calculateLetterGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Helper to compute 4.0 scale grade points.
     */
    public static function calculateGradePoints(float $percentage): float
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }

    public function approveLeave(Request $request, LeaveRequest $leave)
    {
        $department = $this->getDepartment();
        if (!$department->facultyMembers->contains('user_id', $leave->user_id) && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $leave->update([
            'status' => 'approved',
            'manager_id' => auth()->id(),
            'manager_comment' => $request->input('manager_comment', 'Approved by HOD.'),
        ]);

        return back()->with('success', 'Leave request approved.');
    }

    public function rejectLeave(Request $request, LeaveRequest $leave)
    {
        $department = $this->getDepartment();
        if (!$department->facultyMembers->contains('user_id', $leave->user_id) && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $leave->update([
            'status' => 'rejected',
            'manager_id' => auth()->id(),
            'manager_comment' => $request->input('manager_comment', 'Rejected by HOD.'),
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    public function endorseProgramChange(Request $request, ProgramChangeRequest $programChange)
    {
        $department = $this->getDepartment();
        if (!in_array($programChange->requested_program_id, $department->programs->pluck('id')->toArray()) && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $existingNotes = $programChange->review_notes;
        $endorsement = "Endorsed by HOD ({$department->name}) on " . now()->format('Y-m-d H:i') . ".";
        
        $programChange->update([
            'review_notes' => $existingNotes ? $existingNotes . "\n" . $endorsement : $endorsement,
        ]);

        return back()->with('success', 'Program change request endorsed successfully. Sent to Registrar.');
    }
}
