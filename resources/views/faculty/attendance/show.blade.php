@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 600;">{{ $course->name }}</h2>
            <p class="text-muted mb-0">{{ $course->course_code ?? $course->code }} - Attendance Records</p>
        </div>
        <a href="{{ route('faculty.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Attendance
        </a>
    </div>

    @php
        $total = $attendanceRecords->total();
        $present = (clone $attendanceRecords->getCollection())->where('status', 'present')->count();
        $late = (clone $attendanceRecords->getCollection())->where('status', 'late')->count();
        $absent = (clone $attendanceRecords->getCollection())->where('status', 'absent')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Records</div>
                    <div class="h4 mb-0">{{ $total }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Present (Page)</div>
                    <div class="h4 mb-0 text-success">{{ $present }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Late (Page)</div>
                    <div class="h4 mb-0 text-warning">{{ $late }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Absent (Page)</div>
                    <div class="h4 mb-0 text-danger">{{ $absent }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($attendanceRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Status</th>
                                <th>Check-in</th>
                                <th>Marked By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $record)
                                <tr>
                                    <td>{{ $record->attendance_date?->format('M d, Y') ?? '-' }}</td>
                                    <td>{{ $record->student->full_name ?? $record->student->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($record->status === 'present')
                                            <span class="badge bg-success">Present</span>
                                        @elseif($record->status === 'late')
                                            <span class="badge bg-warning text-dark">Late</span>
                                        @elseif($record->status === 'absent')
                                            <span class="badge bg-danger">Absent</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->check_in_time?->format('H:i') ?? '-' }}</td>
                                    <td>{{ $record->markedBy->name ?? '-' }}</td>
                                    <td>{{ $record->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0">
                    {{ $attendanceRecords->links() }}
                </div>
            @else
                <div class="p-4 text-center text-muted">No attendance records found for this course.</div>
            @endif
        </div>
    </div>
</div>
@endsection
