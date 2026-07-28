@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Course Creation Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-plus-circle me-2"></i>Create New Course
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.courses.store') }}" method="POST">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fa fa-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="e.g., Introduction to Computer Science"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('code') is-invalid @enderror"
                                           id="code"
                                           name="code"
                                           value="{{ old('code') }}"
                                           placeholder="e.g., CS101"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Course Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fa fa-book me-2"></i>Course Details
                                </h6>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4"
                                              placeholder="Enter course description...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="credits" class="form-label">Credits <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('credits') is-invalid @enderror"
                                           id="credits"
                                           name="credits"
                                           value="{{ old('credits', 3) }}"
                                           min="1"
                                           max="10"
                                           required>
                                    @error('credits')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_students" class="form-label">Max Students</label>
                                    <input type="number"
                                           class="form-control @error('max_students') is-invalid @enderror"
                                           id="max_students"
                                           name="max_students"
                                           value="{{ old('max_students', 50) }}"
                                           min="1"
                                           max="500">
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="course_type" class="form-label">Course Type</label>
                                    <select class="form-select @error('course_type') is-invalid @enderror"
                                            id="course_type"
                                            name="course_type">
                                        <option value="core" {{ old('course_type') == 'core' ? 'selected' : '' }}>Core</option>
                                        <option value="elective" {{ old('course_type') == 'elective' ? 'selected' : '' }}>Elective</option>
                                        <option value="lab" {{ old('course_type') == 'lab' ? 'selected' : '' }}>Lab</option>
                                        <option value="project" {{ old('course_type') == 'project' ? 'selected' : '' }}>Project</option>
                                    </select>
                                    @error('course_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Assignment -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fa fa-users me-2"></i>Assignment
                                </h6>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror"
                                            id="department_id"
                                            name="department_id"
                                            required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}"
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select class="form-select @error('semester_id') is-invalid @enderror"
                                            id="semester_id"
                                            name="semester_id"
                                            required>
                                        <option value="">Select Semester</option>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                    {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }} ({{ $semester->academic_year }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semester_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">Instructor</label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror"
                                            id="instructor_id"
                                            name="instructor_id">
                                        <option value="">Select Instructor</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                    {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program_id" class="form-label">Program</label>
                                    <select class="form-select @error('program_id') is-invalid @enderror"
                                            id="program_id"
                                            name="program_id">
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}"
                                                {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif - {{ $program->department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year_of_study" class="form-label">Year of Study</label>
                                    <input type="number"
                                           class="form-control @error('year_of_study') is-invalid @enderror"
                                           id="year_of_study"
                                           name="year_of_study"
                                           value="{{ old('year_of_study') }}"
                                           min="1"
                                           max="12"
                                           placeholder="e.g., 1">
                                    @error('year_of_study')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Course Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fa fa-cog me-2"></i>Settings
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prerequisites" class="form-label">Prerequisites</label>
                                    <textarea class="form-control @error('prerequisites') is-invalid @enderror"
                                              id="prerequisites"
                                              name="prerequisites"
                                              rows="3"
                                              placeholder="List any prerequisite courses or requirements...">{{ old('prerequisites') }}</textarea>
                                    @error('prerequisites')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_elective"
                                           name="is_elective"
                                           value="1"
                                           {{ old('is_elective') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_elective">
                                        This is an elective course
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-2"></i>Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-question-circle me-2"></i>Course Creation Help
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Course Code Guidelines</h6>
                        <ul class="small text-muted">
                            <li>Use department prefix (e.g., CS, MATH, ENG)</li>
                            <li>Follow with course level (100-400)</li>
                            <li>Keep it unique and descriptive</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">Credit Hours</h6>
                        <ul class="small text-muted">
                            <li>Lecture courses: 3-4 credits</li>
                            <li>Lab courses: 1-2 credits</li>
                            <li>Project courses: 2-6 credits</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">Course Types</h6>
                        <ul class="small text-muted">
                            <li><strong>Core:</strong> Required for all students</li>
                            <li><strong>Elective:</strong> Optional courses</li>
                            <li><strong>Lab:</strong> Practical/hands-on courses</li>
                            <li><strong>Project:</strong> Research/capstone courses</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- System Overview -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-chart-bar me-2"></i>System Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ \App\Models\Course::count() }}</h4>
                                <small class="text-muted">Total Courses</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ \App\Models\Course::where('status', 'active')->count() }}</h4>
                            <small class="text-muted">Active Courses</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-info mb-0">{{ \App\Models\Department::where('is_active', true)->count() }}</h4>
                                <small class="text-muted">Departments</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning mb-0">{{ \App\Models\User::where('role', 'faculty')->where('is_active', true)->count() }}</h4>
                            <small class="text-muted">Faculty</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border-bottom: none;
}

.form-control, .form-select {
    border-radius: 6px;
}

.btn {
    border-radius: 6px;
}

.text-danger {
    font-weight: 500;
}

.border-bottom {
    border-color: #dee2e6 !important;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endsection
