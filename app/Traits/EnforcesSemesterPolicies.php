<?php

namespace App\Traits;

use App\Models\Course;
use App\Models\Semester;

trait EnforcesSemesterPolicies
{
    /**
     * Prevent action if the course belongs to a past semester.
     *
     * @param Course $course
     * @return void
     */
    protected function abortIfPastSemester(Course $course): void
    {
        // Find if there's any current semester
        $currentSemester = Semester::where('is_current', true)->first();

        // If no current semester is defined, we might allow it or block it. 
        // We assume we allow or just check if the course's semester is not active.
        if ($course->semester && !$course->semester->is_active) {
            abort(403, 'This action is not allowed because the semester for this course is closed or in the past.');
        }

        if ($currentSemester && $course->semester_id !== $currentSemester->id) {
            abort(403, 'This action is restricted to the current active semester.');
        }
    }

    /**
     * Prevent enrollment if we are past the enrollment deadline.
     *
     * @param Semester $semester
     * @return void
     */
    protected function abortIfPastEnrollmentDeadline(Semester $semester): void
    {
        if ($semester->registration_end && now()->gt($semester->registration_end)) {
            abort(403, 'The enrollment deadline for this semester has passed.');
        }
    }
}
