@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h3 mb-2" style="color: #1a202c; font-weight: 600;">{{ $course->course_name }}</h2>
                <p class="text-muted mb-0">{{ $course->course_code }} - Attendance Details</p>
            </div>
            <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to All Courses
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        @php
            $attendanceRate = $attendanceStats['total'] > 0
                ? round(($attendanceStats['present'] / $attendanceStats['total']) * 100, 1)
                : 0;
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
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $attendanceStats['total'] }}</h3>
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
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $attendanceStats['present'] }}</h3>
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
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $attendanceStats['absent'] }}</h3>
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
                            <h3 class="mb-0" style="color: #1a202c; font-weight: 700;">{{ $attendanceRate }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0" style="color: #1a202c; font-weight: 600;">Attendance Records</h5>
        </div>
        <div class="card-body p-0">
            @if($attendanceRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0 px-4 py-3" style="color: #4b5563; font-weight: 600;">Date</th>
                                <th class="border-0 px-4 py-3" style="color: #4b5563; font-weight: 600;">Status</th>
                                <th class="border-0 px-4 py-3" style="color: #4b5563; font-weight: 600;">Check-in Time</th>
                                <th class="border-0 px-4 py-3" style="color: #4b5563; font-weight: 600;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $record)
                                <tr>
                                    <td class="px-4 py-3" style="color: #1a202c; font-weight: 500;">
                                        {{ $record->attendance_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($record->status === 'present')
                                            <span class="badge" style="background-color: rgba(34, 197, 94, 0.1); color: #22c55e; font-weight: 600; padding: 6px 12px;">
                                                <i class="bi bi-check-circle me-1"></i>Present
                                            </span>
                                        @elseif($record->status === 'absent')
                                            <span class="badge" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600; padding: 6px 12px;">
                                                <i class="bi bi-x-circle me-1"></i>Absent
                                            </span>
                                        @elseif($record->status === 'late')
                                            <span class="badge" style="background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; font-weight: 600; padding: 6px 12px;">
                                                <i class="bi bi-clock me-1"></i>Late
                                            </span>
                                        @else
                                            <span class="badge" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; font-weight: 600; padding: 6px 12px;">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3" style="color: #4b5563;">
                                        {{ $record->check_in_time ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #4b5563;">
                                        {{ $record->notes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-0">
                    {{ $attendanceRecords->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 64px; color: #cbd5e1;"></i>
                    <h5 class="mt-3 mb-2" style="color: #64748b;">No Attendance Records</h5>
                    <p class="text-muted mb-0">Attendance records for this course will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
