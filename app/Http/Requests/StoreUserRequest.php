<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/', // Only letters, spaces, dots, hyphens, apostrophes
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'confirmed',
            ],
            'role' => [
                'nullable',
                'string',
                Rule::in(['admin', 'faculty', 'student', 'parent']),
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'student_id' => [
                'nullable',
                'nullable',
                'string',
                'regex:/^JBI\d{4,6}$/',
                'unique:users,student_id',
            ],
            'employee_id' => [
                'nullable',
                'nullable',
                'string',
                'regex:/^JBI\d{3,5}$/',
                'unique:users,employee_id',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'gender' => [
                'nullable',
                'string',
                Rule::in(['male', 'female', 'other', 'prefer_not_to_say']),
            ],
            'emergency_contact' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'emergency_phone' => [
                'nullable',
                'string',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'profile_picture' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048', // 2MB
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'preferences' => [
                'nullable',
                'array',
            ],
            'preferences.notifications' => [
                'nullable',
                'array',
            ],
            'preferences.notifications.email' => [
                'nullable',
                'boolean',
            ],
            'preferences.notifications.sms' => [
                'nullable',
                'boolean',
            ],
            'preferences.language' => [
                'nullable',
                'string',
                Rule::in(['en', 'es', 'fr', 'de']),
            ],
            'preferences.timezone' => [
                'nullable',
                'string',
                'timezone',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            'email.email' => 'Please provide a valid email address.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'student_id.regex' => 'Student ID must be in format JBI followed by 4-6 digits (e.g., JBI2024).',
            'employee_id.regex' => 'Employee ID must be in format JBI followed by 3-5 digits (e.g., JBI001).',
            'phone.regex' => 'Please provide a valid phone number.',
            'emergency_phone.regex' => 'Please provide a valid emergency contact phone number.',
            'profile_picture.dimensions' => 'Profile picture must be between 100x100 and 2000x2000 pixels.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'date_of_birth' => 'date of birth',
            'emergency_contact' => 'emergency contact name',
            'emergency_phone' => 'emergency contact phone',
            'profile_picture' => 'profile picture',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for age requirements
            if ($this->date_of_birth && $this->role === 'student') {
                $age = now()->diffInYears($this->date_of_birth);
                if ($age < 16) {
                    $validator->errors()->add('date_of_birth', 'Students must be at least 16 years old.');
                }
                if ($age > 80) {
                    $validator->errors()->add('date_of_birth', 'Please verify the date of birth.');
                }
            }

            // Validate email domain for institutional emails
            if ($this->email && in_array($this->role, ['faculty', 'admin'])) {
                $domain = substr(strrchr($this->email, "@"), 1);
                if (!in_array($domain, ['jbi.edu', 'johnsonbible.edu'])) {
                    $validator->errors()->add('email', 'Faculty and admin users must use institutional email addresses.');
                }
            }
        });
    }
}
