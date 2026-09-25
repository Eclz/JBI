<?php

namespace App\Http\Controllers\Faculty;

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
use Illuminate\Support\Facades\Auth;

class LmsController extends Controller
{
    public function index()
    {
        $faculty = Auth::user();
        $facultyId = $faculty->id;
        
        // --- Dashboard Data ---
        $courseIds = Course::where('instructor_id', $facultyId)->pluck('id');
        
        $totalCourses = $courseIds->count();
        
        $totalStudents = CourseEnrollment::whereIn('course_id', $courseIds)
            ->where('status', 'enrolled')
            ->distinct('user_id')
            ->count('user_id');
            
        $pendingAssignments = AssignmentSubmission::whereHas('assignment', function ($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            })
            ->where(function ($query) {
                $query->where('status', 'submitted')->orWhereNotNull('submitted_at');
            })
            ->whereNull('score')
            ->count();
            
        $todayAttendance = \App\Models\Attendance::whereHas('course', function ($query) use ($facultyId) {
                $query->where('instructor_id', $facultyId);
            })
            ->whereDate('attendance_date', today())
            ->count();
            
        $upcomingAssignments = Assignment::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->with('course')
            ->orderBy('due_date')
            ->limit(5)
            ->get();
            
        $recentSubmissions = AssignmentSubmission::whereHas('assignment', function ($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            })
            ->where(function ($query) {
                $query->where('status', 'submitted')->orWhereNotNull('submitted_at');
            })
            ->with(['assignment', 'student'])
            ->orderBy('submitted_at', 'desc')
            ->limit(10)
            ->get();

        // --- LMS Analytics Data ---
        $courses = Course::with(['semester', 'department'])
            ->where('instructor_id', $facultyId)
            ->orderBy('name')
            ->get()
            ->map(function ($course) {
                $enrolledStudentIds = CourseEnrollment::where('course_id', $course->id)
                    ->where('status', 'enrolled')
                    ->pluck('user_id');

                $studentsCount = $enrolledStudentIds->count();
                $averageProgress = 0;

                if ($studentsCount > 0) {
                    $sum = 0;
                    foreach ($enrolledStudentIds as $studentId) {
                        $sum += $this->courseProgressPercent($course->id, (int) $studentId);
                    }
                    $averageProgress = round($sum / $studentsCount, 1);
                }

                return [
                    'course' => $course,
                    'students_count' => $studentsCount,
                    'average_progress' => $averageProgress,
                ];
            });

        return view('faculty.lms.index', compact(
            'courses',
            'totalCourses',
            'totalStudents',
            'pendingAssignments',
            'upcomingAssignments',
            'recentSubmissions',
            'todayAttendance'
        ));
    }

    public function show(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $course->load(['semester', 'department']);
        $publishedMaterials = CourseMaterial::where('course_id', $course->id)
            ->where('is_published', true)
            ->get(['id', 'type']);
        $materialIds = $publishedMaterials->pluck('id');
        $videoMaterialIds = $publishedMaterials->where('type', 'video')->pluck('id');
        $noteMaterialIds = $publishedMaterials->where('type', '!=', 'video')->pluck('id');

        $enrollments = CourseEnrollment::with('student')
            ->where('course_id', $course->id)
            ->where('status', 'enrolled')
            ->orderByDesc('enrollment_date')
            ->get();

        $students = $enrollments->map(function ($enrollment) use ($course, $materialIds, $videoMaterialIds, $noteMaterialIds) {
            $studentId = (int) $enrollment->user_id;
            $materialProgress = LearningProgress::where('course_id', $course->id)
                ->where('user_id', $studentId)
                ->where('content_type', 'material')
                ->when($materialIds->isNotEmpty(), function ($query) use ($materialIds) {
                    $query->whereIn('content_id', $materialIds);
                })
                ->get();

            $notesRead = $materialProgress
                ->whereIn('content_id', $noteMaterialIds)
                ->filter(function ($row) {
                    return !is_null($row->read_at) || !is_null($row->completed_at);
                })
                ->count();

            $videosWatched = $materialProgress
                ->whereIn('content_id', $videoMaterialIds)
                ->filter(function ($row) {
                    return (bool) $row->is_video_completed || !is_null($row->completed_at);
                })
                ->count();

            return [
                'student' => $enrollment->student,
                'progress' => $this->courseProgress($course->id, $studentId),
                'last_activity' => LearningProgress::where('course_id', $course->id)
                    ->where('user_id', $studentId)
                    ->max('last_accessed_at'),
                'material_stats' => [
                    'notes_read' => $notesRead,
                    'notes_total' => $noteMaterialIds->count(),
                    'videos_watched' => $videosWatched,
                    'videos_total' => $videoMaterialIds->count(),
                ],
            ];
        });

        return view('faculty.lms.course', compact('course', 'students'));
    }

    private function courseProgressPercent(int $courseId, int $userId): float
    {
        return $this->courseProgress($courseId, $userId)['percent'];
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
        ];
    }
}
