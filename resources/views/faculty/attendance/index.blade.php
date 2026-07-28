@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 600;">Attendance Management</h2>
            <p class="text-muted mb-0">Track and manage student attendance across all your courses</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Courses</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $courses->count() }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-journal-text" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Classes</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $totalClasses }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-calendar-check" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Students</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $courses->sum('enrolled_students_count') }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-people" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Attendance Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4" style="color: #1a202c; font-weight: 600;">Course Attendance Overview</h5>

            @if($courses->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <p class="mt-3 text-muted">No courses assigned yet</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f7fafc;">
                            <tr>
                                <th style="color: #4a5568; font-weight: 600;">Course</th>
                                <th style="color: #4a5568; font-weight: 600;">Code</th>
                                <th style="color: #4a5568; font-weight: 600;">Students</th>
                                <th style="color: #4a5568; font-weight: 600;">Total Classes</th>
                                <th style="color: #4a5568; font-weight: 600;">Present</th>
                                <th style="color: #4a5568; font-weight: 600;">Late</th>
                                <th style="color: #4a5568; font-weight: 600;">Absent</th>
                                <th style="color: #4a5568; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                @php
                                    $stats = $attendanceStats[$course->id] ?? null;
                                    $totalRecords = $stats->total_records ?? 0;
                                    $presentCount = $stats->present_count ?? 0;
                                    $lateCount = $stats->late_count ?? 0;
                                    $absentCount = $stats->absent_count ?? 0;
                                    $attendanceRate = $totalRecords > 0 ? round(($presentCount + $lateCount) / $totalRecords * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td style="color: #2d3748; font-weight: 500;">{{ $course->name }}</td>
                                    <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                    <td style="color: #4a5568;">{{ $course->enrolled_students_count }}</td>
                                    <td style="color: #4a5568;">{{ $totalRecords }}</td>
                                    <td><span class="badge bg-success">{{ $presentCount }}</span></td>
                                    <td><span class="badge bg-warning">{{ $lateCount }}</span></td>
                                    <td><span class="badge bg-danger">{{ $absentCount }}</span></td>
                                    <td>
                                        <a href="{{ route('faculty.courses.attendance.index', $course) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>Manage
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
</div>
@endsection
