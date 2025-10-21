<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacultyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $facultyId = $this->route('faculty')->id;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($facultyId)],
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,visiting',
            'employment_status' => 'required|in:active,inactive,on_leave,terminated',
            'highest_degree' => 'required|string|max:255',
            'degree_institution' => 'required|string|max:255',
            'degree_year' => 'required|integer|min:1970|max:' . date('Y'),
            'specialization' => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'bio' => 'nullable|string|max:1000',
            'linkedin_profile' => 'nullable|url|max:255',
            'personal_website' => 'nullable|url|max:255',
            'certifications' => 'nullable|string|max:1000',
            'research_interests' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department is invalid.',
            'employment_type.required' => 'Please select an employment type.',
            'employment_type.in' => 'The employment type must be full-time, part-time, contract, or visiting.',
            'employment_status.required' => 'Please select an employment status.',
            'employment_status.in' => 'The employment status must be active, inactive, on leave, or terminated.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'degree_year.min' => 'Graduation year must be 1970 or later.',
            'degree_year.max' => 'Graduation year cannot be in the future.',
            'years_of_experience.min' => 'Years of experience cannot be negative.',
            'years_of_experience.max' => 'Years of experience cannot exceed 50.',
            'profile_picture.image' => 'Profile picture must be an image file.',
            'profile_picture.mimes' => 'Profile picture must be a JPEG, PNG, or JPG file.',
            'profile_picture.max' => 'Profile picture must not exceed 2MB.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone numbers
        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone)
            ]);
        }

        if ($this->emergency_phone) {
            $this->merge([
                'emergency_phone' => preg_replace('/[^0-9+\-\s]/', '', $this->emergency_phone)
            ]);
        }

        // Ensure URLs have proper protocol
        if ($this->linkedin_profile && !str_starts_with($this->linkedin_profile, 'http')) {
            $this->merge(['linkedin_profile' => 'https://' . $this->linkedin_profile]);
        }

        if ($this->personal_website && !str_starts_with($this->personal_website, 'http')) {
            $this->merge(['personal_website' => 'https://' . $this->personal_website]);
        }

        // Convert checkbox to boolean
        $this->merge(['is_active' => $this->has('is_active')]);
    }
}
