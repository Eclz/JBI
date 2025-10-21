<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\User;

class StoreCourseEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CourseEnrollment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'student')->where('is_active', true),
            ],
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')->where('status', 'active'),
            ],
            'enrollment_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:' . now()->subYear()->toDateString(),
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['enrolled', 'dropped', 'completed', 'failed']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'Selected user must be an active student.',
            'course_id.exists' => 'Selected course must be active.',
            'enrollment_date.after' => 'Enrollment date cannot be more than one year ago.',
            'enrollment_date.before_or_equal' => 'Enrollment date cannot be in the future.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check for duplicate enrollment
            if ($this->user_id && $this->course_id) {
                $existingEnrollment = CourseEnrollment::where('user_id', $this->user_id)
                    ->where('course_id', $this->course_id)
                    ->whereIn('status', ['enrolled', 'completed'])
                    ->first();

                if ($existingEnrollment) {
                    $validator->errors()->add(
                        'course_id',
                        'Student is already enrolled in this course.'
                    );
                }
            }

            // Check course capacity
            if ($this->course_id) {
                $course = Course::find($this->course_id);
                if ($course && $course->enrolled_count >= $course->max_students) {
                    $validator->errors()->add(
                        'course_id',
                        'Course has reached maximum enrollment capacity.'
                    );
                }
            }

            // Validate prerequisites
            if ($this->user_id && $this->course_id) {
                $this->validatePrerequisites($validator);
            }

            // Validate semester registration period
            if ($this->course_id && $this->enrollment_date) {
                $this->validateRegistrationPeriod($validator);
            }

            // Validate student academic standing
            if ($this->user_id) {
                $this->validateAcademicStanding($validator);
            }
        });
    }

    /**
     * Validate course prerequisites.
     */
    private function validatePrerequisites($validator): void
    {
        $course = Course::find($this->course_id);
        $student = User::find($this->user_id);

        if ($course && $student && $course->prerequisites) {
            $completedCourses = $student->enrolledCourses()
                ->wherePivot('status', 'completed')
                ->pluck('courses.id')
                ->toArray();

            $missingPrerequisites = array_diff($course->prerequisites, $completedCourses);

            if (!empty($missingPrerequisites)) {
                $missingCourseNames = Course::whereIn('id', $missingPrerequisites)
                    ->pluck('code')
                    ->implode(', ');

                $validator->errors()->add(
                    'course_id',
                    "Student must complete prerequisites: {$missingCourseNames}"
                );
            }
        }
    }

    /**
     * Validate registration period.
     */
    private function validateRegistrationPeriod($validator): void
    {
        $course = Course::find($this->course_id);
        
        if ($course && $course->semester) {
            $semester = $course->semester;
            $enrollmentDate = $this->enrollment_date;

            if ($semester->registration_start && $enrollmentDate < $semester->registration_start) {
                $validator->errors()->add(
                    'enrollment_date',
                    "Registration for this semester starts on {$semester->registration_start->format('Y-m-d')}"
                );
            }

            if ($semester->registration_end && $enrollmentDate > $semester->registration_end) {
                $validator->errors()->add(
                    'enrollment_date',
                    "Registration for this semester ended on {$semester->registration_end->format('Y-m-d')}"
                );
            }
        }
    }

    /**
     * Validate student academic standing.
     */
    private function validateAcademicStanding($validator): void
    {
        $student = User::find($this->user_id);
        
        if ($student && $student->studentProfile) {
            $profile = $student->studentProfile;

            // Check if student is in good standing
            if ($profile->status !== 'active') {
                $validator->errors()->add(
                    'user_id',
                    "Student status is {$profile->status}. Only active students can enroll."
                );
            }

            // Check GPA requirements
            if ($profile->cumulative_gpa && $profile->cumulative_gpa < 2.0) {
                $validator->errors()->add(
                    'user_id',
                    'Student must maintain a minimum 2.0 GPA to enroll in courses.'
                );
            }

            // Check credit load limits
            $currentSemesterCredits = $student->enrolledCourses()
                ->where('semester_id', Course::find($this->course_id)?->semester_id)
                ->wherePivot('status', 'enrolled')
                ->sum('credits');

            $courseCredits = Course::find($this->course_id)?->credits ?? 0;
            $totalCredits = $currentSemesterCredits + $courseCredits;

            if ($totalCredits > 18) {
                $validator->errors()->add(
                    'course_id',
                    "Enrollment would exceed maximum credit load (18 credits). Current: {$currentSemesterCredits}, Course: {$courseCredits}"
                );
            }
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default enrollment date to today if not provided
        if (!$this->enrollment_date) {
            $this->merge([
                'enrollment_date' => now()->toDateString(),
            ]);
        }

        // Set default status
        if (!$this->status) {
            $this->merge([
                'status' => 'enrolled',
            ]);
        }
    }
}
