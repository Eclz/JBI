@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Course Edit Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-edit me-2"></i>Edit Course: {{ $course->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($course->enrollments()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This course has {{ $course->enrollments()->count() }} enrolled students.
                            Changes may affect existing enrollments.
                        </div>
                    @endif

                    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
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
                                           value="{{ old('name', $course->name) }}"
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
                                           value="{{ old('code', $course->code) }}"
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
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="fa fa-book me-2"></i>Course Details
                                </h6>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4">{{ old('description', $course->description) }}</textarea>
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
                                           value="{{ old('credits', $course->credits) }}"
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
                                           value="{{ old('max_students', $course->max_students) }}"
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
                                        <option value="core" {{ old('course_type', $course->course_type) == 'core' ? 'selected' : '' }}>Core</option>
                                        <option value="elective" {{ old('course_type', $course->course_type) == 'elective' ? 'selected' : '' }}>Elective</option>
                                        <option value="lab" {{ old('course_type', $course->course_type) == 'lab' ? 'selected' : '' }}>Lab</option>
                                        <option value="project" {{ old('course_type', $course->course_type) == 'project' ? 'selected' : '' }}>Project</option>
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
                                <h6 class="text-warning border-bottom pb-2 mb-3">
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
                                                    {{ old('department_id', $course->department_id) == $department->id ? 'selected' : '' }}>
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
                                                    {{ old('semester_id', $course->semester_id) == $semester->id ? 'selected' : '' }}>
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
                                                    {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Course Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="fa fa-cog me-2"></i>Settings
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prerequisites" class="form-label">Prerequisites</label>
                                    <textarea class="form-control @error('prerequisites') is-invalid @enderror"
                                              id="prerequisites"
                                              name="prerequisites"
                                              rows="3">{{ old('prerequisites', $course->prerequisites) }}</textarea>
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
                                        <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
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
                                           {{ old('is_elective', $course->is_elective) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_elective">
                                        This is an elective course
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-save me-2"></i>Update Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Course Information Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-info-circle me-2"></i>Course Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Current Status</h6>
                        <span class="badge bg-{{ $course->status == 'active' ? 'success' : ($course->status == 'inactive' ? 'danger' : 'warning') }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">Enrollment Statistics</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-success mb-0">{{ $course->enrollments()->where('status', 'enrolled')->count() }}</h4>
                                    <small class="text-muted">Enrolled</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-warning mb-0">{{ $course->max_students ?? 'Unlimited' }}</h4>
                                <small class="text-muted">Max Capacity</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary">Course Details</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Created:</strong> {{ $course->created_at->format('M d, Y') }}</li>
                            <li><strong>Last Updated:</strong> {{ $course->updated_at->format('M d, Y') }}</li>
                            <li><strong>Type:</strong> {{ ucfirst($course->course_type ?? 'Not specified') }}</li>
                            <li><strong>Credits:</strong> {{ $course->credits }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-eye me-2"></i>View Course Details
                        </a>
                        <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-outline-info btn-sm">
                            <i class="fa fa-users me-2"></i>Manage Enrollments
                        </a>
                        @if($course->status == 'active')
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleCourseStatus('inactive')">
                                <i class="fa fa-pause me-2"></i>Deactivate Course
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleCourseStatus('active')">
                                <i class="fa fa-play me-2"></i>Activate Course
                            </button>
                        @endif
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

.alert {
    border-radius: 8px;
}
</style>

<script>
function toggleCourseStatus(status) {
    if (confirm(`Are you sure you want to ${status === 'active' ? 'activate' : 'deactivate'} this course?`)) {
        // Create a form to submit the status change
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.courses.update", $course) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';

        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;

        // Copy all current form values
        const currentForm = document.querySelector('form');
        const formData = new FormData(currentForm);

        for (let [key, value] of formData.entries()) {
            if (key !== '_token' && key !== '_method' && key !== 'status') {
                const field = document.createElement('input');
                field.type = 'hidden';
                field.name = key;
                field.value = value;
                form.appendChild(field);
            }
        }

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
