@extends('layouts.app')

@section('title', 'Register - JBI University')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Left side - Registration Form -->
        <div class="col-md-8 d-flex align-items-center justify-content-center py-4">
            <div class="w-100" style="max-width: 800px;">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/jbi-logo.webp') }}" alt="JBI University" class="mb-3" style="height: 60px;">
                    <h2 class="fw-bold text-primary">Apply to JBI University</h2>
                    <p class="text-muted">Complete your application to join our academic community</p>
                </div>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registrationForm">
                    @csrf

                    <!-- Role Selection -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Application Type</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="role" class="form-label">I am applying as a:</label>
                                    <select class="form-select @error('role') is-invalid @enderror"
                                            id="role"
                                            name="role"
                                            required
                                            onchange="toggleRoleFields()">
                                        <option value="">Select Application Type</option>
                                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty Member</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text"
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name"
                                           name="first_name"
                                           value="{{ old('first_name') }}"
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ old('last_name') }}"
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                    <input type="date"
                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth"
                                           name="date_of_birth"
                                           value="{{ old('date_of_birth') }}"
                                           required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select @error('gender') is-invalid @enderror"
                                            id="gender"
                                            name="gender"
                                            required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="profile_picture" class="form-label">Profile Picture</label>
                                    <input type="file"
                                           class="form-control @error('profile_picture') is-invalid @enderror"
                                           id="profile_picture"
                                           name="profile_picture"
                                           accept="image/*">
                                    @error('profile_picture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address"
                                          name="address"
                                          rows="3"
                                          required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name *</label>
                                    <input type="text"
                                           class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                           id="emergency_contact_name"
                                           name="emergency_contact_name"
                                           value="{{ old('emergency_contact_name') }}"
                                           required>
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone *</label>
                                    <input type="tel"
                                           class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                           id="emergency_contact_phone"
                                           name="emergency_contact_phone"
                                           value="{{ old('emergency_contact_phone') }}"
                                           required>
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Academic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label">Department *</label>
                                    <select class="form-select @error('department_id') is-invalid @enderror"
                                            id="department_id"
                                            name="department_id"
                                            required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student-specific fields -->
                    <div id="studentFields" style="display: none;">
                        <!-- Program Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-book me-2"></i>Program Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="program" class="form-label">Program *</label>
                                        <select class="form-select @error('program') is-invalid @enderror"
                                                id="program"
                                                name="program">
                                            <option value="">Select Program</option>
                                            <option value="Bachelor of Arts" {{ old('program') == 'Bachelor of Arts' ? 'selected' : '' }}>Bachelor of Arts</option>
                                            <option value="Bachelor of Science" {{ old('program') == 'Bachelor of Science' ? 'selected' : '' }}>Bachelor of Science</option>
                                            <option value="Bachelor of Theology" {{ old('program') == 'Bachelor of Theology' ? 'selected' : '' }}>Bachelor of Theology</option>
                                            <option value="Master of Arts" {{ old('program') == 'Master of Arts' ? 'selected' : '' }}>Master of Arts</option>
                                            <option value="Master of Science" {{ old('program') == 'Master of Science' ? 'selected' : '' }}>Master of Science</option>
                                            <option value="Master of Divinity" {{ old('program') == 'Master of Divinity' ? 'selected' : '' }}>Master of Divinity</option>
                                            <option value="Doctor of Philosophy" {{ old('program') == 'Doctor of Philosophy' ? 'selected' : '' }}>Doctor of Philosophy</option>
                                            <option value="Doctor of Theology" {{ old('program') == 'Doctor of Theology' ? 'selected' : '' }}>Doctor of Theology</option>
                                        </select>
                                        @error('program')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="specialization" class="form-label">Specialization</label>
                                        <input type="text"
                                               class="form-control @error('specialization') is-invalid @enderror"
                                               id="specialization"
                                               name="specialization"
                                               value="{{ old('specialization') }}">
                                        @error('specialization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Guardian Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="guardian_name" class="form-label">Guardian Name *</label>
                                        <input type="text"
                                               class="form-control @error('guardian_name') is-invalid @enderror"
                                               id="guardian_name"
                                               name="guardian_name"
                                               value="{{ old('guardian_name') }}">
                                        @error('guardian_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="guardian_phone" class="form-label">Guardian Phone *</label>
                                        <input type="tel"
                                               class="form-control @error('guardian_phone') is-invalid @enderror"
                                               id="guardian_phone"
                                               name="guardian_phone"
                                               value="{{ old('guardian_phone') }}">
                                        @error('guardian_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="guardian_email" class="form-label">Guardian Email</label>
                                        <input type="email"
                                               class="form-control @error('guardian_email') is-invalid @enderror"
                                               id="guardian_email"
                                               name="guardian_email"
                                               value="{{ old('guardian_email') }}">
                                        @error('guardian_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="guardian_address" class="form-label">Guardian Address *</label>
                                    <textarea class="form-control @error('guardian_address') is-invalid @enderror"
                                              id="guardian_address"
                                              name="guardian_address"
                                              rows="3">{{ old('guardian_address') }}</textarea>
                                    @error('guardian_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Academic Background -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Academic Background</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_school" class="form-label">Previous School *</label>
                                        <input type="text"
                                               class="form-control @error('previous_school') is-invalid @enderror"
                                               id="previous_school"
                                               name="previous_school"
                                               value="{{ old('previous_school') }}">
                                        @error('previous_school')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="graduation_year" class="form-label">Graduation Year *</label>
                                        <input type="number"
                                               class="form-control @error('graduation_year') is-invalid @enderror"
                                               id="graduation_year"
                                               name="graduation_year"
                                               min="1990"
                                               max="{{ date('Y') }}"
                                               value="{{ old('graduation_year') }}">
                                        @error('graduation_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="previous_school_address" class="form-label">Previous School Address *</label>
                                    <textarea class="form-control @error('previous_school_address') is-invalid @enderror"
                                              id="previous_school_address"
                                              name="previous_school_address"
                                              rows="3">{{ old('previous_school_address') }}</textarea>
                                    @error('previous_school_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_gpa" class="form-label">Previous GPA</label>
                                        <input type="number"
                                               class="form-control @error('previous_gpa') is-invalid @enderror"
                                               id="previous_gpa"
                                               name="previous_gpa"
                                               step="0.01"
                                               min="0"
                                               max="4"
                                               value="{{ old('previous_gpa') }}">
                                        @error('previous_gpa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="major_subjects" class="form-label">Major Subjects</label>
                                        <input type="text"
                                               class="form-control @error('major_subjects') is-invalid @enderror"
                                               id="major_subjects"
                                               name="major_subjects"
                                               placeholder="Separate with commas"
                                               value="{{ old('major_subjects') }}">
                                        @error('major_subjects')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Scores -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Test Scores & Qualifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="sat_score" class="form-label">SAT Score</label>
                                        <input type="number"
                                               class="form-control @error('sat_score') is-invalid @enderror"
                                               id="sat_score"
                                               name="sat_score"
                                               min="400"
                                               max="1600"
                                               value="{{ old('sat_score') }}">
                                        @error('sat_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="act_score" class="form-label">ACT Score</label>
                                        <input type="number"
                                               class="form-control @error('act_score') is-invalid @enderror"
                                               id="act_score"
                                               name="act_score"
                                               min="1"
                                               max="36"
                                               value="{{ old('act_score') }}">
                                        @error('act_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="toefl_score" class="form-label">TOEFL Score</label>
                                        <input type="number"
                                               class="form-control @error('toefl_score') is-invalid @enderror"
                                               id="toefl_score"
                                               name="toefl_score"
                                               min="0"
                                               max="120"
                                               value="{{ old('toefl_score') }}">
                                        @error('toefl_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="ielts_score" class="form-label">IELTS Score</label>
                                        <input type="number"
                                               class="form-control @error('ielts_score') is-invalid @enderror"
                                               id="ielts_score"
                                               name="ielts_score"
                                               step="0.5"
                                               min="0"
                                               max="9"
                                               value="{{ old('ielts_score') }}">
                                        @error('ielts_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="high_school_diploma" name="high_school_diploma" {{ old('high_school_diploma') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="high_school_diploma">
                                                High School Diploma/Certificate
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="other_certifications" class="form-label">Other Certifications</label>
                                        <input type="text"
                                               class="form-control @error('other_certifications') is-invalid @enderror"
                                               id="other_certifications"
                                               name="other_certifications"
                                               placeholder="Separate with commas"
                                               value="{{ old('other_certifications') }}">
                                        @error('other_certifications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty-specific fields -->
                    <div id="facultyFields" style="display: none;">
                        <!-- Position Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-briefcase me-2"></i>Position Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="position" class="form-label">Desired Position *</label>
                                        <select class="form-select @error('position') is-invalid @enderror"
                                                id="position"
                                                name="position">
                                            <option value="">Select Position</option>
                                            <option value="Instructor" {{ old('position') == 'Instructor' ? 'selected' : '' }}>Instructor</option>
                                            <option value="Assistant Professor" {{ old('position') == 'Assistant Professor' ? 'selected' : '' }}>Assistant Professor</option>
                                            <option value="Associate Professor" {{ old('position') == 'Associate Professor' ? 'selected' : '' }}>Associate Professor</option>
                                            <option value="Professor" {{ old('position') == 'Professor' ? 'selected' : '' }}>Professor</option>
                                            <option value="Lecturer" {{ old('position') == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                                            <option value="Adjunct Faculty" {{ old('position') == 'Adjunct Faculty' ? 'selected' : '' }}>Adjunct Faculty</option>
                                        </select>
                                        @error('position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="years_of_experience" class="form-label">Years of Experience *</label>
                                        <input type="number"
                                               class="form-control @error('years_of_experience') is-invalid @enderror"
                                               id="years_of_experience"
                                               name="years_of_experience"
                                               min="0"
                                               max="50"
                                               value="{{ old('years_of_experience') }}">
                                        @error('years_of_experience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Educational Background -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Educational Background</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="highest_degree" class="form-label">Highest Degree *</label>
                                        <select class="form-select @error('highest_degree') is-invalid @enderror"
                                                id="highest_degree"
                                                name="highest_degree">
                                            <option value="">Select Degree</option>
                                            <option value="Bachelor's Degree" {{ old('highest_degree') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                            <option value="Master's Degree" {{ old('highest_degree') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                            <option value="Doctoral Degree" {{ old('highest_degree') == 'Doctoral Degree' ? 'selected' : '' }}>Doctoral Degree</option>
                                            <option value="Professional Degree" {{ old('highest_degree') == 'Professional Degree' ? 'selected' : '' }}>Professional Degree</option>
                                        </select>
                                        @error('highest_degree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="degree_institution" class="form-label">Institution *</label>
                                        <input type="text"
                                               class="form-control @error('degree_institution') is-invalid @enderror"
                                               id="degree_institution"
                                               name="degree_institution"
                                               value="{{ old('degree_institution') }}">
                                        @error('degree_institution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="degree_year" class="form-label">Graduation Year *</label>
                                        <input type="number"
                                               class="form-control @error('degree_year') is-invalid @enderror"
                                               id="degree_year"
                                               name="degree_year"
                                               min="1970"
                                               max="{{ date('Y') }}"
                                               value="{{ old('degree_year') }}">
                                        @error('degree_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="specialization" class="form-label">Specialization *</label>
                                        <input type="text"
                                               class="form-control @error('specialization') is-invalid @enderror"
                                               id="faculty_specialization"
                                               name="specialization"
                                               value="{{ old('specialization') }}">
                                        @error('specialization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="certifications" class="form-label">Certifications</label>
                                        <input type="text"
                                               class="form-control @error('certifications') is-invalid @enderror"
                                               id="certifications"
                                               name="certifications"
                                               placeholder="Separate with commas"
                                               value="{{ old('certifications') }}">
                                        @error('certifications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_positions" class="form-label">Previous Positions</label>
                                        <input type="text"
                                               class="form-control @error('previous_positions') is-invalid @enderror"
                                               id="previous_positions"
                                               name="previous_positions"
                                               placeholder="Separate with commas"
                                               value="{{ old('previous_positions') }}">
                                        @error('previous_positions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="research_interests" class="form-label">Research Interests</label>
                                    <textarea class="form-control @error('research_interests') is-invalid @enderror"
                                              id="research_interests"
                                              name="research_interests"
                                              rows="3"
                                              placeholder="Describe your research interests and areas of expertise">{{ old('research_interests') }}</textarea>
                                    @error('research_interests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Upload -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-file-earmark-arrow-up me-2"></i>Supporting Documents</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="documents" class="form-label">Upload Documents</label>
                                <input type="file"
                                       class="form-control @error('documents') is-invalid @enderror"
                                       id="documents"
                                       name="documents[]"
                                       multiple
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="form-text">
                                    Upload transcripts, certificates, ID copy, and other relevant documents.
                                    Accepted formats: PDF, DOC, DOCX, JPG, PNG. Max size: 5MB per file.
                                </div>
                                @error('documents')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-chat-text me-2"></i>Additional Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="application_notes" class="form-label">Personal Statement / Additional Notes</label>
                                <textarea class="form-control @error('application_notes') is-invalid @enderror"
                                          id="application_notes"
                                          name="application_notes"
                                          rows="5"
                                          placeholder="Tell us about yourself, your goals, and why you want to join JBI University...">{{ old('application_notes') }}</textarea>
                                @error('application_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Account Security</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required>
                                    <div class="form-text">
                                        Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a> *
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>Submit Application
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-3">
                        <p class="mb-0">Already have an account?
                            <a href="{{ route('login') }}" class="text-decoration-none">Sign in here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right side - Info Panel -->
        <div class="col-md-4 bg-primary d-none d-md-flex align-items-center justify-content-center text-white">
            <div class="text-center p-4">
                <i class="bi bi-mortarboard-fill" style="font-size: 4rem;"></i>
                <h3 class="mt-3">Join JBI University</h3>
                <p class="lead">Start your academic journey with us</p>

                <div class="mt-4">
                    <h5>Application Process</h5>
                    <div class="text-start">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-1-circle-fill me-2"></i>
                            <span>Submit Application</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-2-circle-fill me-2"></i>
                            <span>Document Review</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-3-circle-fill me-2"></i>
                            <span>Admission Decision</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-4-circle-fill me-2"></i>
                            <span>Account Activation</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>Need Help?</h6>
                    <p class="small">Contact our admissions office at<br>
                    <strong>admissions@jbi.edu</strong><br>
                    <strong>(555) 123-4567</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('studentFields');
    const facultyFields = document.getElementById('facultyFields');

    if (role === 'student') {
        studentFields.style.display = 'block';
        facultyFields.style.display = 'none';

        // Make student fields required
        document.getElementById('program').required = true;
        document.getElementById('guardian_name').required = true;
        document.getElementById('guardian_phone').required = true;
        document.getElementById('guardian_address').required = true;
        document.getElementById('previous_school').required = true;
        document.getElementById('previous_school_address').required = true;
        document.getElementById('graduation_year').required = true;

        // Remove faculty field requirements
        document.getElementById('position').required = false;
        document.getElementById('highest_degree').required = false;
        document.getElementById('degree_institution').required = false;
        document.getElementById('degree_year').required = false;
        document.getElementById('faculty_specialization').required = false;
        document.getElementById('years_of_experience').required = false;

    } else if (role === 'faculty') {
        studentFields.style.display = 'none';
        facultyFields.style.display = 'block';

        // Remove student field requirements
        document.getElementById('program').required = false;
        document.getElementById('guardian_name').required = false;
        document.getElementById('guardian_phone').required = false;
        document.getElementById('guardian_address').required = false;
        document.getElementById('previous_school').required = false;
        document.getElementById('previous_school_address').required = false;
        document.getElementById('graduation_year').required = false;

        // Make faculty fields required
        document.getElementById('position').required = true;
        document.getElementById('highest_degree').required = true;
        document.getElementById('degree_institution').required = true;
        document.getElementById('degree_year').required = true;
        document.getElementById('faculty_specialization').required = true;
        document.getElementById('years_of_experience').required = true;

    } else {
        studentFields.style.display = 'none';
        facultyFields.style.display = 'none';

        // Remove all role-specific requirements
        document.getElementById('program').required = false;
        document.getElementById('guardian_name').required = false;
        document.getElementById('guardian_phone').required = false;
        document.getElementById('guardian_address').required = false;
        document.getElementById('previous_school').required = false;
        document.getElementById('previous_school_address').required = false;
        document.getElementById('graduation_year').required = false;
        document.getElementById('position').required = false;
        document.getElementById('highest_degree').required = false;
        document.getElementById('degree_institution').required = false;
        document.getElementById('degree_year').required = false;
        document.getElementById('faculty_specialization').required = false;
        document.getElementById('years_of_experience').required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRoleFields();
});
</script>
@endsection
