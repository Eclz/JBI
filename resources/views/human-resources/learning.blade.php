@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Learning & Development</h2>
            <p class="text-muted mb-0">Manage training courses and track employee certifications.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                <i class="bi bi-book me-1"></i> Add Course
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#enrollUserModal">
                <i class="bi bi-person-workspace me-1"></i> Enroll Employee
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Course Catalog</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['active_courses'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Active Learners</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['active_learners'] }}</h3>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-warning fs-4">
                            <i class="bi bi-laptop"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Completed Trainings</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['completed_trainings'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-award-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Training Hours</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_hours'], 1) }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-stopwatch"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Course Catalog -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom">
                    <h5 class="mb-0 fw-bold">Training Courses</h5>
                </div>
                <div class="card-body p-0">
                    @if($courses->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <p class="mb-2">No courses added yet.</p>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">Create Course</button>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($courses as $course)
                                <div class="list-group-item p-3 border-bottom">
                                    <h6 class="mb-1 fw-bold text-primary">{{ $course->title }}</h6>
                                    <div class="text-muted small mb-2">{{ $course->provider ?? 'Internal' }}</div>
                                    
                                    <div class="d-flex gap-2 text-muted small">
                                        <span><i class="bi bi-clock me-1"></i> {{ $course->duration_hours }}h</span>
                                        <span>•</span>
                                        <span><i class="bi bi-currency-dollar"></i> {{ $course->cost > 0 ? number_format($course->cost, 2) : 'Free' }}</span>
                                        <span>•</span>
                                        <span><i class="bi bi-people me-1"></i> {{ $course->enrollments_count }} Enrolled</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enrollments Tracker -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pt-4 pb-3 border-bottom">
                    <h5 class="mb-0 fw-bold">Employee Enrollments</h5>
                </div>
                <div class="card-body p-0">
                    @if($enrollments->isEmpty())
                        <div class="p-5 text-center text-muted flex-grow-1">
                            <i class="bi bi-mortarboard fs-1 mb-3 d-block opacity-50"></i>
                            <h6>No employees are currently enrolled in training.</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 px-4 border-0">Employee</th>
                                        <th class="py-3 px-4 border-0">Course</th>
                                        <th class="py-3 px-4 border-0">Enrolled On</th>
                                        <th class="py-3 px-4 border-0">Status</th>
                                        <th class="py-3 px-4 border-0 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $enrollment->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($enrollment->user->name).'&background=random' }}" alt="{{ $enrollment->user->name }}" class="rounded-circle me-3" style="width: 35px; height: 35px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0">{{ $enrollment->user->name }}</h6>
                                                        <small class="text-muted">{{ $enrollment->user->hrProfile->department ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 fw-semibold text-primary">
                                                {{ $enrollment->course->title }}
                                            </td>
                                            <td class="px-4 text-muted small">
                                                {{ $enrollment->enrollment_date->format('d M Y') }}
                                            </td>
                                            <td class="px-4">
                                                @if($enrollment->status === 'Enrolled')
                                                    <span class="badge bg-light text-dark border">Enrolled</span>
                                                @elseif($enrollment->status === 'In Progress')
                                                    <span class="badge bg-warning text-dark">In Progress</span>
                                                @elseif($enrollment->status === 'Completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $enrollment->status }}</span>
                                                @endif
                                                
                                                @if($enrollment->completion_date)
                                                    <div class="text-muted small mt-1" style="font-size: 0.65rem;">{{ $enrollment->completion_date->format('d M y') }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#updateEnrollmentModal{{ $enrollment->id }}">
                                                    Update
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Update Enrollment Modal -->
                                        <div class="modal fade" id="updateEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('human-resources.learning.enrollments.update', $enrollment->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Enrollment Status</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h6 class="mb-1">{{ $enrollment->user->name }}</h6>
                                                            <p class="text-muted small mb-3">{{ $enrollment->course->title }}</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Training Status</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="Enrolled" {{ $enrollment->status == 'Enrolled' ? 'selected' : '' }}>Enrolled (Not Started)</option>
                                                                    <option value="In Progress" {{ $enrollment->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                                    <option value="Completed" {{ $enrollment->status == 'Completed' ? 'selected' : '' }}>Completed (Passed)</option>
                                                                    <option value="Failed" {{ $enrollment->status == 'Failed' ? 'selected' : '' }}>Failed / Did Not Complete</option>
                                                                    <option value="Cancelled" {{ $enrollment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save Status</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.learning.courses.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Training Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Course Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Advanced Leadership Skills">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provider/Platform (Optional)</label>
                        <input type="text" name="provider" class="form-control" placeholder="e.g. Coursera, Udemy, Internal">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Duration (Hours)</label>
                            <input type="number" name="duration_hours" class="form-control" required step="0.5" min="0.5" value="1.0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Cost per User ($)</label>
                            <input type="number" name="cost" class="form-control" required step="0.01" min="0" value="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="What will they learn?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enroll User Modal -->
<div class="modal fade" id="enrollUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.learning.enroll') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Enroll Employee in Training</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->hrProfile->department ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Training Course</label>
                        <select name="hr_training_course_id" class="form-select" required>
                            <option value="">-- Select Course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->duration_hours }}h)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enrollment Date</label>
                        <input type="date" name="enrollment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Enroll Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
