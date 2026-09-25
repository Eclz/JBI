@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="mb-0 opacity-75">Inspiring minds and shaping futures at JBI University</p>
            </div>
            <div class="col-md-4 text-right">
                <div class="d-flex align-items-center justify-content-end">
                    <img src="{{ asset('images/jbi-logo-white.webp') }}" alt="JBI University Logo" style="height: 60px; opacity: 0.9;">
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Teaching Courses</h6>
                        <h2 class="text-white mb-1">{{ $totalCourses ?? 0 }}</h2>
                        <small class="text-white-50">This semester</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Total Students</h6>
                        <h2 class="text-white mb-1">{{ $totalStudents ?? 0 }}</h2>
                        <small class="text-white-50">Across all courses</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Pending Grades</h6>
                        <h2 class="text-white mb-1">{{ $pendingAssignments ?? 0 }}</h2>
                        <small class="text-white-50">Assignments to grade</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Today's Attendance</h6>
                        <h2 class="text-white mb-1">{{ $todayAttendance ?? 0 }}</h2>
                        <small class="text-white-50">Records marked today</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-check-square"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions and Schedule -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card card-hover h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1" style="color: var(--jbi-navy);">Recent Submissions</h5>
                            <p class="text-muted mb-0">Latest student assignment submissions</p>
                        </div>
                        <div class="icon-wrapper icon-primary">
                            <i class="fa fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Assignment</th>
                                    <th>Course</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSubmissions ?? [] as $submission)
                                    <tr>
                                        <td>{{ $submission->student->name ?? 'N/A' }}</td>
                                        <td>{{ $submission->assignment->title ?? 'N/A' }}</td>
                                        <td>{{ $submission->assignment->course->course_code ?? 'N/A' }}</td>
                                        <td>{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->diffForHumans() : 'N/A' }}</td>
                                        <td>
                                            @if($submission->score !== null)
                                                <span class="badge bg-success">Graded</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No recent submissions</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card card-hover h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1" style="color: var(--jbi-navy);">Upcoming Assignments</h5>
                            <p class="text-muted mb-0">Due soon</p>
                        </div>
                        <div class="icon-wrapper icon-accent">
                            <i class="fa fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($upcomingAssignments ?? [] as $assignment)
                        <div class="d-flex align-items-center mb-3 p-3 rounded" style="background: #f8fafc;">
                            <div class="icon-wrapper icon-primary me-3" style="width: 40px; height: 40px;">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="color: var(--jbi-navy);">{{ $assignment->title }}</h6>
                                <small class="text-muted">{{ $assignment->course->course_code ?? 'Course' }}</small>
                                <br>
                                <small class="fw-medium text-danger">Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, g:i A') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No upcoming assignments.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card card-hover">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1" style="color: var(--jbi-navy);">Quick Actions</h5>
                    <p class="text-muted mb-0">Frequently used faculty tools</p>
                </div>
                <div class="icon-wrapper icon-primary">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('faculty.grading.index') ?? '#' }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-warning mx-auto mb-3">
                                <i class="fa fa-star"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Grade Assignments</h6>
                            <p class="text-muted small mb-0">{{ $pendingAssignments ?? 0 }} pending</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('faculty.attendance.index') ?? '#' }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-success mx-auto mb-3">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Take Attendance</h6>
                            <p class="text-muted small mb-0">Mark today's attendance</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('faculty.assignments.index') ?? '#' }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-primary mx-auto mb-3">
                                <i class="fa fa-plus"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Assignments</h6>
                            <p class="text-muted small mb-0">Manage assignments</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('faculty.courses.index') ?? '#' }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-accent mx-auto mb-3">
                                <i class="fa fa-book-open"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">My Courses</h6>
                            <p class="text-muted small mb-0">Manage your courses</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
