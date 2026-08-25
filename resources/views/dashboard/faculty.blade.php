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
                        <h2 class="text-white mb-1">{{ count($myCourses ?? []) }}</h2>
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
                        <h2 class="text-white mb-1">{{ $totalStudents ?? 127 }}</h2>
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
                        <h2 class="text-white mb-1">{{ $pendingGrades ?? 23 }}</h2>
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
                        <h6 class="text-white-50 mb-1">Class Average</h6>
                        <h2 class="text-white mb-1">{{ $classAverage ?? 87 }}%</h2>
                        <small class="text-white-50">Above target</small>
                    </div>
                    <div class="icon-wrapper" style="background: rgba(255,255,255,0.1); color: white;">
                        <i class="fa fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Distribution and Schedule -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card card-hover h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1" style="color: var(--jbi-navy);">Grade Distribution</h5>
                            <p class="text-muted mb-0">Current semester grade breakdown</p>
                        </div>
                        <div class="icon-wrapper icon-primary">
                            <i class="fa fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach([
                        ['grade' => 'A', 'count' => 45, 'percentage' => 35, 'color' => '#22c55e'],
                        ['grade' => 'B', 'count' => 52, 'percentage' => 41, 'color' => '#3b82f6'],
                        ['grade' => 'C', 'count' => 23, 'percentage' => 18, 'color' => '#f59e0b'],
                        ['grade' => 'D', 'count' => 6, 'percentage' => 5, 'color' => '#ef4444'],
                        ['grade' => 'F', 'count' => 1, 'percentage' => 1, 'color' => '#6b7280']
                    ] as $grade)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3" style="background: {{ $grade['color'] }}20; color: {{ $grade['color'] }}; width: 40px; height: 40px;">
                                    <strong>{{ $grade['grade'] }}</strong>
                                </div>
                                <span class="fw-medium">{{ $grade['count'] }} students</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="progress-bar me-3" style="width: 100px;">
                                    <div class="progress-fill" style="width: {{ $grade['percentage'] }}%; background: {{ $grade['color'] }};"></div>
                                </div>
                                <span class="fw-bold" style="color: var(--jbi-navy);">{{ $grade['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card card-hover h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1" style="color: var(--jbi-navy);">Today's Schedule</h5>
                            <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }}</p>
                        </div>
                        <div class="icon-wrapper icon-accent">
                            <i class="fa fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach([
                        ['course' => 'Biblical Studies 101', 'room' => 'Room 204', 'time' => '11:00 AM - 12:30 PM', 'students' => 32, 'status' => 'upcoming'],
                        ['course' => 'Theology 301', 'room' => 'Room 156', 'time' => '2:00 PM - 3:30 PM', 'students' => 28, 'status' => 'later']
                    ] as $class)
                        <div class="d-flex align-items-center mb-3 p-3 rounded" style="background: {{ $class['status'] === 'upcoming' ? '#f0fdf4' : '#f8fafc' }};">
                            <div class="icon-wrapper {{ $class['status'] === 'upcoming' ? 'icon-success' : 'icon-primary' }} me-3" style="width: 40px; height: 40px;">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="color: var(--jbi-navy);">{{ $class['course'] }}</h6>
                                <small class="text-muted">{{ $class['room'] }} - {{ $class['students'] }} students</small>
                                <br>
                                <small class="fw-medium" style="color: {{ $class['status'] === 'upcoming' ? '#22c55e' : 'var(--jbi-primary)' }};">{{ $class['time'] }}</small>
                            </div>
                        </div>
                    @endforeach
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
                    <a href="" class="text-decoration-none">
                        {{-- {{ route('faculty.grading.index') }} --}}
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-warning mx-auto mb-3">
                                <i class="fa fa-star"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Grade Assignments</h6>
                            <p class="text-muted small mb-0">{{ $pendingGrades ?? 23 }} pending</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="" class="text-decoration-none">
                        {{-- {{ route('faculty.attendance.index') }} --}}
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
                    <a href="" class="text-decoration-none">
                        {{-- {{ route('assignments.create') }} --}}
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-primary mx-auto mb-3">
                                <i class="fa fa-plus"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Create Assignment</h6>
                            <p class="text-muted small mb-0">Add new assignment</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="" class="text-decoration-none">
                        {{-- {{ route('faculty.materials.index') }} --}}
                        <div class="quick-action-card text-center">
                            <div class="icon-wrapper icon-accent mx-auto mb-3">
                                <i class="fa fa-file-upload"></i>
                            </div>
                            <h6 style="color: var(--jbi-navy);">Course Materials</h6>
                            <p class="text-muted small mb-0">Upload resources</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
