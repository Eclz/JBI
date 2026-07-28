<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseMaterial;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\LearningProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class LmsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = now();
        $dueWindowEnd = now()->addDays(7);

        $enrollments = CourseEnrollment::with(['course.semester', 'course.instructor'])
            ->where('user_id', $userId)
            ->where('status', 'enrolled')
            ->orderByDesc('enrollment_date')
            ->get();

        $courseIds = $enrollments->pluck('course_id')->filter()->values();

        $lastActivityByCourse = LearningProgress::where('user_id', $userId)
            ->whereIn('course_id', $courseIds)
            ->select('course_id')
            ->selectRaw('MAX(last_accessed_at) as last_accessed_at')
            ->groupBy('course_id')
            ->pluck('last_accessed_at', 'course_id');

        $courses = $enrollments->map(function ($enrollment) use ($userId) {
            $course = $enrollment->course;
            $progress = $this->courseProgress($course->id, $userId);

            return [
                'course' => $course,
                'progress' => $progress,
            ];
        });

        $courses = $courses->map(function ($entry) use ($lastActivityByCourse) {
            $courseId = $entry['course']->id;
            $lastActivity = $lastActivityByCourse[$courseId] ?? null;
            $entry['last_activity'] = $lastActivity ? Carbon::parse($lastActivity) : null;

            return $entry;
        });

        $upcomingAssignments = Assignment::with('course')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$now, $dueWindowEnd])
            ->get()
            ->map(function ($assignment) {
                return [
                    'type' => 'Assignment',
                    'title' => $assignment->title,
                    'course' => $assignment->course?->name,
                    'due_at' => $assignment->due_date,
                    'link' => route('student.assignments.show', $assignment),
                ];
            });

        $upcomingQuizzes = Quiz::with('course')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->whereNotNull('start_time')
            ->whereBetween('start_time', [$now, $dueWindowEnd])
            ->get()
            ->map(function ($quiz) {
                return [
                    'type' => 'Quiz',
                    'title' => $quiz->title,
                    'course' => $quiz->course?->name,
                    'due_at' => $quiz->start_time,
                    'link' => route('student.quizzes.show', $quiz),
                ];
            });

        $upcomingExams = Exam::with('course')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->whereNotNull('start_time')
            ->whereBetween('start_time', [$now, $dueWindowEnd])
            ->get()
            ->map(function ($exam) {
                return [
                    'type' => 'Exam',
                    'title' => $exam->title,
                    'course' => $exam->course?->name,
                    'due_at' => $exam->start_time,
                    'link' => route('student.exams.show', $exam),
                ];
            });

        $upcomingItems = $upcomingAssignments
            ->merge($upcomingQuizzes)
            ->merge($upcomingExams)
            ->sortBy('due_at')
            ->values();

        $summary = [
            'total_courses' => $courses->count(),
            'avg_progress' => $courses->count() > 0 ? round($courses->avg(fn ($entry) => $entry['progress']['percent']), 1) : 0,
            'total_completed' => $courses->sum(fn ($entry) => $entry['progress']['completed']),
            'total_items' => $courses->sum(fn ($entry) => $entry['progress']['total']),
            'due_7_days' => $upcomingItems->count(),
        ];

        return view('student.lms.index', compact('courses', 'summary', 'upcomingItems'));
    }

    public function show(Course $course)
    {
        $userId = Auth::id();

        $enrolled = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $course->load(['semester', 'instructor', 'department']);

        $progress = $this->courseProgress($course->id, $userId);
        $completed = $this->completedIds($course->id, $userId);
        $materialProgress = LearningProgress::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('content_type', 'material')
            ->get()
            ->keyBy('content_id');

        $materials = CourseMaterial::where('course_id', $course->id)
            ->where('is_published', true)
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        $assignments = Assignment::where('course_id', $course->id)
            ->where('is_published', true)
            ->orderBy('due_date')
            ->get();

        $quizzes = Quiz::where('course_id', $course->id)
            ->where('is_published', true)
            ->orderBy('start_time')
            ->get();

        $exams = Exam::where('course_id', $course->id)
            ->where('is_published', true)
            ->orderBy('start_time')
            ->get();

        return view('student.lms.course', compact(
            'course',
            'progress',
            'completed',
            'materialProgress',
            'materials',
            'assignments',
            'quizzes',
            'exams'
        ));
    }

    public function material(Course $course, CourseMaterial $material)
    {
        $userId = Auth::id();

        $enrolled = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled || $material->course_id !== $course->id || !$material->is_published) {
            abort(403);
        }

        $progress = LearningProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $course->id,
                'content_type' => 'material',
                'content_id' => $material->id,
            ],
            [
                'last_accessed_at' => now(),
            ]
        );

        $progress->update([
            'last_accessed_at' => now(),
        ]);

        return view('student.lms.material', compact('course', 'material', 'progress'));
    }

    public function trackMaterial(Request $request, Course $course, CourseMaterial $material)
    {
        $userId = Auth::id();

        $enrolled = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled || $material->course_id !== $course->id || !$material->is_published) {
            abort(403);
        }

        $validated = $request->validate([
            'event' => 'required|in:read,video_progress,video_complete',
            'watched_seconds' => 'nullable|integer|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'position_seconds' => 'nullable|integer|min:0',
        ]);

        $progress = LearningProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $course->id,
                'content_type' => 'material',
                'content_id' => $material->id,
            ]
        );

        $updateData = [
            'last_accessed_at' => now(),
        ];

        if ($validated['event'] === 'read') {
            $updateData['read_at'] = now();
            $updateData['completed_at'] = $progress->completed_at ?? now();
        }

        if (in_array($validated['event'], ['video_progress', 'video_complete'], true)) {
            $watched = max((int) ($validated['watched_seconds'] ?? 0), (int) ($progress->video_watched_seconds ?? 0));
            $duration = max((int) ($validated['duration_seconds'] ?? 0), (int) ($progress->video_duration_seconds ?? 0));
            $position = max((int) ($validated['position_seconds'] ?? 0), (int) ($progress->last_video_position_seconds ?? 0));
            $isComplete = $validated['event'] === 'video_complete';

            if (!$isComplete && $duration > 0) {
                $isComplete = ($watched / $duration) >= 0.9;
            }

            $updateData['video_watched_seconds'] = $watched;
            $updateData['video_duration_seconds'] = $duration;
            $updateData['last_video_position_seconds'] = $position;
            $updateData['is_video_completed'] = $isComplete;

            if ($isComplete) {
                $updateData['completed_at'] = $progress->completed_at ?? now();
            }
        }

        $progress->update($updateData);

        return response()->json([
            'ok' => true,
            'progress_id' => $progress->id,
        ]);
    }

    public function markComplete(Request $request, Course $course)
    {
        $userId = Auth::id();

        $enrolled = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $validated = $request->validate([
            'content_type' => 'required|in:material,assignment,quiz,exam',
            'content_id' => 'required|integer|min:1',
        ]);

        $exists = match ($validated['content_type']) {
            'material' => CourseMaterial::where('course_id', $course->id)->where('id', $validated['content_id'])->exists(),
            'assignment' => Assignment::where('course_id', $course->id)->where('id', $validated['content_id'])->exists(),
            'quiz' => Quiz::where('course_id', $course->id)->where('id', $validated['content_id'])->exists(),
            'exam' => Exam::where('course_id', $course->id)->where('id', $validated['content_id'])->exists(),
            default => false,
        };

        if (!$exists) {
            return back()->withErrors(['error' => 'Learning item was not found in this course.']);
        }

        LearningProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $course->id,
                'content_type' => $validated['content_type'],
                'content_id' => $validated['content_id'],
            ],
            [
                'completed_at' => now(),
                'last_accessed_at' => now(),
            ]
        );

        return back()->with('success', 'Learning progress updated.');
    }

    public function certificate(Course $course)
    {
        $userId = Auth::id();
        $student = Auth::user();

        $enrolled = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $course->load(['semester', 'instructor', 'department']);
        $progress = $this->courseProgress($course->id, $userId);

        if ($progress['total'] === 0 || $progress['percent'] < 100) {
            return redirect()
                ->route('student.lms.show', $course)
                ->withErrors(['error' => 'Certificate is available only after 100% course completion.']);
        }

        $completionDate = LearningProgress::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->max('completed_at') ?? now();

        return view('student.lms.certificate', compact(
            'course',
            'student',
            'progress',
            'completionDate'
        ));
    }

    private function courseProgress(int $courseId, int $userId): array
    {
        $totalMaterials = CourseMaterial::where('course_id', $courseId)->where('is_published', true)->count();
        $totalAssignments = Assignment::where('course_id', $courseId)->where('is_published', true)->count();
        $totalQuizzes = Quiz::where('course_id', $courseId)->where('is_published', true)->count();
        $totalExams = Exam::where('course_id', $courseId)->where('is_published', true)->count();

        $completedMaterials = LearningProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('content_type', 'material')
            ->where(function ($query) {
                $query->whereNotNull('completed_at')
                    ->orWhereNotNull('read_at')
                    ->orWhere('is_video_completed', true);
            })
            ->count();

        $completedAssignments = AssignmentSubmission::where('user_id', $userId)
            ->whereHas('assignment', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where('status', '!=', 'draft')
            ->distinct('assignment_id')
            ->count('assignment_id');

        $completedQuizzes = QuizAttempt::where('user_id', $userId)
            ->whereHas('quiz', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where(function ($query) {
                $query->whereNotNull('submitted_at')
                    ->orWhereIn('status', ['submitted', 'graded']);
            })
            ->distinct('quiz_id')
            ->count('quiz_id');

        $completedExams = ExamAttempt::where('user_id', $userId)
            ->whereHas('exam', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where(function ($query) {
                $query->whereNotNull('submitted_at')
                    ->orWhereIn('status', ['submitted', 'graded']);
            })
            ->distinct('exam_id')
            ->count('exam_id');

        $total = $totalMaterials + $totalAssignments + $totalQuizzes + $totalExams;
        $completed = $completedMaterials + $completedAssignments + $completedQuizzes + $completedExams;
        $percent = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => $percent,
            'materials' => [$completedMaterials, $totalMaterials],
            'assignments' => [$completedAssignments, $totalAssignments],
            'quizzes' => [$completedQuizzes, $totalQuizzes],
            'exams' => [$completedExams, $totalExams],
        ];
    }

    private function completedIds(int $courseId, int $userId): array
    {
        $materialIds = LearningProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('content_type', 'material')
            ->where(function ($query) {
                $query->whereNotNull('completed_at')
                    ->orWhereNotNull('read_at')
                    ->orWhere('is_video_completed', true);
            })
            ->pluck('content_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $assignmentIds = AssignmentSubmission::where('user_id', $userId)
            ->whereHas('assignment', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where('status', '!=', 'draft')
            ->pluck('assignment_id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $quizIds = QuizAttempt::where('user_id', $userId)
            ->whereHas('quiz', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where(function ($query) {
                $query->whereNotNull('submitted_at')
                    ->orWhereIn('status', ['submitted', 'graded']);
            })
            ->pluck('quiz_id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $examIds = ExamAttempt::where('user_id', $userId)
            ->whereHas('exam', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->where(function ($query) {
                $query->whereNotNull('submitted_at')
                    ->orWhereIn('status', ['submitted', 'graded']);
            })
            ->pluck('exam_id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        return [
            'materials' => $materialIds,
            'assignments' => $assignmentIds,
            'quizzes' => $quizIds,
            'exams' => $examIds,
        ];
    }
}
