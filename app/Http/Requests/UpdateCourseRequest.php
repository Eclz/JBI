<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Course;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('course'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'min:6',
                'max:10',
                'regex:/^[A-Z]{3,4}\d{3}[A-Z]?$/',
                Rule::unique('courses', 'code')->ignore($course->id),
            ],
            'name' => [
                'sometimes',
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
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:6',
            ],
            'department_id' => [
                'sometimes',
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
                'sometimes',
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
            'room' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\.]+$/',
            ],
            'max_students' => [
                'sometimes',
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
                'max:10240',
            ],
            'prerequisites' => [
                'nullable',
                'array',
            ],
            'fee_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:50000',
                'decimal:0,2',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $course = $this->route('course');
            
            // Prevent reducing max_students below current enrollment
            if ($this->max_students && $course->enrolled_count > $this->max_students) {
                $validator->errors()->add(
                    'max_students',
                    "Cannot reduce maximum students below current enrollment ({$course->enrolled_count})."
                );
            }

            // Validate status changes
            if ($this->status && $this->status !== $course->status) {
                $this->validateStatusChange($validator, $course);
            }
        });
    }

    /**
     * Validate course status changes.
     */
    private function validateStatusChange($validator, $course): void
    {
        $validTransitions = [
            'active' => ['inactive', 'completed', 'cancelled'],
            'inactive' => ['active', 'cancelled'],
            'completed' => [], // Cannot change from completed
            'cancelled' => [], // Cannot change from cancelled
        ];

        $currentStatus = $course->status;
        $newStatus = $this->status;

        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            $validator->errors()->add(
                'status',
                "Cannot change course status from {$currentStatus} to {$newStatus}."
            );
        }

        // Additional validation for completing courses
        if ($newStatus === 'completed' && $course->enrollments()->where('status', 'enrolled')->exists()) {
            $validator->errors()->add(
                'status',
                'Cannot complete course while students are still enrolled.'
            );
        }
    }
}
