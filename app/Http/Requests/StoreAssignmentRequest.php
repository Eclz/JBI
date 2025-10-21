<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Assignment;
use App\Models\Course;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Assignment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')->where('status', 'active'),
            ],
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'instructions' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['homework', 'quiz', 'exam', 'project', 'essay', 'presentation']),
            ],
            'max_points' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
            'weight_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'decimal:0,2',
            ],
            'due_date' => [
                'required',
                'date',
                'after:now',
                'before:' . now()->addYear()->toDateString(),
            ],
            'available_from' => [
                'nullable',
                'date',
                'before_or_equal:due_date',
                'after_or_equal:now',
            ],
            'available_until' => [
                'nullable',
                'date',
                'after_or_equal:due_date',
            ],
            'allow_late_submission' => [
                'boolean',
            ],
            'late_penalty_per_day' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
                'required_if:allow_late_submission,true',
            ],
            'allowed_file_types' => [
                'nullable',
                'array',
                'min:1',
            ],
            'allowed_file_types.*' => [
                'string',
                Rule::in(['pdf', 'doc', 'docx', 'txt', 'rtf', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif']),
            ],
            'max_file_size' => [
                'nullable',
                'integer',
                'min:1024', // 1MB minimum
                'max:102400', // 100MB maximum
            ],
            'is_published' => [
                'boolean',
            ],
            'rubric' => [
                'nullable',
                'string',
                'max:3000',
            ],
            'settings' => [
                'nullable',
                'array',
            ],
            'settings.auto_grade' => [
                'nullable',
                'boolean',
            ],
            'settings.show_correct_answers' => [
                'nullable',
                'boolean',
            ],
            'settings.randomize_questions' => [
                'nullable',
                'boolean',
            ],
            'settings.time_limit' => [
                'nullable',
                'integer',
                'min:5',
                'max:480', // 8 hours maximum
            ],
            'settings.attempts_allowed' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_id.exists' => 'Selected course must be active.',
            'due_date.after' => 'Due date must be in the future.',
            'due_date.before' => 'Due date cannot be more than one year from now.',
            'available_from.before_or_equal' => 'Available from date must be before or equal to due date.',
            'available_until.after_or_equal' => 'Available until date must be after or equal to due date.',
            'late_penalty_per_day.required_if' => 'Late penalty is required when late submissions are allowed.',
            'max_file_size.min' => 'Maximum file size must be at least 1MB.',
            'max_file_size.max' => 'Maximum file size cannot exceed 100MB.',
            'settings.time_limit.min' => 'Time limit must be at least 5 minutes.',
            'settings.time_limit.max' => 'Time limit cannot exceed 8 hours.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate course ownership for faculty
            if ($this->course_id && $this->user()->role === 'faculty') {
                $course = Course::find($this->course_id);
                if ($course && $course->instructor_id !== $this->user()->id) {
                    $validator->errors()->add('course_id', 'You can only create assignments for courses you teach.');
                }
            }

            // Validate weight percentage doesn't exceed course total
            if ($this->course_id && $this->weight_percentage) {
                $this->validateWeightPercentage($validator);
            }

            // Validate assignment type specific rules
            if ($this->type) {
                $this->validateTypeSpecificRules($validator);
            }

            // Validate file settings
            if ($this->allowed_file_types && !$this->max_file_size) {
                $validator->errors()->add('max_file_size', 'Maximum file size is required when file types are specified.');
            }
        });
    }

    /**
     * Validate weight percentage doesn't exceed course limits.
     */
    private function validateWeightPercentage($validator): void
    {
        $course = Course::find($this->course_id);
        if ($course) {
            $totalWeight = $course->assignments()
                ->where('is_published', true)
                ->sum('weight_percentage');
            
            if (($totalWeight + $this->weight_percentage) > 100) {
                $remaining = 100 - $totalWeight;
                $validator->errors()->add(
                    'weight_percentage',
                    "Weight percentage cannot exceed {$remaining}%. Current total: {$totalWeight}%"
                );
            }
        }
    }

    /**
     * Validate type-specific rules.
     */
    private function validateTypeSpecificRules($validator): void
    {
        switch ($this->type) {
            case 'quiz':
                if ($this->max_points > 100) {
                    $validator->errors()->add('max_points', 'Quiz maximum points should not exceed 100.');
                }
                break;
                
            case 'exam':
                if ($this->max_points < 100) {
                    $validator->errors()->add('max_points', 'Exam should have at least 100 points.');
                }
                if (!isset($this->settings['time_limit'])) {
                    $validator->errors()->add('settings.time_limit', 'Time limit is required for exams.');
                }
                break;
                
            case 'project':
                if ($this->weight_percentage < 10) {
                    $validator->errors()->add('weight_percentage', 'Projects should have at least 10% weight.');
                }
                break;
                
            case 'essay':
                if (!$this->allowed_file_types || !in_array('pdf', $this->allowed_file_types)) {
                    $validator->errors()->add('allowed_file_types', 'Essays should allow PDF submissions.');
                }
                break;
        }
    }
}
