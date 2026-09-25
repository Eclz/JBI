@extends('layouts.app')

@section('title', 'Faculty LMS Dashboard')

@section('content')
<div class="container-fluid py-4 px-4">
    <!-- Welcome Banner -->
    <div class="rounded-4 mb-4 text-white p-4" style="background-color: #001d48; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2;">
            <h1 class="fw-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="mb-0 text-white-50 fs-5">Inspiring minds and shaping futures at JBI University</p>
        </div>
        <img src="{{ asset('images/logo.png') }}" alt="JBI Logo" style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); height: 80px; opacity: 0.9; z-index: 1;" onerror="this.style.display='none'">
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <!-- Teaching Courses -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100 text-white" style="background-color: #0f172a;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <h6 class="text-white-50 mb-1">Teaching Courses</h6>
                    <h2 class="fw-bold mb-1 display-5">{{ $totalCourses }}</h2>
                    <p class="mb-0 text-white-50 small">This semester</p>
                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center" style="right: 20px; top: 50%; transform: translateY(-50%); width: 60px; height: 60px; background-color: rgba(255,255,255,0.1);">
                        <i class="bi bi-easel text-white fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Students -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100 text-white" style="background-color: #d89b00;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <h6 class="text-white-50 mb-1">Total Students</h6>
                    <h2 class="fw-bold mb-1 display-5">{{ $totalStudents }}</h2>
                    <p class="mb-0 text-white-50 small">Across all courses</p>
                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center" style="right: 20px; top: 50%; transform: translateY(-50%); width: 60px; height: 60px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-mortarboard text-white fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Grades -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100 text-white" style="background-color: #ef4444;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <h6 class="text-white-50 mb-1">Pending Grades</h6>
                    <h2 class="fw-bold mb-1 display-5">{{ $pendingAssignments }}</h2>
                    <p class="mb-0 text-white-50 small">Assignments to grade</p>
                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center" style="right: 20px; top: 50%; transform: translateY(-50%); width: 60px; height: 60px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-star text-white fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Today's Attendance -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 h-100 text-white" style="background-color: #22c55e;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <h6 class="text-white-50 mb-1">Today's Attendance</h6>
                    <h2 class="fw-bold mb-1 display-5">{{ $todayAttendance }}</h2>
                    <p class="mb-0 text-white-50 small">Records marked today</p>
                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center" style="right: 20px; top: 50%; transform: translateY(-50%); width: 60px; height: 60px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-check2-square text-white fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Recent Submissions -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm h-100" style="border: 1px solid #e2e8f0 !important; border-top: 4px solid #d89b00 !important;">
                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-primary mb-0" style="color: #001d48 !important;">Recent Submissions</h5>
                        <small class="text-muted">Latest student assignment submissions</small>
                    </div>
                    <div class="bg-light rounded p-2">
                        <i class="bi bi-file-earmark-text text-secondary fs-5"></i>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-top mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 border-0 py-3">Student</th>
                                    <th class="border-0 py-3">Assignment</th>
                                    <th class="border-0 py-3">Course</th>
                                    <th class="border-0 py-3">Submitted</th>
                                    <th class="px-4 border-0 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSubmissions as $submission)
                                    <tr>
                                        <td class="px-4 py-3 fw-semibold text-dark">{{ $submission->student->name }}</td>
                                        <td class="py-3 text-muted">{{ Str::limit($submission->assignment->title, 30) }}</td>
                                        <td class="py-3 text-muted">{{ $submission->assignment->course->code }}</td>
                                        <td class="py-3 text-muted">{{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('faculty.assignments.submissions', $submission->assignment) }}" class="btn btn-sm" style="background-color: #f1f5f9; color: #001d48; font-weight: 500;">Grade</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No recent submissions</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Assignments -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm h-100" style="border: 1px solid #e2e8f0 !important; border-top: 4px solid #001d48 !important;">
                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-primary mb-0" style="color: #001d48 !important;">Upcoming Assignments</h5>
                        <small class="text-muted">Due soon</small>
                    </div>
                    <div class="bg-light rounded p-2" style="background-color: #fef3c7 !important;">
                        <i class="bi bi-calendar-event" style="color: #d89b00;"></i>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush border-top">
                        @forelse($upcomingAssignments as $assignment)
                            <li class="list-group-item px-4 py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 text-dark fw-semibold">{{ $assignment->title }}</h6>
                                        <small class="text-muted">{{ $assignment->course->code }}</small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill">{{ \Carbon\Carbon::parse($assignment->due_date)->format('M d') }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item px-4 py-5 text-center text-muted border-0">
                                No upcoming assignments.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card border-0 rounded-4 shadow-sm mb-5" style="border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-primary mb-0" style="color: #001d48 !important;">Quick Actions</h5>
                <small class="text-muted">Frequently used faculty tools</small>
            </div>
            <div class="bg-light rounded p-2">
                <i class="bi bi-lightning-charge-fill" style="color: #001d48;"></i>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row text-center g-4">
                <div class="col-md-3">
                    <a href="{{ route('faculty.assignments.index') }}" class="text-decoration-none text-dark d-block">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: #fee2e2;">
                            <i class="bi bi-star-fill text-danger fs-4"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-primary" style="color: #001d48 !important;">Grade Assignments</h6>
                        <small class="text-muted">{{ $pendingAssignments }} pending</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('faculty.attendance.index') }}" class="text-decoration-none text-dark d-block border-start border-end">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: #dcfce7;">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-primary" style="color: #001d48 !important;">Take Attendance</h6>
                        <small class="text-muted">Mark today's attendance</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('faculty.assignments.index') }}" class="text-decoration-none text-dark d-block border-end">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: #f1f5f9;">
                            <i class="bi bi-plus text-primary fs-2" style="color: #001d48 !important;"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-primary" style="color: #001d48 !important;">Assignments</h6>
                        <small class="text-muted">Manage assignments</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('faculty.courses.index') }}" class="text-decoration-none text-dark d-block">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: #fef3c7;">
                            <i class="bi bi-book-fill fs-4" style="color: #d89b00;"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-primary" style="color: #001d48 !important;">My Courses</h6>
                        <small class="text-muted">Manage your courses</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- LMS Analytics Table -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
        <div>
            <h2 class="mb-1 fw-bold text-dark">LMS Analytics</h2>
            <p class="text-muted mb-0">Track student progress across your courses.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 border-0">Course</th>
                            <th class="py-3 border-0">Semester</th>
                            <th class="text-end py-3 border-0">Students</th>
                            <th class="text-end py-3 border-0">Avg Progress</th>
                            <th class="px-4 py-3 border-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $entry)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $entry['course']->name }}</div>
                                    <small class="text-muted">{{ $entry['course']->code ?? $entry['course']->course_code }}</small>
                                </td>
                                <td class="py-3 text-muted">{{ $entry['course']->semester->name ?? 'N/A' }}</td>
                                <td class="text-end py-3 fw-semibold">{{ $entry['students_count'] }}</td>
                                <td class="text-end py-3">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="me-2 fw-semibold">{{ $entry['average_progress'] }}%</span>
                                        <div class="progress" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $entry['average_progress'] }}%; background-color: #001d48 !important;" aria-valuenow="{{ $entry['average_progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 text-end py-3"><a href="{{ route('faculty.lms.show', $entry['course']) }}" class="btn btn-sm text-white" style="background-color: #001d48;">View Learners</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">No courses assigned.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
