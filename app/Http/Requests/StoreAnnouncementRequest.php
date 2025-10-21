<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Announcement;

class StoreAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Announcement::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],
            'content' => [
                'required',
                'string',
                'min:20',
                'max:10000',
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['general', 'academic', 'administrative', 'emergency', 'event']),
            ],
            'priority' => [
                'required',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],
            'course_id' => [
                'nullable',
                'integer',
                Rule::exists('courses', 'id')->where('status', 'active'),
                'required_if:type,academic',
            ],
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where('is_active', true),
            ],
            'target_roles' => [
                'nullable',
                'array',
                'min:1',
            ],
            'target_roles.*' => [
                'string',
                Rule::in(['student', 'faculty', 'admin', 'parent']),
            ],
            'is_published' => [
                'boolean',
            ],
            'send_email' => [
                'boolean',
            ],
            'send_sms' => [
                'boolean',
            ],
            'published_at' => [
                'nullable',
                'date',
                'after_or_equal:now',
                'required_if:is_published,true',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:published_at',
                'before:' . now()->addYear()->toDateString(),
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:5',
            ],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,gif',
                'max:5120', // 5MB
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'Announcement title must be at least 5 characters.',
            'content.min' => 'Announcement content must be at least 20 characters.',
            'course_id.required_if' => 'Course is required for academic announcements.',
            'published_at.required_if' => 'Published date is required when publishing announcement.',
            'expires_at.after' => 'Expiration date must be after publication date.',
            'target_roles.min' => 'At least one target role must be selected.',
            'attachments.max' => 'Maximum 5 attachments allowed.',
            'attachments.*.max' => 'Each attachment must not exceed 5MB.',
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
                $course = \App\Models\Course::find($this->course_id);
                if ($course && $course->instructor_id !== $this->user()->id) {
                    $validator->errors()->add(
                        'course_id',
                        'You can only create announcements for courses you teach.'
                    );
                }
            }

            // Validate emergency announcements
            if ($this->type === 'emergency') {
                if ($this->priority !== 'urgent') {
                    $validator->errors()->add(
                        'priority',
                        'Emergency announcements must have urgent priority.'
                    );
                }
                
                if (!$this->send_email) {
                    $validator->errors()->add(
                        'send_email',
                        'Emergency announcements should be sent via email.'
                    );
                }
            }

            // Validate target roles for course announcements
            if ($this->course_id && $this->target_roles) {
                if (!in_array('student', $this->target_roles)) {
                    $validator->errors()->add(
                        'target_roles',
                        'Course announcements should target students.'
                    );
                }
            }

            // Validate content for urgent announcements
            if ($this->priority === 'urgent' && strlen($this->content) < 50) {
                $validator->errors()->add(
                    'content',
                    'Urgent announcements should have detailed content (minimum 50 characters).'
                );
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default published_at if publishing
        if ($this->is_published && !$this->published_at) {
            $this->merge([
                'published_at' => now(),
            ]);
        }

        // Set default target roles if not specified
        if (!$this->target_roles) {
            $defaultRoles = match($this->type) {
                'academic' => ['student', 'faculty'],
                'administrative' => ['student', 'faculty', 'admin'],
                'emergency' => ['student', 'faculty', 'admin', 'parent'],
                default => ['student'],
            };
            
            $this->merge([
                'target_roles' => $defaultRoles,
            ]);
        }
    }
}
