@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <!-- Fixed text visibility by using solid background and ensuring proper contrast -->
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2" style="color: #ffffff; font-weight: 600;">
                                Welcome back, {{ $student->first_name }}! 👋
                            </h3>
                            <p class="mb-0" style="color: rgba(255, 255, 255, 0.9); font-size: 0.95rem;">
                                @if($studentProfile)
                                    {{ $studentProfile->program }}
                                    @if($studentProfile->department)
                                        - {{ $studentProfile->department->name }}
                                    @endif
                                    | Semester {{ $studentProfile->current_semester ?? 1 }}
                                @else
                                    Student Dashboard
                                @endif
                            </p>
                        </div>
                        @if($studentProfile)
                        <div class="text-end">
                            <div class="fs-6" style="color: rgba(255, 255, 255, 0.8);">Admission Number</div>
                            <div class="fs-5 fw-bold mb-2" style="color: #ffffff;">{{ $studentProfile->admission_number ?? 'N/A' }}</div>
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('student.admission-letter.show') }}" class="btn btn-sm btn-light text-primary fw-bold shadow-sm" style="border-radius: 6px;">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i>Admission Letter
                                </a>
                                <a href="{{ route('student.dashboard') }}?view=admission" class="btn btn-sm btn-outline-light" style="border-radius: 6px;">
                                    <i class="bi bi-mortarboard me-1"></i>Admission Status
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($currentSemester && !$hasCurrentEnrollment && ($registrationOpen || $currentSemester->is_active))
        <div class="modal fade" id="enrollmentPromptModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">New Semester Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Enrollment is open for {{ $currentSemester->name }}.</p>
                        <p class="text-muted mb-0">Please enroll in your course units for this semester.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('student.courses.enrollments') }}" class="btn btn-primary">Enroll Now</a>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Later</button>
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('enrollmentPromptModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
        </script>
        @endpush
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Enrolled Courses -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <!-- Added explicit text color for better visibility -->
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Enrolled Courses
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700;">{{ $courseStats['total_courses'] }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-book fs-4" style="color: #0d6efd;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current GPA -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Current GPA
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700;">{{ number_format($currentGPA, 2) }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-graph-up fs-4" style="color: #198754;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Attendance
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700;">{{ $attendanceRate }}%</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(13, 202, 240, 0.1);">
                            <i class="bi bi-calendar-check fs-4" style="color: #0dcaf0;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Assignments -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Pending Assignments
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700;">{{ $pendingAssignments }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(255, 193, 7, 0.1);">
                            <i class="bi bi-clipboard-check fs-4" style="color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Need to change your program?</h5>
                        <p class="mb-0 text-muted">Submit a request and track the approval status.</p>
                    </div>
                    <a href="{{ route('student.program-changes.index') }}" class="btn btn-outline-primary">
                        Request Program Change
                    </a>
                </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Added explicit text colors -->
                        <h5 class="mb-0" style="color: #212529; font-weight: 600;">My Courses</h5>
                        <a href="{{ route('student.courses.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($enrolledCourses->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-book fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No courses enrolled yet</p>
                            <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                                Browse Courses
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="color: #495057; font-weight: 600;">Course</th>
                                        <th style="color: #495057; font-weight: 600;">Code</th>
                                        <th style="color: #495057; font-weight: 600;">Instructor</th>
                                        <th style="color: #495057; font-weight: 600;">Credits</th>
                                        <th style="color: #495057; font-weight: 600;">Status</th>
                                        <th style="color: #495057; font-weight: 600;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledCourses as $enrollment)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold" style="color: #212529;">{{ $enrollment->course->name }}</div>
                                                @if($enrollment->course->department)
                                                    <small class="text-muted">{{ $enrollment->course->department->name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: #e9ecef; color: #495057;">{{ $enrollment->course->code }}</span>
                                            </td>
                                            <td style="color: #495057;">
                                                @if($enrollment->course->instructor)
                                                    {{ $enrollment->course->instructor->name }}
                                                @else
                                                    <span class="text-muted">TBA</span>
                                                @endif
                                            </td>
                                            <td style="color: #495057;">{{ $enrollment->course->credits }}</td>
                                            <td>
                                                <span class="badge bg-success">Enrolled</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('student.courses.show', $enrollment->course->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Assignments -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: #212529; font-weight: 600;">Upcoming Assignments</h5>
                        <a href="{{ route('student.assignments.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingAssignments->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-check fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No upcoming assignments</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($upcomingAssignments as $assignment)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $assignment->title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $assignment->course->name }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3"></i>
                                                Due: {{ $assignment->due_date->format('M d, Y h:i A') }}
                                            </small>
                                        </div>
                                        <div class="text-end ms-3">
                                            @php
                                                $daysUntilDue = now()->diffInDays($assignment->due_date, false);
                                            @endphp
                                            @if($daysUntilDue < 1)
                                                <span class="badge bg-danger">Due Soon</span>
                                            @elseif($daysUntilDue < 3)
                                                <span class="badge bg-warning">{{ ceil($daysUntilDue) }} days</span>
                                            @else
                                                <span class="badge bg-info">{{ ceil($daysUntilDue) }} days</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Grades -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: #212529; font-weight: 600;">Recent Grades</h5>
                        <a href="{{ route('student.grades.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentGrades->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-trophy fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No grades yet</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Assignment</th>
                                        <th>Course</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentGrades as $grade)
                                        <tr>
                                            <td>{{ $grade->assignment->title ?? 'N/A' }}</td>
                                            <td>{{ $grade->course->code ?? 'N/A' }}</td>
                                            <td>{{ $grade->points_earned }} / {{ $grade->max_points }}</td>
                                            <td>
                                                @php
                                                    $percentage = $grade->max_points > 0 ? ($grade->points_earned / $grade->max_points) * 100 : 0;
                                                    $badgeClass = $percentage >= 90 ? 'success' : ($percentage >= 80 ? 'primary' : ($percentage >= 70 ? 'warning' : 'danger'));
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">
                                                    {{ number_format($percentage, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Attendance by Course -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Attendance by Course</h5>
                </div>
                <div class="card-body">
                    @if($attendanceByCourse->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No attendance records</p>
                        </div>
                    @else
                        @foreach($attendanceByCourse as $attendance)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small fw-semibold">{{ Str::limit($attendance['course'], 30) }}</span>
                                    <span class="small fw-semibold">{{ $attendance['percentage'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $progressClass = $attendance['percentage'] >= 90 ? 'success' : ($attendance['percentage'] >= 75 ? 'primary' : ($attendance['percentage'] >= 60 ? 'warning' : 'danger'));
                                    @endphp
                                    <div class="progress-bar bg-{{ $progressClass }}"
                                         role="progressbar"
                                         style="width: {{ $attendance['percentage'] }}%"
                                         aria-valuenow="{{ $attendance['percentage'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $attendance['present'] }} / {{ $attendance['total'] }} classes</small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Fee Information -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Fee Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Fees:</span>
                            <span class="fw-bold">{{ $currencyCode }} {{ number_format($totalFees, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Paid:</span>
                            <span class="text-success fw-bold">{{ $currencyCode }} {{ number_format($paidFees, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Pending:</span>
                            <span class="text-danger fw-bold">{{ $currencyCode }} {{ number_format($pendingFees, 2) }}</span>
                        </div>
                    </div>

                    @if($totalFees > 0)
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success"
                                 role="progressbar"
                                 style="width: {{ ($paidFees / $totalFees) * 100 }}%"
                                 aria-valuenow="{{ ($paidFees / $totalFees) * 100 }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <p class="small text-muted mb-3">{{ number_format(($paidFees / $totalFees) * 100, 1) }}% paid</p>
                    @endif

                    <a href="{{ route('student.fees.index') }}" class="btn btn-primary w-100">
                        View Fee Details
                    </a>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Recent Notifications</h5>
                </div>
                <div class="card-body p-0">
                    @if($unreadNotifications->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No new notifications</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($unreadNotifications as $notification)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small">{{ Str::limit($notification->message, 80) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
