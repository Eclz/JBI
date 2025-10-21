@extends('layouts.app')

@section('title', 'Edit Faculty Member')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Faculty Member</h4>
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Faculty List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.faculty.update', $faculty) }}" method="POST" enctype="multipart/form-data">
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
                                       id="first_name" name="first_name" value="{{ old('first_name', $faculty->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name', $faculty->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $faculty->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $faculty->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $faculty->date_of_birth?->format('Y-m-d')) }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $faculty->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $faculty->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $faculty->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="3" required>{{ old('address', $faculty->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact" class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror"
                                       id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $faculty->emergency_contact) }}" required>
                                @error('emergency_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="emergency_phone" class="form-label">Emergency Contact Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('emergency_phone') is-invalid @enderror"
                                       id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $faculty->emergency_phone) }}" required>
                                @error('emergency_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Academic Information</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                {{ old('department_id', $faculty->facultyProfile->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
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
                                       id="position" name="position" value="{{ old('position', $faculty->facultyProfile->position) }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status" required>
                                    <option value="active" {{ old('employment_status', $faculty->facultyProfile->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('employment_status', $faculty->facultyProfile->employment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="on_leave" {{ old('employment_status', $faculty->facultyProfile->employment_status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                    <option value="terminated" {{ old('employment_status', $faculty->facultyProfile->employment_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                                @error('employment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                       id="specialization" name="specialization"
                                       value="{{ old('specialization', $faculty->facultyProfile->qualifications['specialization'] ?? '') }}" required>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Qualifications -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Qualifications</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="highest_degree" class="form-label">Highest Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('highest_degree') is-invalid @enderror"
                                       id="highest_degree" name="highest_degree"
                                       value="{{ old('highest_degree', $faculty->facultyProfile->qualifications['highest_degree'] ?? '') }}" required>
                                @error('highest_degree')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="degree_institution" class="form-label">Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('degree_institution') is-invalid @enderror"
                                       id="degree_institution" name="degree_institution"
                                       value="{{ old('degree_institution', $faculty->facultyProfile->qualifications['institution'] ?? '') }}" required>
                                @error('degree_institution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="degree_year" class="form-label">Graduation Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('degree_year') is-invalid @enderror"
                                       id="degree_year" name="degree_year" min="1970" max="{{ date('Y') }}"
                                       value="{{ old('degree_year', $faculty->facultyProfile->qualifications['graduation_year'] ?? '') }}" required>
                                @error('degree_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="years_of_experience" class="form-label">Years of Experience <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('years_of_experience') is-invalid @enderror"
                                       id="years_of_experience" name="years_of_experience" min="0" max="50"
                                       value="{{ old('years_of_experience', $faculty->facultyProfile->experience['years_of_experience'] ?? '') }}" required>
                                @error('years_of_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="certifications" class="form-label">Certifications (comma-separated)</label>
                                <textarea class="form-control @error('certifications') is-invalid @enderror"
                                          id="certifications" name="certifications" rows="2">{{ old('certifications', is_array($faculty->facultyProfile->qualifications['certifications'] ?? null) ? implode(', ', $faculty->facultyProfile->qualifications['certifications']) : '') }}</textarea>
                                @error('certifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="research_interests" class="form-label">Research Interests (comma-separated)</label>
                                <textarea class="form-control @error('research_interests') is-invalid @enderror"
                                          id="research_interests" name="research_interests" rows="2">{{ old('research_interests', is_array($faculty->facultyProfile->experience['research_interests'] ?? null) ? implode(', ', $faculty->facultyProfile->experience['research_interests']) : '') }}</textarea>
                                @error('research_interests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Picture and Password -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Additional Information</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                       id="profile_picture" name="profile_picture" accept="image/*">
                                @if($faculty->profile_picture)
                                    <div class="mt-2">
                                        <img src="{{ $faculty->profile_picture_url }}" alt="Current Profile Picture"
                                             class="img-thumbnail" style="max-width: 100px;">
                                        <small class="text-muted d-block">Current profile picture</small>
                                    </div>
                                @endif
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           {{ old('is_active', $faculty->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active User
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">Cancel</a>
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
    // Auto-generate employee ID display (read-only)
    const employeeIdDisplay = document.getElementById('employee_id_display');
    if (employeeIdDisplay) {
        employeeIdDisplay.textContent = '{{ $faculty->facultyProfile->employee_id }}';
    }
});
</script>
@endpush
