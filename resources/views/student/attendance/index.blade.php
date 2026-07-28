@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="h3 mb-2" style="color: #1a202c; font-weight: 600;">My Attendance</h2>
        <p class="text-muted mb-0">Track your attendance across all enrolled courses</p>
    </div>

    <!-- Overall Statistics -->
    <div class="row g-3 mb-4">
        @php
            $totalClasses = $attendanceData->sum('total_classes');
            $totalPresent = $attendanceData->sum('present_count');
            $totalLate = $attendanceData->sum('late_count');
            $totalAbsent = $attendanceData->sum('absent_count');
            $overallRate = $totalClasses > 0 ? round(($totalPresent / $totalClasses) * 100, 1) : 0;
        @endphp

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.1);">
                                <i class="bi bi-calendar-check" style="font-size: 24px; color: #3b82f6;"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Total Classes</p>
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $totalClasses }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(34, 197, 94, 0.1);">
                                <i class="bi bi-check-circle" style="font-size: 24px; color: #22c55e;"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Present</p>
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $totalPresent }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(239, 68, 68, 0.1);">
                                <i class="bi bi-x-circle" style="font-size: 24px; color: #ef4444;"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Absent</p>
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $totalAbsent }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: rgba(168, 85, 247, 0.1);">
                                <i class="bi bi-percent" style="font-size: 24px; color: #a855f7;"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Attendance Rate</p>
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $overallRate }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course-wise Attendance -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0" style="color: #1a202c; font-weight: 600;">Course-wise Attendance</h5>
        </div>
        <div class="card-body p-0">
            @if($attendanceData->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0 px-4 py-3" style="color: #4b5563; font-weight: 600;">Course</th>
                                <th class="border-0 px-4 py-3 text-center" style="color: #4b5563; font-weight: 600;">Total Classes</th>
                                <th class="border-0 px-4 py-3 text-center" style="color: #4b5563; font-weight: 600;">Present</th>
                                <th class="border-0 px-4 py-3 text-center" style="color: #4b5563; font-weight: 600;">Late</th>
                                <th class="border-0 px-4 py-3 text-center" style="color: #4b5563; font-weight: 600;">Absent</th>
                                <th class="border-0 px-4 py-3" style="color: #4b5563; font-weight: 600;">Attendance Rate</th>
                                <th class="border-0 px-4 py-3 text-end" style="color: #4b5563; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceData as $attendance)
                                @php
                                    $rate = $attendance->total_classes > 0
                                        ? round(($attendance->present_count / $attendance->total_classes) * 100, 1)
                                        : 0;
                                    $rateColor = $rate >= 75 ? '#22c55e' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <div>
                                            <div style="font-weight: 600; color: #1a202c;">
                                                {{ $attendance->course->course_code ?? 'N/A' }}
                                            </div>
                                            <div class="small text-muted">
                                                {{ $attendance->course->course_name ?? 'Unknown Course' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center" style="color: #4b5563; font-weight: 500;">
                                        {{ $attendance->total_classes }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; font-weight: 600;">
                                            {{ $attendance->present_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; font-weight: 600;">
                                            {{ $attendance->late_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600;">
                                            {{ $attendance->absent_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $rate }}%; background-color: {{ $rateColor }};"
                                                     aria-valuenow="{{ $rate }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span style="font-weight: 600; color: {{ $rateColor }}; min-width: 45px;">
                                                {{ $rate }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <a href="{{ route('student.attendance.show', $attendance->course_id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 64px; color: #cbd5e1;"></i>
                    <h5 class="mt-3 mb-2" style="color: #64748b;">No Attendance Records</h5>
                    <p class="text-muted mb-0">Attendance records will appear here once classes begin.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
