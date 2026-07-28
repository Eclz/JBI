<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\FeeRecord;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $currentSemester = Semester::where('is_current', true)->first();
        $registrationOpen = $currentSemester?->is_registration_open ?? false;

        $enrollments = CourseEnrollment::where('user_id', Auth::id())
            ->with(['course.semester', 'course.instructor', 'course.department'])
            ->where('status', 'enrolled')
            ->orderBy('enrollment_date', 'desc')
            ->paginate(20);

        return view('student.courses.index', compact('enrollments', 'currentSemester', 'registrationOpen'));
    }

    public function show(Course $course)
    {
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $course->load([
            'semester',
            'instructor',
            'department',
            'assignments' => function ($query) {
                $query->orderBy('due_date', 'asc');
            }
        ]);

        // Get student's grades for this course
        $grades = $course->grades()->where('user_id', Auth::id())->get();

        // Get student's attendance for this course
        $attendanceRecords = $course->attendance()->where('user_id', Auth::id())->get();
        // $totalClasses = $course->attendance()->distinct('date')->count();
        $totalClasses = 0;
        $attendedClasses = $attendanceRecords->where('status', 'present')->count();
        $attendancePercentage = $totalClasses > 0 ? round(($attendedClasses / $totalClasses) * 100, 2) : 0;

        $progress = $this->calculateCourseProgress($course, Auth::id());

        return view('student.courses.show', compact(
            'course',
            'enrollment',
            'grades',
            'attendanceRecords',
            'attendancePercentage',
            'progress'
        ));
    }

    public function materials(Course $course)
    {
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->firstOrFail();

        $materials = $course->materials()
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.courses.materials', compact('course', 'materials'));
    }

    public function enroll(Request $request, Course $course)
    {
        $student = Auth::user();
        $profile = $student->studentProfile;
        $currentSemester = Semester::where('is_current', true)->first();

        if (!$profile) {
            return back()->withErrors(['error' => 'Your student profile is incomplete. Please contact the administration office.']);
        }

        if (!$currentSemester) {
            return back()->withErrors(['error' => 'No active semester is available for enrollment.']);
        }

        if (!$currentSemester->is_registration_open) {
            return back()->withErrors(['error' => 'Course registration is currently closed.']);
        }

        if ($course->semester_id !== $currentSemester->id) {
            return back()->withErrors(['error' => 'You can only enroll in courses for the current semester.']);
        }

        if (!$this->matchesProgramAndYear($course, $profile)) {
            return back()->withErrors(['error' => 'This course does not match your program or year of study.']);
        }

        $eligibilityError = $this->checkEnrollmentEligibility($student, $profile, $currentSemester);
        if ($eligibilityError) {
            return back()->withErrors(['error' => $eligibilityError]);
        }

        // Check if student is already enrolled
        if ($course->enrollments()->where('user_id', Auth::id())->where('status', '!=', 'dropped')->exists()) {
            return back()->withErrors(['error' => 'You are already enrolled in this course.']);
        }

        // Check course capacity
        if ($course->capacity && $course->enrollments()->where('status', 'enrolled')->count() >= $course->capacity) {
            return back()->withErrors(['error' => 'This course is at full capacity.']);
        }

        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => now(),
            'status' => 'enrolled',
        ]);

        return back()->with('success', 'Successfully enrolled in the course.');
    }

    private function checkEnrollmentEligibility($student, $profile, $currentSemester): ?string
    {
        if (!$profile->registration_fee_paid_at) {
            return 'Please pay the registration fee before enrolling in courses.';
        }

        $minPercent = (float) SystemSetting::getSetting('tuition_min_percent', 0);
        if ($minPercent <= 0) {
            return null;
        }

        $tuitionRecords = FeeRecord::where('user_id', $student->id)
            ->whereHas('feeStructure', function ($query) use ($currentSemester) {
                $query->where('type', 'tuition')
                    ->where('semester_id', $currentSemester->id);
            })
            ->get();

        $total = $tuitionRecords->sum('total_amount');
        $paid = $tuitionRecords->sum('paid_amount');
        $percent = $total > 0 ? round(($paid / $total) * 100, 2) : 100;

        $deadline = $profile->tuition_deadline_at;
        if (!$deadline && $profile->registration_fee_paid_at) {
            $days = (int) SystemSetting::getSetting('tuition_payment_days', 30);
            $deadline = $profile->registration_fee_paid_at->copy()->addDays($days);
        }

        if ($deadline && now()->greaterThan($deadline) && $percent < $minPercent) {
            $student->update(['is_active' => false]);
            $profile->update(['status' => 'inactive']);
            return 'You missed the tuition payment deadline and your account has been deactivated. Please contact administration.';
        }

        if ($currentSemester->start_date && now()->greaterThanOrEqualTo($currentSemester->start_date) && $percent < $minPercent) {
            return 'You must pay at least ' . $minPercent . '% of tuition to enroll for the current semester.';
        }

        if ($percent < $minPercent) {
            return 'You must pay at least ' . $minPercent . '% of tuition to enroll.';
        }

        return null;
    }

    public function available()
    {
        $student = Auth::user();
        $profile = $student->studentProfile;
        $currentSemester = Semester::where('is_current', true)->first();
        $registrationOpen = $currentSemester?->is_registration_open ?? false;

        $availableCourses = collect();

        if ($currentSemester && $profile) {
            $enrolledCourseIds = $student->courseEnrollments()
                ->where('status', '!=', 'dropped')
                ->pluck('course_id');

            $availableCourses = Course::with(['semester', 'department', 'instructor', 'program'])
                ->where('status', Course::STATUS_ACTIVE)
                ->where('semester_id', $currentSemester->id)
                ->when($profile, function ($query) use ($profile) {
                    if ($profile->program_id) {
                        $query->where(function ($subQuery) use ($profile) {
                            $subQuery->where('program_id', $profile->program_id)
                                ->orWhere(function ($fallbackQuery) use ($profile) {
                                    $fallbackQuery->whereNull('program_id')
                                        ->where('department_id', $profile->department_id);
                                });
                        });
                    } else {
                        $query->where('department_id', $profile->department_id);
                    }

                    if ($profile->year_of_study) {
                        $query->where(function ($yearQuery) use ($profile) {
                            $yearQuery->whereNull('year_of_study')
                                ->orWhere('year_of_study', $profile->year_of_study);
                        });
                    }
                })
                ->whereNotIn('id', $enrolledCourseIds)
                ->orderBy('name')
                ->get();
        }

        return view('student.courses.enroll', compact(
            'availableCourses',
            'currentSemester',
            'registrationOpen',
            'profile'
        ));
    }

    private function matchesProgramAndYear(Course $course, StudentProfile $profile): bool
    {
        if ($course->program_id) {
            if ($profile->program_id && $course->program_id !== $profile->program_id) {
                return false;
            }
        } else {
            if ($course->department_id !== $profile->department_id) {
                return false;
            }
        }

        if ($course->year_of_study && $profile->year_of_study && $course->year_of_study !== $profile->year_of_study) {
            return false;
        }

        return true;
    }

    private function calculateCourseProgress(Course $course, int $studentId): array
    {
        $assignmentPercent = $course->grades()
            ->published()
            ->where('user_id', $studentId)
            ->avg('percentage');

        $quizAttempts = \App\Models\QuizAttempt::where('user_id', $studentId)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->get();

        $quizPercent = $quizAttempts
            ->groupBy('quiz_id')
            ->map(function ($attempts) {
                return $attempts->max('percentage');
            })
            ->avg();

        $courseworkParts = array_filter([$assignmentPercent, $quizPercent], function ($value) {
            return $value !== null;
        });

        $courseworkPercent = count($courseworkParts) > 0
            ? round(array_sum($courseworkParts) / count($courseworkParts), 2)
            : 0;

        $examAttempts = \App\Models\ExamAttempt::where('user_id', $studentId)
            ->whereNotNull('marks_obtained')
            ->whereHas('exam', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with('exam')
            ->get();

        $examPercent = $examAttempts->map(function ($attempt) {
            $totalMarks = $attempt->exam?->total_marks ?: 0;
            if ($totalMarks <= 0) {
                return 0;
            }
            return ($attempt->marks_obtained / $totalMarks) * 100;
        })->max() ?? 0;

        $overall = round(($courseworkPercent * 0.4) + ($examPercent * 0.6), 2);

        return [
            'coursework' => round($courseworkPercent, 2),
            'exam' => round($examPercent, 2),
            'overall' => $overall,
        ];
    }

    public function unenroll(Course $course)
    {
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return back()->withErrors(['error' => 'You are not enrolled in this course.']);
        }

        $enrollment->update(['status' => 'dropped']);

        return back()->with('success', 'Successfully unenrolled from the course.');
    }
}
