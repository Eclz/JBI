@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="mb-0 opacity-75">Ready to continue your journey at JBI University?</p>
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
                        <h6 class="text-white-50 mb-1">Current GPA</h6>
                        <h2 class="text-white mb-1">{{ number_format($currentGPA ?? 0, 2) }}</h2>
                        <small class="text-success">
                            <i class="fa fa-arrow-up"></i> Excellent standing
                        </small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Enrolled Courses</h6>
                        <h2 class="text-white mb-1">{{ count($enrolledCourses ?? []) }}</h2>
                        <small class="text-white-50">This semester</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-book-open"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Attendance Rate</h6>
                        <h2 class="text-white mb-1">{{ number_format($attendanceRate ?? 0, 1) }}%</h2>
                        <small class="text-white-50">Above average</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Pending Assignments</h6>
                        <h2 class="text-white mb-1">{{ $pendingAssignments ?? 0 }}</h2>
                        <small class="text-white-50">Due soon</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress and Assignments -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card card-hover h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1" style="color: var(--jbi-navy);">Semester Progress</h5>
                            <p class="text-muted mb-0">Your current courses attendance</p>
                        </div>
                        <div class="icon-wrapper icon-primary">
                            <i class="fa fa-chart-line"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-3" style="color: var(--jbi-navy);">Course Performance (Attendance)</h6>
                    @forelse($attendanceByCourse ?? [] as $courseStats)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper icon-primary me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    <i class="fa fa-book"></i>
                                </div>
                                <span class="fw-medium">{{ $courseStats['course'] }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge me-2" style="background: var(--jbi-accent); color: white;">{{ $courseStats['present'] }}/{{ $courseStats['total'] }} Attended</span>
                                <small class="text-muted">{{ $courseStats['percentage'] }}%</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No course attendance data available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card card-hover h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1" style="color: var(--jbi-navy);">Upcoming Assignments</h5>
                            <p class="text-muted mb-0">Due dates and priorities</p>
                        </div>
                        <div class="icon-wrapper icon-warning">
                            <i class="fa fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($upcomingAssignments ?? [] as $assignment)
                        @php $urgent = \Carbon\Carbon::parse($assignment->due_date)->diffInDays(now()) <= 2; @endphp
                        <div class="d-flex align-items-center mb-3 p-3 rounded" style="background: {{ $urgent ? '#fef2f2' : '#f8fafc' }};">
                            <div class="icon-wrapper {{ $urgent ? 'icon-warning' : 'icon-primary' }} me-3" style="width: 40px; height: 40px;">
                                <i class="fa fa-{{ $urgent ? 'exclamation' : 'file-alt' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="color: var(--jbi-navy);">{{ $assignment->title }}</h6>
                                <small class="text-muted">{{ $assignment->course->course_code ?? 'Course' }} - Due {{ \Carbon\Carbon::parse($assignment->due_date)->diffForHumans() }}</small>
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
                    <p class="text-muted mb-0">Frequently used student tools</p>
                </div>
                <div class="icon-wrapper icon-primary">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('student.assignments.index') }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-primary mx-auto mb-3">
                                <i class="fa fa-tasks"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">View Assignments</h6>
                            <p class="text-muted small mb-0">{{ $pendingAssignments ?? 0 }} pending</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('student.grades.index') }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-success mx-auto mb-3">
                                <i class="fa fa-star"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Check Grades</h6>
                            <p class="text-muted small mb-0">GPA: {{ number_format($currentGPA ?? 0, 2) }}</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('student.courses.index') }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-accent mx-auto mb-3">
                                <i class="fa fa-book-open"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">My Courses</h6>
                            <p class="text-muted small mb-0">{{ count($enrolledCourses ?? []) }} enrolled</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('student.fees.index') }}" class="text-decoration-none">
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-warning mx-auto mb-3">
                                <i class="fa fa-dollar-sign"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Fee Status</h6>
                            <p class="text-muted small mb-0">
                                @if(($pendingFees ?? 0) > 0)
                                    ${{ number_format($pendingFees) }} due
                                @else
                                    Paid in full
                                @endif
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Balance Alert -->
    @if(($pendingFees ?? 0) > 0)
    <div class="alert alert-warning mt-4" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: none; border-radius: 12px;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper me-3" style="background: rgba(245, 158, 11, 0.2); color: var(--jbi-accent);">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="mb-1" style="color: var(--jbi-navy);">Outstanding Balance</h6>
                    <p class="mb-0 text-muted">You have a balance of ${{ number_format($pendingFees) }} due</p>
                </div>
            </div>
            <a href="{{ route('student.fees.index') }}" class="btn" style="background: var(--jbi-accent); color: white; border-radius: 8px;">
                Pay Now
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
