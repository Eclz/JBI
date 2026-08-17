@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Student Assessment Dashboard</h1>
                    <p class="text-muted">Comprehensive overview of {{ $user->full_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Left Column - Profile & Basic Info -->
        <div class="col-lg-4 mb-4">
            <!-- Profile Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->profile_picture_url }}"
                             alt="Profile Picture"
                             class="rounded-circle border border-3 border-white shadow"
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <h4 class="mb-1">{{ $user->full_name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($user->role) }}</span>

                    @if($user->role === 'student' && $user->studentProfile)
                    <div class="mt-3 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-primary fw-bold fs-5">{{ $user->studentProfile->current_gpa ?? '0.00' }}</div>
                                <small class="text-muted">Current GPA</small>
                            </div>
                            <div class="col-6">
                                <div class="text-success fw-bold fs-5">{{ $user->studentProfile->progress_percentage ?? 0 }}%</div>
                                <small class="text-muted">Progress</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-lightning-charge text-warning"></i> Quick Actions</h6>
                </div>
                <div class="card-body p-2">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} w-100">
                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}-circle"></i>
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }} Account
                            </button>
                        </form>
                        @if($user->role === 'student')
                        <button class="btn btn-sm btn-outline-info w-100">
                            <i class="bi bi-envelope"></i> Send Message
                        </button>
                        <button class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-file-earmark-pdf"></i> Generate Report
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-person-lines-fill text-info"></i> Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Phone Number</label>
                        <div class="fw-medium">{{ $user->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Address</label>
                        <div class="fw-medium">{{ $user->address ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Emergency Contact</label>
                        <div class="fw-medium">{{ $user->emergency_contact ?? 'Not provided' }}</div>
                        @if($user->emergency_phone)
                        <small class="text-muted d-block">{{ $user->emergency_phone }}</small>
                        @endif
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Date of Birth</label>
                        <div class="fw-medium">{{ $user->date_of_birth ? $user->date_of_birth->format('F j, Y') : 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Detailed Information -->
        <div class="col-lg-8">
            @if($user->role === 'student' && $user->studentProfile)
            <!-- Academic Overview -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-mortarboard fs-1 mb-2"></i>
                            <h5 class="card-title">{{ $user->studentProfile->current_semester ?? 'N/A' }}</h5>
                            <p class="card-text small">Current Semester</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up fs-1 mb-2"></i>
                            <h5 class="card-title">{{ $user->studentProfile->cumulative_gpa ?? '0.00' }}</h5>
                            <p class="card-text small">Cumulative GPA</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-book fs-1 mb-2"></i>
                            <h5 class="card-title">{{ $user->studentProfile->total_credits_earned ?? 0 }}</h5>
                            <p class="card-text small">Credits Earned</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check fs-1 mb-2"></i>
                            <h5 class="card-title">85%</h5>
                            <p class="card-text small">Attendance Rate</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Progress -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-bar-chart text-primary"></i> Academic Progress</h6>
                    <small class="text-muted">{{ $user->studentProfile->program ?? 'N/A' }}</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Degree Progress</span>
                                    <span class="text-muted small">{{ $user->studentProfile->total_credits_earned ?? 0 }}/{{ $user->studentProfile->total_credits_required ?? 120 }} Credits</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $user->studentProfile->progress_percentage ?? 0 }}%"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Department</small>
                                    <div class="fw-medium">{{ $user->studentProfile->department->name ?? 'Not assigned' }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Expected Graduation</small>
                                    <div class="fw-medium">{{ $user->studentProfile->expected_graduation_date ? $user->studentProfile->expected_graduation_date->format('M Y') : 'TBD' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="position-relative d-inline-block">
                                <svg width="100" height="100" class="circular-progress">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#0d6efd" stroke-width="8"
                                            stroke-dasharray="{{ 2 * 3.14159 * 40 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 40 * (1 - ($user->studentProfile->progress_percentage ?? 0) / 100) }}"
                                            transform="rotate(-90 50 50)"/>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <div class="fw-bold text-primary">{{ $user->studentProfile->progress_percentage ?? 0 }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Courses -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-journal-bookmark text-success"></i> Current Enrollments</h6>
                </div>
                <div class="card-body">
                    @if($user->enrolledCourses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Credits</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->enrolledCourses->take(5) as $course)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $course->name }}</div>
                                        <small class="text-muted">{{ $course->code }}</small>
                                    </td>
                                    <td>{{ $course->instructor->full_name ?? 'TBA' }}</td>
                                    <td>{{ $course->credits }}</td>
                                    <td>
                                        <span class="badge bg-{{ $course->pivot->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($course->pivot->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($course->pivot->letter_grade)
                                        <span class="badge bg-primary">{{ $course->pivot->letter_grade }}</span>
                                        @else
                                        <span class="text-muted">In Progress</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-journal-x fs-1 mb-2"></i>
                        <p>No current course enrollments</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Fee Status -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-credit-card text-warning"></i> Fee Status</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="text-success fw-bold fs-4">$8,500</div>
                            <small class="text-muted">Total Paid</small>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="text-danger fw-bold fs-4">$1,500</div>
                            <small class="text-muted">Outstanding</small>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="text-primary fw-bold fs-4">$10,000</div>
                            <small class="text-muted">Total Fees</small>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">85% Paid</small>
                        <small class="text-muted">Due: Dec 31, 2024</small>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-clock-history text-info"></i> Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Assignment Submitted</h6>
                                <p class="text-muted small mb-1">Database Design Project - CS301</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Attended Class</h6>
                                <p class="text-muted small mb-1">Advanced Programming - CS401</p>
                                <small class="text-muted">1 day ago</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Fee Payment</h6>
                                <p class="text-muted small mb-1">Semester fee payment of $2,500</p>
                                <small class="text-muted">3 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application & Registration Profile -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-person text-primary"></i> Application & Registration Profile</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Program Applied For</p>
                            <p class="fw-medium mb-0">{{ $user->studentProfile->program ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Admission Number / Status</p>
                            <p class="fw-medium mb-0">
                                <span class="badge bg-secondary">{{ $user->studentProfile->admission_number ?? 'Pending' }}</span>
                                <span class="badge bg-info">{{ ucfirst($user->studentProfile->application_status ?? 'Submitted') }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-3 pt-3 border-top">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Previous School / Institution</p>
                            <p class="fw-medium mb-0">{{ $user->studentProfile->previous_school ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Previous GPA</p>
                            <p class="fw-medium mb-0">{{ $user->studentProfile->previous_gpa ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if(!empty($user->studentProfile->qualifications))
                    <div class="row mb-3 pt-3 border-top">
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Academic Qualifications & Test Scores</p>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach($user->studentProfile->qualifications as $key => $value)
                                    @if($value)
                                        <span class="badge bg-light text-dark border">
                                            {{ str_replace('_', ' ', ucfirst($key)) }}: 
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @elseif(is_bool($value))
                                                {{ $value ? 'Yes' : 'No' }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($user->studentProfile->guardian_name))
                    <div class="row mb-3 pt-3 border-top">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Guardian Details</p>
                            <p class="mb-0 fw-medium">{{ $user->studentProfile->guardian_name }}</p>
                            <small class="text-muted">{{ $user->studentProfile->guardian_phone }} | {{ $user->studentProfile->guardian_email ?? 'No email' }}</small>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Guardian Address</p>
                            <p class="mb-0 fw-medium">{{ $user->studentProfile->guardian_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($user->studentProfile->application_notes))
                    <div class="row mb-3 pt-3 border-top">
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Personal Statement / Application Notes</p>
                            <p class="mb-0 text-muted" style="white-space: pre-wrap;">{{ $user->studentProfile->application_notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($user->studentProfile->documents))
                    <div class="row pt-3 border-top">
                        <div class="col-12">
                            <p class="mb-2 text-muted small">Uploaded Supporting Documents</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($user->studentProfile->documents as $index => $doc)
                                    <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Document {{ $index + 1 }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($user->role === 'faculty' && $user->facultyProfile)
            <!-- Faculty Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-person-badge text-primary"></i> Faculty Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Employee ID</label>
                                <div class="fw-medium">{{ $user->facultyProfile->employee_id }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Department</label>
                                <div class="fw-medium">{{ $user->facultyProfile->department->name ?? 'Not assigned' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Position</label>
                                <div class="fw-medium">{{ $user->facultyProfile->position }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Hire Date</label>
                                <div class="fw-medium">{{ $user->facultyProfile->hire_date->format('F j, Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-3 border-top mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Specialization</p>
                            <p class="fw-medium mb-0">{{ $user->facultyProfile->specialization ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Application Status</p>
                            <p class="fw-medium mb-0">
                                <span class="badge bg-info">{{ ucfirst($user->facultyProfile->application_status ?? 'Submitted') }}</span>
                                <span class="badge bg-secondary">{{ ucfirst($user->facultyProfile->employment_status ?? 'Pending') }}</span>
                            </p>
                        </div>
                    </div>

                    @if(!empty($user->facultyProfile->qualifications))
                    <div class="row pt-3 border-top mb-3">
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Qualifications & Degrees</p>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach($user->facultyProfile->qualifications as $key => $value)
                                    @if($value)
                                        <span class="badge bg-light text-dark border">
                                            {{ str_replace('_', ' ', ucfirst($key)) }}: 
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @elseif(is_bool($value))
                                                {{ $value ? 'Yes' : 'No' }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($user->facultyProfile->experience))
                    <div class="row pt-3 border-top mb-3">
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Professional Experience & Research</p>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach($user->facultyProfile->experience as $key => $value)
                                    @if($value)
                                        <span class="badge bg-light text-dark border">
                                            {{ str_replace('_', ' ', ucfirst($key)) }}: 
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @elseif(is_bool($value))
                                                {{ $value ? 'Yes' : 'No' }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($user->facultyProfile->application_notes))
                    <div class="row pt-3 border-top mb-3">
                        <div class="col-12">
                            <p class="mb-1 text-muted small">Cover Letter / Application Notes</p>
                            <p class="mb-0 text-muted" style="white-space: pre-wrap;">{{ $user->facultyProfile->application_notes }}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($user->facultyProfile->documents))
                    <div class="row pt-3 border-top">
                        <div class="col-12">
                            <p class="mb-2 text-muted small">Uploaded Supporting Documents</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($user->facultyProfile->documents as $index => $doc)
                                    <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Document {{ $index + 1 }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 20px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    font-size: 0.9rem;
}

.circular-progress {
    transform: rotate(-90deg);
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>
@endsection
