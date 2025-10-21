<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Assignment;

class UpdateAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('assignment'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $assignment = $this->route('assignment');

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'sometimes',
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
                'sometimes',
                'required',
                'string',
                Rule::in(['homework', 'quiz', 'exam', 'project', 'essay', 'presentation']),
            ],
            'max_points' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
            'weight_percentage' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:100',
                'decimal:0,2',
            ],
            'due_date' => [
                'sometimes',
                'required',
                'date',
            ],
            'available_from' => [
                'nullable',
                'date',
                'before_or_equal:due_date',
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
            ],
            'allowed_file_types' => [
                'nullable',
                'array',
            ],
            'max_file_size' => [
                'nullable',
                'integer',
                'min:1024',
                'max:102400',
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
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assignment = $this->route('assignment');
            
            // Prevent changing due date if submissions exist
            if ($this->due_date && $assignment->submissions()->exists()) {
                $originalDueDate = $assignment->due_date;
                $newDueDate = $this->due_date;
                
                if ($newDueDate < $originalDueDate) {
                    $validator->errors()->add(
                        'due_date',
                        'Cannot move due date earlier when submissions already exist.'
                    );
                }
            }

            // Prevent reducing max_points if graded submissions exist
            if ($this->max_points && $assignment->gradedSubmissions()->exists()) {
                $maxSubmissionScore = $assignment->submissions()->max('score');
                if ($this->max_points < $maxSubmissionScore) {
                    $validator->errors()->add(
                        'max_points',
                        "Cannot reduce maximum points below highest submission score ({$maxSubmissionScore})."
                    );
                }
            }

            // Validate publishing restrictions
            if ($this->is_published === false && $assignment->submissions()->exists()) {
                $validator->errors()->add(
                    'is_published',
                    'Cannot unpublish assignment that has submissions.'
                );
            }
        });
    }
}
