<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Course::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'min:6',
                'max:10',
                'regex:/^[A-Z]{3,4}\d{3}[A-Z]?$/',
                'unique:courses,code',
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'credits' => [
                'required',
                'integer',
                'min:1',
                'max:6',
            ],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where('is_active', true),
            ],
            'program' => [
                'nullable',
                'string',
                'max:255',
            ],
            'program_id' => [
                'nullable',
                'integer',
                Rule::exists('programs', 'id')->where('is_active', true),
            ],
            'instructor_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'faculty')->where('is_active', true),
            ],
            'semester_id' => [
                'required',
                'integer',
                Rule::exists('semesters', 'id')->where('is_active', true),
            ],
            'year_of_study' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
            ],
            'schedule' => [
                'nullable',
                'array',
            ],
            'schedule.*' => [
                'array',
            ],
            'schedule.*.start' => [
                'required_with:schedule.*',
                'date_format:H:i',
                'before:schedule.*.end',
            ],
            'schedule.*.end' => [
                'required_with:schedule.*',
                'date_format:H:i',
                'after:schedule.*.start',
            ],
            'room' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\.]+$/',
            ],
            'max_students' => [
                'required',
                'integer',
                'min:5',
                'max:100',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'inactive', 'completed', 'cancelled']),
            ],
            'syllabus_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240', // 10MB
            ],
            'prerequisites' => [
                'nullable',
                'array',
            ],
            'prerequisites.*' => [
                'integer',
                Rule::exists('courses', 'id'),
            ],
            'fee_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:50000',
                'decimal:0,2',
            ],
            'learning_objectives' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'assessment_methods' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.regex' => 'Course code must be in format: 3-4 letters followed by 3 digits (e.g., BIBL101, THEO301A).',
            'schedule.*.start.before' => 'Class start time must be before end time.',
            'schedule.*.end.after' => 'Class end time must be after start time.',
            'room.regex' => 'Room name may only contain letters, numbers, spaces, hyphens, and dots.',
            'instructor_id.exists' => 'Selected instructor must be an active faculty member.',
            'department_id.exists' => 'Selected department must be active.',
            'semester_id.exists' => 'Selected semester must be active.',
            'syllabus_file.max' => 'Syllabus file must not exceed 10MB.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate course code matches department
            if ($this->code && $this->department_id) {
                $department = Department::find($this->department_id);
                if ($department) {
                    $codePrefix = substr($this->code, 0, strlen($department->code));
                    if (strtoupper($codePrefix) !== strtoupper($department->code)) {
                        $validator->errors()->add('code', "Course code must start with department code: {$department->code}");
                    }
                }
            }

            // Validate schedule doesn't conflict with instructor's other courses
            if ($this->instructor_id && $this->semester_id && $this->schedule) {
                $this->validateScheduleConflicts($validator);
            }

            // Validate room capacity if room is specified
            if ($this->room && $this->max_students) {
                $this->validateRoomCapacity($validator);
            }
        });
    }

    /**
     * Validate schedule conflicts with instructor's other courses.
     */
    private function validateScheduleConflicts($validator): void
    {
        $conflictingCourses = Course::where('instructor_id', $this->instructor_id)
            ->where('semester_id', $this->semester_id)
            ->where('status', 'active')
            ->get();

        foreach ($conflictingCourses as $course) {
            if ($course->schedule) {
                foreach ($this->schedule as $day => $times) {
                    if (isset($course->schedule[$day])) {
                        $existingStart = $course->schedule[$day]['start'];
                        $existingEnd = $course->schedule[$day]['end'];
                        $newStart = $times['start'];
                        $newEnd = $times['end'];

                        if ($this->timesOverlap($newStart, $newEnd, $existingStart, $existingEnd)) {
                            $validator->errors()->add(
                                "schedule.{$day}",
                                "Schedule conflicts with {$course->code} on {$day} ({$existingStart}-{$existingEnd})"
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if two time ranges overlap.
     */
    private function timesOverlap($start1, $end1, $start2, $end2): bool
    {
        return $start1 < $end2 && $end1 > $start2;
    }

    /**
     * Validate room capacity (placeholder for room management system).
     */
    private function validateRoomCapacity($validator): void
    {
        // This would integrate with a room management system
        // For now, we'll do basic validation
        $knownRooms = [
            'Johnson Hall 101' => 30,
            'Johnson Hall 102' => 25,
            'Academic Building A-101' => 40,
            'Chapel' => 200,
        ];

        if (isset($knownRooms[$this->room])) {
            $capacity = $knownRooms[$this->room];
            if ($this->max_students > $capacity) {
                $validator->errors()->add('max_students', "Room {$this->room} has a capacity of {$capacity} students.");
            }
        }
    }
}
