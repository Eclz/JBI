@extends('layouts.app')

@section('title', 'Edit Faculty Member')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Faculty Member: {{ $facultyStaff->name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.faculty-staff.show', $facultyStaff) }}" class="btn btn-outline-info">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.faculty-staff.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Faculty Staff
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.faculty-staff.update', $facultyStaff) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name" value="{{ old('first_name', $facultyStaff->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name', $facultyStaff->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $facultyStaff->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $facultyStaff->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $facultyStaff->date_of_birth?->format('Y-m-d')) }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $facultyStaff->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $facultyStaff->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $facultyStaff->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                       id="profile_picture" name="profile_picture" accept="image/*">
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($facultyStaff->profile_picture)
                                    <div class="form-text">
                                        <small>Current: <a href="{{ $facultyStaff->profile_picture_url }}" target="_blank">View Current Picture</a></small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="2" required>{{ old('address', $facultyStaff->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror"
                                       id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $facultyStaff->emergency_contact) }}" required>
                                @error('emergency_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="emergency_phone" class="form-label">Emergency Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('emergency_phone') is-invalid @enderror"
                                       id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $facultyStaff->emergency_phone) }}" required>
                                @error('emergency_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Employment Information</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                {{ old('department_id', $facultyStaff->facultyProfile?->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                            @if($department->faculty)
                                                ({{ $department->faculty->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                       id="position" name="position" value="{{ old('position', $facultyStaff->facultyProfile?->position) }}" required
                                       placeholder="e.g., Assistant Professor, Lecturer">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_type') is-invalid @enderror" id="employment_type" name="employment_type" required>
                                    <option value="">Select Employment Type</option>
                                    <option value="full_time" {{ old('employment_type', $facultyStaff->facultyProfile?->employment_type) == 'full_time' ? 'selected' : '' }}>Full-time</option>
                                    <option value="part_time" {{ old('employment_type', $facultyStaff->facultyProfile?->employment_type) == 'part_time' ? 'selected' : '' }}>Part-time</option>
                                    <option value="contract" {{ old('employment_type', $facultyStaff->facultyProfile?->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="visiting" {{ old('employment_type', $facultyStaff->facultyProfile?->employment_type) == 'visiting' ? 'selected' : '' }}>Visiting</option>
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status" required>
                                    <option value="">Select Employment Status</option>
                                    <option value="active" {{ old('employment_status', $facultyStaff->facultyProfile?->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('employment_status', $facultyStaff->facultyProfile?->employment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="on_leave" {{ old('employment_status', $facultyStaff->facultyProfile?->employment_status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="terminated" {{ old('employment_status', $facultyStaff->facultyProfile?->employment_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                                @error('employment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                       id="specialization" name="specialization" value="{{ old('specialization', $facultyStaff->facultyProfile?->specialization) }}" required
                                       placeholder="e.g., Computer Science, Mathematics">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="years_of_experience" class="form-label">Years of Experience <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('years_of_experience') is-invalid @enderror"
                                       id="years_of_experience" name="years_of_experience"
                                       value="{{ old('years_of_experience', $facultyStaff->facultyProfile?->years_of_experience) }}"
                                       min="0" max="50" required>
                                @error('years_of_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Educational Background -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Educational Background</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="highest_degree" class="form-label">Highest Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('highest_degree') is-invalid @enderror"
                                       id="highest_degree" name="highest_degree"
                                       value="{{ old('highest_degree', data_get($facultyStaff->facultyProfile, 'qualifications.highest_degree', $facultyStaff->facultyProfile?->qualification ?? '')) }}" required
                                       placeholder="e.g., Ph.D., Master's, Bachelor's">
                                @error('highest_degree')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="degree_institution" class="form-label">Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('degree_institution') is-invalid @enderror"
                                       id="degree_institution" name="degree_institution"
                                       value="{{ old('degree_institution', data_get($facultyStaff->facultyProfile, 'qualifications.institution', '')) }}" required
                                       placeholder="e.g., Harvard University">
                                @error('degree_institution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="degree_year" class="form-label">Graduation Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('degree_year') is-invalid @enderror"
                                       id="degree_year" name="degree_year"
                                       value="{{ old('degree_year', data_get($facultyStaff->facultyProfile, 'qualifications.graduation_year', '')) }}"
                                       min="1970" max="{{ date('Y') }}" required>
                                @error('degree_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="certifications" class="form-label">Certifications</label>
                                @php
                                    $certs = data_get($facultyStaff->facultyProfile, 'qualifications.certifications', []);
                                    $certsValue = is_array($certs) ? implode(', ', $certs) : (is_string($certs) ? $certs : '');
                                @endphp
                                <input type="text" class="form-control @error('certifications') is-invalid @enderror"
                                       id="certifications" name="certifications"
                                       value="{{ old('certifications', $certsValue) }}"
                                       placeholder="Comma-separated list of certifications">
                                @error('certifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Separate multiple certifications with commas</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="research_interests" class="form-label">Research Interests</label>
                                @php
                                    $interests = data_get($facultyStaff->facultyProfile, 'experience.research_interests', []);
                                    $interestsValue = is_array($interests) ? implode(', ', $interests) : (is_string($interests) ? $interests : '');
                                @endphp
                                <input type="text" class="form-control @error('research_interests') is-invalid @enderror"
                                       id="research_interests" name="research_interests"
                                       value="{{ old('research_interests', $interestsValue) }}"
                                       placeholder="Comma-separated list of research interests">
                                @error('research_interests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Separate multiple research interests with commas</div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Additional Information</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">Biography</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror"
                                          id="bio" name="bio" rows="4"
                                          placeholder="Brief biography and professional background">{{ old('bio', $facultyStaff->facultyProfile?->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="linkedin_profile" class="form-label">LinkedIn Profile</label>
                                <input type="url" class="form-control @error('linkedin_profile') is-invalid @enderror"
                                       id="linkedin_profile" name="linkedin_profile"
                                       value="{{ old('linkedin_profile', $facultyStaff->facultyProfile?->linkedin_profile) }}"
                                       placeholder="https://linkedin.com/in/username">
                                @error('linkedin_profile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="personal_website" class="form-label">Personal Website</label>
                                <input type="url" class="form-control @error('personal_website') is-invalid @enderror"
                                       id="personal_website" name="personal_website"
                                       value="{{ old('personal_website', $facultyStaff->facultyProfile?->personal_website) }}"
                                       placeholder="https://example.com">
                                @error('personal_website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Course Assignments -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-journal-bookmark me-2"></i>Course & Teaching Assignments</h5>
                                <p class="text-muted small mb-3">Select the courses assigned to this faculty member. You can assign courses across departments and faculties.</p>
                            </div>

                            <div class="col-12 mb-3">
                                @if(isset($courses) && $courses->count() > 0)
                                    <div class="card bg-light border p-3" style="max-height: 280px; overflow-y: auto;">
                                        <div class="row g-2">
                                            @foreach($courses as $c)
                                                @php
                                                    $isAssigned = in_array($c->id, old('assigned_courses', $assignedCourseIds ?? []));
                                                    $isAssignedOther = $c->instructor_id && $c->instructor_id !== $facultyStaff->id;
                                                @endphp
                                                <div class="col-md-6 course-assignment-item" data-department-id="{{ $c->department_id }}">
                                                    <div class="form-check p-2 bg-white rounded border shadow-sm h-100">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="assigned_courses[]" value="{{ $c->id }}" id="course_{{ $c->id }}" {{ $isAssigned ? 'checked' : '' }}>
                                                        <label class="form-check-label small fw-semibold" for="course_{{ $c->id }}">
                                                            {{ $c->name }} <span class="badge bg-secondary ms-1">{{ $c->code }}</span>
                                                            @if($c->department)
                                                                <span class="text-muted d-block font-monospace" style="font-size: 0.72rem;">{{ $c->department->name }}</span>
                                                            @endif
                                                            @if($isAssignedOther && $c->instructor)
                                                                <span class="badge bg-warning text-dark d-inline-block mt-1" style="font-size: 0.65rem;">Currently: {{ $c->instructor->name }}</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info small mb-0">No courses available in the system. Create courses in Course Management first.</div>
                                @endif
                            </div>
                        </div>

                        <!-- Security -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Security & Status</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password"
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 8 characters. Leave blank to keep current password.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="Confirm new password">
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           {{ old('is_active', $facultyStaff->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Faculty Member
                                    </label>
                                </div>
                                <div class="form-text">Inactive faculty members cannot access the system</div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.faculty-staff.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Update Faculty Member
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle department filtering based on faculty selection
    const facultySelect = document.getElementById('faculty');
    const departmentSelect = document.getElementById('department_id');

    if (facultySelect && departmentSelect) {
        facultySelect.addEventListener('change', function() {
            const facultyId = this.value;
            let option;

            // Reset department options
            departmentSelect.innerHTML = '<option value="">Select Department</option>';

            if (facultyId) {
                // Filter departments by selected faculty
                @foreach($departments as $department)
                    if ('{{ $department->faculty_id }}' === facultyId) {
                        option = document.createElement('option');
                        option.value = '{{ $department->id }}';
                        option.textContent = '{{ $department->name }}';
                        if ('{{ old('department_id', $facultyStaff->facultyProfile?->department_id) }}' === '{{ $department->id }}') {
                            option.selected = true;
                        }
                        departmentSelect.appendChild(option);
                    }
                @endforeach
            } else {
                // Show all departments
                @foreach($departments as $department)
                    option = document.createElement('option');
                    option.value = '{{ $department->id }}';
                    option.textContent = '{{ $department->name }}';
                    if ('{{ old('department_id', $facultyStaff->facultyProfile?->department_id) }}' === '{{ $department->id }}') {
                        option.selected = true;
                    }
                    departmentSelect.appendChild(option);
                @endforeach
            }
            // Trigger Select2 update and change event
            $(departmentSelect).trigger('change');
        });
    }

    // Filter courses based on department selection
    const courseItems = $('.course-assignment-item');
    if (departmentSelect && courseItems.length) {
        function filterCoursesByDepartment(departmentId) {
            courseItems.each(function() {
                const itemDeptId = $(this).data('department-id');
                const isChecked = $(this).find('input[type="checkbox"]').is(':checked');
                if (!departmentId || itemDeptId == departmentId || isChecked) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Initialize filter on load (wrapped in a small timeout to let select2 finish rendering)
        setTimeout(function() {
            filterCoursesByDepartment($(departmentSelect).val());
        }, 100);

        // Update filter on change
        $(departmentSelect).on('change', function() {
            filterCoursesByDepartment($(this).val());
        });
    }

    // Password confirmation validation
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');

    function validatePasswordMatch() {
        if (!passwordField || !confirmPasswordField) return;
        const passVal = passwordField.value.trim();
        const confVal = confirmPasswordField.value.trim();
        if (passVal !== '' || confVal !== '') {
            if (passVal !== confVal) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        } else {
            confirmPasswordField.setCustomValidity('');
        }
    }

    if (passwordField && confirmPasswordField) {
        passwordField.addEventListener('input', validatePasswordMatch);
        confirmPasswordField.addEventListener('input', validatePasswordMatch);
    }
});
</script>
@endpush
