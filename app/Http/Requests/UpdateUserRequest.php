<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s\.\-\']+$/',
            ],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'sometimes',
                'nullable',
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
                Rule::unique('users', 'student_id')->ignore($user->id),
            ],
            'employee_id' => [
                'nullable',
                'nullable',
                'string',
                'regex:/^JBI\d{3,5}$/',
                Rule::unique('users', 'employee_id')->ignore($user->id),
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
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'preferences' => [
                'nullable',
                'array',
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
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'student_id.regex' => 'Student ID must be in format JBI followed by 4-6 digits.',
            'employee_id.regex' => 'Employee ID must be in format JBI followed by 3-5 digits.',
        ];
    }
}
