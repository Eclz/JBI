@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Attendance Records</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.show', $student->id) }}">{{ $student->name }}</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Student
        </a>
    </div>

    <!-- Student Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="{{ asset('storage/' . $student->profile_picture) ?? '/images/default-avatar.png' }}"
                     alt="{{ $student->name }}"
                     class="rounded-circle me-3"
                     style="width: 60px; height: 60px; object-fit: cover;">
                <div>
                    <h5 class="mb-1">{{ $student->name }}</h5>
                    <p class="text-muted mb-0">
                        {{ $student->email }} |
                        {{ $student->studentProfile->department->name ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Classes</h6>
                    <h3 class="mb-0">{{ $stats['total_classes'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Present</h6>
                    <h3 class="mb-0 text-success">{{ $stats['present'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Absent</h6>
                    <h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Attendance Rate</h6>
                    <h3 class="mb-0 text-primary">{{ $stats['attendance_percentage'] }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Attendance History</h5>
        </div>
        <div class="card-body">
            @if($attendanceRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $record)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}</td>
                                    <td>
                                        <strong>{{ $record->course->course_code }}</strong><br>
                                        <small class="text-muted">{{ $record->course->course_name }}</small>
                                    </td>
                                    <td>
                                        @if($record->status === 'present')
                                            <span class="badge bg-success">Present</span>
                                        @elseif($record->status === 'absent')
                                            <span class="badge bg-danger">Absent</span>
                                        @elseif($record->status === 'late')
                                            <span class="badge bg-warning text-dark">Late</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->remarks ?: '-' }}</td>
                                    <td>{{ $record->marked_by }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $attendanceRecords->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No attendance records found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
