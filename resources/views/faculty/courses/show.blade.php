@extends('layouts.app')

@section('title', $course->name . ' - Course Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-dark fw-bold">{{ $course->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('faculty.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('faculty.courses.index') }}">My Courses</a></li>
                            <li class="breadcrumb-item active">{{ $course->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('faculty.courses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>
                </div>
            </div>

            <ul class="nav nav-tabs mb-4 border-bottom-0" id="courseTab" role="tablist" style="background: white; border-radius: 8px; padding: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="bi bi-info-circle me-2"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">
                        <i class="bi bi-people me-2"></i> Students
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab">
                        <i class="bi bi-file-earmark-text me-2"></i> Assignments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">
                        <i class="bi bi-clipboard-check me-2"></i> Exams
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" type="button" role="tab">
                        <i class="bi bi-question-circle me-2"></i> Quizzes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                        <i class="bi bi-folder me-2"></i> Materials
                    </button>
                </li>
            </ul>

            <style>
                .nav-tabs .nav-link {
                    transition: all 0.3s ease;
                    color: #6B7280;
                    border: none;
                    font-weight: 500;
                    padding: 12px 20px;
                }
                .nav-tabs .nav-link:hover {
                    background-color: #F3F4F6;
                    border-radius: 6px;
                }
                .nav-tabs .nav-link.active {
                    color: #4F46E5 !important;
                    background-color: #EEF2FF;
                    border-radius: 6px;
                }
            </style>

            <div class="tab-content" id="courseTabContent">
                {{-- Overview Tab --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-3">{{ $course->name }}</h5>
                                    <p class="text-muted mb-2"><strong>Code:</strong> {{ $course->code }}</p>
                                    <p class="text-muted mb-2"><strong>Credits:</strong> {{ $course->credits }}</p>
                                    <p class="text-muted mb-2"><strong>Department:</strong> {{ $course->department->name ?? 'N/A' }}</p>
                                    <p class="text-muted mb-2"><strong>Semester:</strong> {{ $course->semester->name ?? 'N/A' }}</p>
                                    <p class="mt-3">{{ $course->description }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Quick Stats</h6>
                                            <div class="mb-2">
                                                <small class="text-muted">Enrolled Students</small>
                                                <div class="h4 mb-0">{{ $course->enrollments->where('status', 'enrolled')->count() }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Assignments</small>
                                                <div class="h4 mb-0">{{ $course->assignments->count() }}</div>
                                            </div>
                                            <div>
                                                <small class="text-muted">Materials</small>
                                                <div class="h4 mb-0">{{ $course->materials->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Students Tab --}}
                <div class="tab-pane fade" id="students" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Enrolled Students</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Enrolled Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($course->enrollments->where('status', 'enrolled') as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->student->student_id ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->student->name }}</td>
                                            <td>{{ $enrollment->student->email }}</td>
                                            <td><span class="badge bg-success">{{ ucfirst($enrollment->status) }}</span></td>
                                            <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View Profile</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No students enrolled yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assignments Tab --}}
                <div class="tab-pane fade" id="assignments" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Assignments</h5>
                            <a href="{{ route('faculty.assignments.create', $course) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Assignment
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Due Date</th>
                                            <th>Total Points</th>
                                            <th>Submissions</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($course->assignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->title }}</td>
                                            <td>{{ $assignment->due_date->format('M d, Y H:i') }}</td>
                                            <td>{{ $assignment->total_points }}</td>
                                            <td>{{ $assignment->submissions->count() }}</td>
                                            <td>
                                                @if($assignment->due_date->isFuture())
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Closed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('faculty.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No assignments created yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Exams Tab --}}
                <div class="tab-pane fade" id="exams" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Exams</h5>
                            <a href="{{ route('faculty.exams.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Exam
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Manage exams for this course. You can create, edit, and view exam results.</p>
                            <a href="{{ route('faculty.exams.index') }}?course={{ $course->id }}" class="btn btn-outline-primary">
                                View All Exams
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Quizzes Tab --}}
                <div class="tab-pane fade" id="quizzes" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quizzes</h5>
                            <a href="{{ route('faculty.quizzes.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Quiz
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Manage quizzes for this course. Create quick assessments to test student understanding.</p>
                            <a href="{{ route('faculty.quizzes.index') }}?course={{ $course->id }}" class="btn btn-outline-primary">
                                View All Quizzes
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Materials Tab --}}
                <div class="tab-pane fade" id="materials" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Course Materials</h5>
                            <a href="{{ route('faculty.courses.materials.create', $course) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Upload Material
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @forelse($course->materials as $material)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $material->title }}</h6>
                                            <p class="card-text text-muted small">{{ $material->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $material->created_at->format('M d, Y') }}</small>
                                                <a href="{{ $material->file_path }}" class="btn btn-sm btn-outline-primary" download>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-center text-muted py-4">No materials uploaded yet</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggerTabList = document.querySelectorAll('#courseTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })
})
</script>
@endsection
