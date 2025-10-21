<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\StudentProfile;

class StoreStudentProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', StudentProfile::class);
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
                Rule::exists('users', 'id')->where('role', 'student'),
                'unique:student_profiles,user_id',
            ],
            'admission_number' => [
                'required',
                'string',
                'regex:/^ADM\d{6}$/',
                'unique:student_profiles,admission_number',
            ],
            'admission_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:' . now()->subYears(10)->toDateString(),
            ],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where('is_active', true),
            ],
            'program' => [
                'required',
                'string',
                'max:255',
                Rule::in([
                    'Bachelor of Arts in Biblical Studies',
                    'Bachelor of Arts in Theology',
                    'Bachelor of Arts in Christian Ministry',
                    'Bachelor of Science in Christian Education',
                    'Master of Divinity',
                    'Master of Arts in Biblical Studies',
                    'Master of Arts in Theology',
                    'Master of Arts in Christian Ministry',
                    'Doctor of Ministry',
                    'Certificate in Biblical Studies',
                    'Certificate in Christian Ministry',
                ]),
            ],
            'specialization' => [
                'nullable',
                'string',
                'max:255',
                Rule::in([
                    'Biblical Exegesis',
                    'Systematic Theology',
                    'Pastoral Ministry',
                    'Youth Ministry',
                    'Missions',
                    'Church History',
                    'Christian Education',
                    'Worship Leadership',
                    'Biblical Languages',
                    'Apologetics',
                    'Counseling',
                    'Church Planting',
                ]),
            ],
            'current_semester' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(['active', 'inactive', 'graduated', 'dropped', 'suspended']),
            ],
            'current_gpa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:4.0',
                'decimal:0,2',
            ],
            'cumulative_gpa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:4.0',
                'decimal:0,2',
            ],
            'total_credits_earned' => [
                'required',
                'integer',
                'min:0',
                'max:200',
            ],
            'total_credits_required' => [
                'required',
                'integer',
                'min:30',
                'max:200',
            ],
            'expected_graduation_date' => [
                'nullable',
                'date',
                'after:admission_date',
                'before:' . now()->addYears(10)->toDateString(),
            ],
            'actual_graduation_date' => [
                'nullable',
                'date',
                'after:admission_date',
                'before_or_equal:today',
                'required_if:status,graduated',
            ],
            'guardian_name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'guardian_phone' => [
                'nullable',
                'string',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'guardian_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],
            'guardian_address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'previous_school' => [
                'nullable',
                'string',
                'max:255',
            ],
            'academic_history' => [
                'nullable',
                'array',
            ],
            'academic_history.*.institution' => [
                'required_with:academic_history',
                'string',
                'max:255',
            ],
            'academic_history.*.graduation_year' => [
                'required_with:academic_history',
                'integer',
                'min:1950',
                'max:' . now()->year,
            ],
            'academic_history.*.gpa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:4.0',
            ],
            'achievements' => [
                'nullable',
                'array',
            ],
            'achievements.*' => [
                'string',
                'max:255',
            ],
            'notes' => [
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
            'admission_number.regex' => 'Admission number must be in format ADM followed by 6 digits (e.g., ADM202401).',
            'guardian_name.regex' => 'Guardian name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            'guardian_phone.regex' => 'Please provide a valid guardian phone number.',
            'actual_graduation_date.required_if' => 'Graduation date is required for graduated students.',
            'total_credits_earned.max' => 'Total credits earned cannot exceed 200.',
            'current_gpa.max' => 'GPA cannot exceed 4.0.',
            'cumulative_gpa.max' => 'Cumulative GPA cannot exceed 4.0.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate credits earned vs required
            if ($this->total_credits_earned > $this->total_credits_required) {
                $validator->errors()->add(
                    'total_credits_earned',
                    'Credits earned cannot exceed credits required.'
                );
            }

            // Validate program-specific credit requirements
            $this->validateProgramCredits($validator);

            // Validate GPA consistency
            if ($this->current_gpa && $this->cumulative_gpa) {
                $difference = abs($this->current_gpa - $this->cumulative_gpa);
                if ($difference > 1.5) {
                    $validator->errors()->add(
                        'current_gpa',
                        'Current GPA and cumulative GPA seem inconsistent.'
                    );
                }
            }

            // Validate graduation requirements
            if ($this->status === 'graduated') {
                $this->validateGraduationRequirements($validator);
            }
        });
    }

    /**
     * Validate program-specific credit requirements.
     */
    private function validateProgramCredits($validator): void
    {
        $programCredits = [
            'Bachelor of Arts in Biblical Studies' => 120,
            'Bachelor of Arts in Theology' => 120,
            'Bachelor of Arts in Christian Ministry' => 120,
            'Bachelor of Science in Christian Education' => 120,
            'Master of Divinity' => 90,
            'Master of Arts in Biblical Studies' => 60,
            'Master of Arts in Theology' => 60,
            'Master of Arts in Christian Ministry' => 60,
            'Doctor of Ministry' => 90,
            'Certificate in Biblical Studies' => 30,
            'Certificate in Christian Ministry' => 30,
        ];

        if (isset($programCredits[$this->program])) {
            $expectedCredits = $programCredits[$this->program];
            if ($this->total_credits_required !== $expectedCredits) {
                $validator->errors()->add(
                    'total_credits_required',
                    "This program typically requires {$expectedCredits} credits."
                );
            }
        }
    }

    /**
     * Validate graduation requirements.
     */
    private function validateGraduationRequirements($validator): void
    {
        // Must have completed required credits
        if ($this->total_credits_earned < $this->total_credits_required) {
            $validator->errors()->add(
                'status',
                'Student must complete all required credits before graduation.'
            );
        }

        // Must have minimum GPA
        if ($this->cumulative_gpa && $this->cumulative_gpa < 2.0) {
            $validator->errors()->add(
                'status',
                'Student must have minimum 2.0 GPA to graduate.'
            );
        }

        // Must have graduation date
        if (!$this->actual_graduation_date) {
            $validator->errors()->add(
                'actual_graduation_date',
                'Graduation date is required for graduated students.'
            );
        }
    }
}
