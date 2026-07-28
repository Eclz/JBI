@extends('layouts.app')

@section('title', $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $course->name }}</h1>
                    <p class="text-muted">{{ $course->code }} • {{ $course->credits }} Credits</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Course
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                    </a>
                </div>
            </div>

            <!-- Course Information -->
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Course Details Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-book me-2"></i>Course Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Course Code</h6>
                                    <p class="mb-3">{{ $course->code }}</p>

                                    <h6 class="text-primary">Department</h6>
                                    <p class="mb-3">
                                        @if($course->department)
                                            <span class="badge bg-info">{{ $course->department->name }}</span>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </p>

                                    <h6 class="text-primary">Credits</h6>
                                    <p class="mb-3">{{ $course->credits }} Credits</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">Semester</h6>
                                    <p class="mb-3">
                                        @if($course->semester)
                                            {{ $course->semester->name }} ({{ $course->semester->academic_year }})
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </p>

                                    <h6 class="text-primary">Instructor</h6>
                                    <p class="mb-3">
                                        @if($course->instructor)
                                            <a href="{{ route('admin.faculty-staff.show', $course->instructor) }}" class="text-decoration-none">
                                                {{ $course->instructor->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </p>

                                    <h6 class="text-primary">Status</h6>
                                    <p class="mb-3">
                                        @if($course->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($course->description)
                            <div class="mt-3">
                                <h6 class="text-primary">Description</h6>
                                <p class="text-muted">{{ $course->description }}</p>
                            </div>
                            @endif

                            @if($course->prerequisites)
                            <div class="mt-3">
                                <h6 class="text-primary">Prerequisites</h6>
                                <p class="text-muted">{{ $course->prerequisites }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Enrolled Students -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>Enrolled Students ({{ $course->enrollments->count() }})
                            </h5>
                            <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i>Manage Enrollments
                            </a>
                        </div>
                        <div class="card-body">
                            @if($course->enrollments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Admission Number</th>
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($course->enrollments->take(10) as $enrollment)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            {{ substr($enrollment->student->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $enrollment->student->name }}</h6>
                                                            <small class="text-muted">{{ $enrollment->student->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($enrollment->student->studentProfile)
                                                        <code>{{ $enrollment->student->studentProfile->admission_number }}</code>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                                <td>
                                                    @switch($enrollment->status)
                                                        @case('enrolled')
                                                            <span class="badge bg-success">Enrolled</span>
                                                            @break
                                                        @case('dropped')
                                                            <span class="badge bg-danger">Dropped</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge bg-primary">Completed</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.students.show', $enrollment->student) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($course->enrollments->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-outline-primary">
                                        View All {{ $course->enrollments->count() }} Students
                                    </a>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Students Enrolled</h5>
                                    <p class="text-muted">This course doesn't have any enrolled students yet.</p>
                                    <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Enroll Students
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Course Materials -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt me-2"></i>Course Materials
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($course->materials && $course->materials->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($course->materials->take(5) as $material)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $material->title }}</h6>
                                            <small class="text-muted">{{ $material->type }} • {{ $material->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Materials Available</h5>
                                    <p class="text-muted">Course materials will appear here when uploaded.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Stats -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-dark text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Course Statistics
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h3 class="text-primary mb-0">{{ $course->enrollments->count() }}</h3>
                                        <small class="text-muted">Students</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-success mb-0">{{ $course->assignments ? $course->assignments->count() : 0 }}</h3>
                                    <small class="text-muted">Assignments</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h3 class="text-info mb-0">{{ $course->materials ? $course->materials->count() : 0 }}</h3>
                                        <small class="text-muted">Materials</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-warning mb-0">{{ $course->credits }}</h3>
                                    <small class="text-muted">Credits</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Actions -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Course Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-users me-2"></i>Manage Enrollments
                                </a>
                                <a href="{{ route('admin.courses.materials', $course) }}" class="btn btn-outline-info">
                                    <i class="fas fa-file-alt me-2"></i>Course Materials
                                </a>
                                <a href="{{ route('admin.courses.assignments', $course) }}" class="btn btn-outline-success">
                                    <i class="fas fa-tasks me-2"></i>Assignments
                                </a>
                                <a href="{{ route('admin.courses.grades', $course) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-chart-line me-2"></i>Grade Reports
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Course Information -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Course Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary">Created</h6>
                                <p class="mb-2 small">{{ $course->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-primary">Last Updated</h6>
                                <p class="mb-2 small">{{ $course->updated_at->diffForHumans() }}</p>
                            </div>

                            @if($course->max_students)
                            <div class="mb-3">
                                <h6 class="text-primary">Enrollment Limit</h6>
                                <p class="mb-2">
                                    {{ $course->enrollments->count() }} / {{ $course->max_students }} students
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar"
                                             style="width: {{ ($course->enrollments->count() / $course->max_students) * 100 }}%">
                                        </div>
                                    </div>
                                </p>
                            </div>
                            @endif

                            @if($course->course_type)
                            <div class="mb-3">
                                <h6 class="text-primary">Course Type</h6>
                                <p class="mb-2">
                                    <span class="badge bg-secondary">{{ ucfirst($course->course_type) }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    border-bottom: none;
}

.btn {
    border-radius: 6px;
}

.progress {
    border-radius: 10px;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection
