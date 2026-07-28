@extends('layouts.guest')

@section('title', 'Apply to JBI University')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Application Form - JBI University</h3>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Application Process:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Submit your application with all required documents</li>
                            <li>Wait for application review (typically 3-5 business days)</li>
                            <li>If approved, pay the admission fee</li>
                            <li>Receive your admission letter and student number</li>
                        </ol>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
                        @csrf

                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Application Type</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="typeStudent" value="student" {{ old('type', 'student') === 'student' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="typeStudent">
                                            <strong>Student Application</strong><br>
                                            <small class="text-muted">Apply for undergraduate or graduate programs</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="typeFaculty" value="faculty" {{ old('type') === 'faculty' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="typeFaculty">
                                            <strong>Faculty Application</strong><br>
                                            <small class="text-muted">Apply for teaching positions</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Personal Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div id="studentFields" class="mb-4" style="display: {{ old('type', 'student') === 'student' ? 'block' : 'none' }}">
                            <h5 class="border-bottom pb-2">Academic Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                                    <select class="form-select" id="program_id" name="program_id">
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif - {{ $program->department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="previous_school" class="form-label">Previous School/College</label>
                                    <input type="text" class="form-control" id="previous_school" name="previous_school" value="{{ old('previous_school') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="previous_qualification" class="form-label">Previous Qualification</label>
                                    <input type="text" class="form-control" id="previous_qualification" name="previous_qualification" value="{{ old('previous_qualification') }}" placeholder="e.g., High School Diploma, Bachelor's Degree">
                                </div>
                                <div class="col-md-6">
                                    <label for="previous_gpa" class="form-label">Previous GPA (0-4 scale)</label>
                                    <input type="number" class="form-control" id="previous_gpa" name="previous_gpa" value="{{ old('previous_gpa') }}" min="0" max="4" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div id="facultyFields" class="mb-4" style="display: {{ old('type') === 'faculty' ? 'block' : 'none' }}">
                            <h5 class="border-bottom pb-2">Professional Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" id="department" name="department">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->name }}" {{ old('department') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="position" class="form-label">Position Applying For</label>
                                    <input type="text" class="form-control" id="position" name="position" value="{{ old('position') }}" placeholder="e.g., Assistant Professor, Lecturer">
                                </div>
                                <div class="col-md-6">
                                    <label for="highest_degree" class="form-label">Highest Degree</label>
                                    <input type="text" class="form-control" id="highest_degree" name="highest_degree" value="{{ old('highest_degree') }}" placeholder="e.g., PhD, Master's, Bachelor's">
                                </div>
                                <div class="col-md-6">
                                    <label for="specialization" class="form-label">Specialization</label>
                                    <input type="text" class="form-control" id="specialization" name="specialization" value="{{ old('specialization') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="years_of_experience" class="form-label">Years of Experience</label>
                                    <input type="number" class="form-control" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Supporting Documents</h5>
                            <div class="alert alert-warning">
                                <small><i class="bi bi-exclamation-triangle me-2"></i>Upload relevant documents (transcripts, certificates, ID, CV, etc.). Max 5MB per file. Accepted formats: PDF, JPG, PNG</small>
                            </div>
                            <input type="file" class="form-control" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const studentFields = document.getElementById('studentFields');
    const facultyFields = document.getElementById('facultyFields');
    const programField = document.getElementById('program_id');
    const departmentField = document.getElementById('department');

    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'student') {
                studentFields.style.display = 'block';
                facultyFields.style.display = 'none';
                programField.required = true;
                departmentField.required = false;
            } else {
                studentFields.style.display = 'none';
                facultyFields.style.display = 'block';
                programField.required = false;
                departmentField.required = true;
            }
        });
    });
});
</script>
@endsection
