<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'faculty_id' => 'nullable|exists:faculties,id',
            'head_of_department_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',
            'code.required' => 'Department code is required.',
            'code.unique' => 'A department with this code already exists.',
            'code.max' => 'Department code cannot exceed 10 characters.',
            'head_of_department_id.exists' => 'Selected head of department does not exist.',
            'email.email' => 'Please enter a valid email address.',
            'phone.max' => 'Phone number cannot exceed 20 characters.'
        ];
    }
}
