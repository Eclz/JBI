<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Semester;
use App\Models\FeeRecord;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;

class ValidationService
{
    /**
     * Validate if a student can enroll in a course.
     */
    public function canStudentEnrollInCourse(User $student, Course $course): array
    {
        $errors = [];

        // Check if student is active
        if (!$student->is_active || $student->studentProfile?->status !== 'active') {
            $errors[] = 'Student must be in active status to enroll.';
        }

        // Check course capacity
        if ($course->enrolled_count >= $course->max_students) {
            $errors[] = 'Course has reached maximum enrollment capacity.';
        }

        // Check prerequisites
        if ($course->prerequisites) {
            $completedCourses = $student->enrolledCourses()
                ->wherePivot('status', 'completed')
                ->pluck('courses.id')
                ->toArray();

            $missingPrerequisites = array_diff($course->prerequisites, $completedCourses);
            if (!empty($missingPrerequisites)) {
                $missingCourseNames = Course::whereIn('id', $missingPrerequisites)
                    ->pluck('code')
                    ->implode(', ');
                $errors[] = "Missing prerequisites: {$missingCourseNames}";
            }
        }

        // Check credit load
        $currentCredits = $student->enrolledCourses()
            ->where('semester_id', $course->semester_id)
            ->wherePivot('status', 'enrolled')
            ->sum('credits');

        if (($currentCredits + $course->credits) > 18) {
            $errors[] = 'Enrollment would exceed maximum credit load (18 credits).';
        }

        // Check GPA requirements
        if ($student->studentProfile?->cumulative_gpa < 2.0) {
            $errors[] = 'Student must maintain minimum 2.0 GPA to enroll.';
        }

        // Check registration period
        $semester = $course->semester;
        if ($semester && $semester->registration_end && now() > $semester->registration_end) {
            $errors[] = 'Registration period has ended for this semester.';
        }

        // Check for schedule conflicts
        $conflictingCourses = $this->getScheduleConflicts($student, $course);
        if (!empty($conflictingCourses)) {
            $courseNames = implode(', ', array_column($conflictingCourses, 'code'));
            $errors[] = "Schedule conflicts with: {$courseNames}";
        }

        return [
            'can_enroll' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get schedule conflicts for a student and course.
     */
    public function getScheduleConflicts(User $student, Course $course): array
    {
        $conflicts = [];
        
        if (!$course->schedule) {
            return $conflicts;
        }

        $enrolledCourses = $student->enrolledCourses()
            ->where('semester_id', $course->semester_id)
            ->wherePivot('status', 'enrolled')
            ->get();

        foreach ($enrolledCourses as $enrolledCourse) {
            if ($enrolledCourse->schedule) {
                foreach ($course->schedule as $day => $times) {
                    if (isset($enrolledCourse->schedule[$day])) {
                        $existingTimes = $enrolledCourse->schedule[$day];
                        
                        if ($this->timesOverlap(
                            $times['start'], 
                            $times['end'], 
                            $existingTimes['start'], 
                            $existingTimes['end']
                        )) {
                            $conflicts[] = [
                                'course_id' => $enrolledCourse->id,
                                'code' => $enrolledCourse->code,
                                'day' => $day,
                                'time' => "{$existingTimes['start']}-{$existingTimes['end']}",
                            ];
                        }
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check if two time ranges overlap.
     */
    private function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        return $start1 < $end2 && $end1 > $start2;
    }

    /**
     * Validate if a faculty can teach a course.
     */
    public function canFacultyTeachCourse(User $faculty, Course $course): array
    {
        $errors = [];

        // Check if faculty is active
        if (!$faculty->is_active || $faculty->facultyProfile?->status !== 'active') {
            $errors[] = 'Faculty must be in active status to teach.';
        }

        // Check department match
        if ($faculty->facultyProfile && $course->department_id !== $faculty->facultyProfile->department_id) {
            $errors[] = 'Faculty can only teach courses in their department.';
        }

        // Check schedule conflicts
        $conflictingCourses = $this->getFacultyScheduleConflicts($faculty, $course);
        if (!empty($conflictingCourses)) {
            $courseNames = implode(', ', array_column($conflictingCourses, 'code'));
            $errors[] = "Schedule conflicts with: {$courseNames}";
        }

        // Check teaching load
        $currentLoad = $faculty->teachingCourses()
            ->where('semester_id', $course->semester_id)
            ->where('status', 'active')
            ->count();

        if ($currentLoad >= 6) {
            $errors[] = 'Faculty teaching load limit reached (6 courses per semester).';
        }

        return [
            'can_teach' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get faculty schedule conflicts.
     */
    public function getFacultyScheduleConflicts(User $faculty, Course $course): array
    {
        $conflicts = [];
        
        if (!$course->schedule) {
            return $conflicts;
        }

        $teachingCourses = $faculty->teachingCourses()
            ->where('semester_id', $course->semester_id)
            ->where('status', 'active')
            ->where('id', '!=', $course->id)
            ->get();

        foreach ($teachingCourses as $teachingCourse) {
            if ($teachingCourse->schedule) {
                foreach ($course->schedule as $day => $times) {
                    if (isset($teachingCourse->schedule[$day])) {
                        $existingTimes = $teachingCourse->schedule[$day];
                        
                        if ($this->timesOverlap(
                            $times['start'], 
                            $times['end'], 
                            $existingTimes['start'], 
                            $existingTimes['end']
                        )) {
                            $conflicts[] = [
                                'course_id' => $teachingCourse->id,
                                'code' => $teachingCourse->code,
                                'day' => $day,
                                'time' => "{$existingTimes['start']}-{$existingTimes['end']}",
                            ];
                        }
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Validate student graduation requirements.
     */
    public function validateGraduationRequirements(User $student): array
    {
        $errors = [];
        $profile = $student->studentProfile;

        if (!$profile) {
            return ['can_graduate' => false, 'errors' => ['Student profile not found.']];
        }

        // Check credit requirements
        if ($profile->total_credits_earned < $profile->total_credits_required) {
            $remaining = $profile->total_credits_required - $profile->total_credits_earned;
            $errors[] = "Missing {$remaining} credits for graduation.";
        }

        // Check GPA requirements
        if ($profile->cumulative_gpa < 2.0) {
            $errors[] = 'Minimum 2.0 GPA required for graduation.';
        }

        // Check outstanding fees
        $outstandingFees = FeeRecord::where('user_id', $student->id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum('balance_amount');

        if ($outstandingFees > 0) {
            $errors[] = "Outstanding fees: \${$outstandingFees}";
        }

        // Check core course requirements (example)
        $coreRequirements = $this->getCoreRequirements($profile->program);
        $completedCourses = $student->enrolledCourses()
            ->wherePivot('status', 'completed')
            ->pluck('courses.code')
            ->toArray();

        $missingCores = array_diff($coreRequirements, $completedCourses);
        if (!empty($missingCores)) {
            $errors[] = 'Missing core courses: ' . implode(', ', $missingCores);
        }

        return [
            'can_graduate' => empty($errors),
            'errors' => $errors,
            'requirements_met' => [
                'credits' => $profile->total_credits_earned >= $profile->total_credits_required,
                'gpa' => $profile->cumulative_gpa >= 2.0,
                'fees' => $outstandingFees == 0,
                'core_courses' => empty($missingCores),
            ],
        ];
    }

    /**
     * Get core course requirements for a program.
     */
    private function getCoreRequirements(string $program): array
    {
        $requirements = [
            'Bachelor of Arts in Biblical Studies' => [
                'BIBL101', 'BIBL201', 'THEO101', 'THEO201', 'HIST101'
            ],
            'Master of Divinity' => [
                'BIBL501', 'THEO501', 'MINI501', 'HIST501'
            ],
            // Add more program requirements as needed
        ];

        return $requirements[$program] ?? [];
    }

    /**
     * Validate assignment submission eligibility.
     */
    public function canSubmitAssignment(User $student, Assignment $assignment): array
    {
        $errors = [];

        // Check if student is enrolled in the course
        $enrollment = $student->enrolledCourses()
            ->where('course_id', $assignment->course_id)
            ->wherePivot('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            $errors[] = 'Student is not enrolled in this course.';
        }

        // Check if assignment is published
        if (!$assignment->is_published) {
            $errors[] = 'Assignment is not yet published.';
        }

        // Check availability window
        if ($assignment->available_from && now() < $assignment->available_from) {
            $errors[] = 'Assignment is not yet available.';
        }

        if ($assignment->available_until && now() > $assignment->available_until) {
            $errors[] = 'Assignment submission period has ended.';
        }

        // Check if already submitted (unless multiple attempts allowed)
        $existingSubmission = $assignment->submissions()
            ->where('user_id', $student->id)
            ->first();

        if ($existingSubmission && !($assignment->settings['attempts_allowed'] ?? false)) {
            $errors[] = 'Assignment has already been submitted.';
        }

        // Check late submission policy
        $isLate = now() > $assignment->due_date;
        if ($isLate && !$assignment->allow_late_submission) {
            $errors[] = 'Late submissions are not allowed for this assignment.';
        }

        return [
            'can_submit' => empty($errors),
            'errors' => $errors,
            'is_late' => $isLate,
        ];
    }
}
