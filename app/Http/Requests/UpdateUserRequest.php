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
        $userId = $user instanceof \App\Models\User ? $user->id : ($user ?? $this->id ?? null);

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
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
                'string',
                Rule::unique('users', 'student_id')->ignore($userId),
            ],
            'employee_id' => [
                'nullable',
                'string',
                Rule::unique('users', 'employee_id')->ignore($userId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[0-9\-\+\s\(\)\.]{7,25}$/',
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
            ],
            'emergency_phone' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^[0-9\-\+\s\(\)\.]{7,25}$/',
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
            'phone.regex' => 'Please provide a valid phone number.',
            'emergency_phone.regex' => 'Please provide a valid emergency contact phone number.',
        ];
    }
}
