@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Attendance Report</h1>
                    <p class="text-muted">Class attendance tracking and analysis</p>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Records</h6>
                    <h2 class="mb-0">{{ number_format($stats['total_records']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Present</h6>
                    <h2 class="mb-0">{{ number_format($stats['present']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Absent</h6>
                    <h2 class="mb-0">{{ number_format($stats['absent']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Attendance Rate</h6>
                    <h2 class="mb-0">{{ number_format($stats['attendance_rate'], 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fa fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.attendance') }}">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->code ?? $course->course_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.reports.attendance') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Course-wise Attendance -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fa fa-book me-2"></i>Course-wise Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courseAttendance as $course)
                                    <tr>
                                        <td>{{ $course->course->code ?? $course->course->course_code ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($course->total) }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{
                                                $course->rate >= 80 ? 'success' :
                                                ($course->rate >= 60 ? 'warning' : 'danger')
                                            }}">
                                                {{ number_format($course->rate, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Attendance Alert -->
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-exclamation-triangle text-danger me-2"></i>Low Attendance Alert (Below 75%)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th class="text-end">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowAttendanceStudents as $student)
                                    <tr>
                                        <td>{{ $student->student?->full_name ?: ($student->student?->name ?? 'N/A') }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-danger">
                                                {{ number_format($student->rate, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            <i class="fa fa-check-circle text-success me-2"></i>
                                            All students have good attendance
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Attendance Trend -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="	fa fa-bar-chart me-2"></i>Daily Attendance Trend (Last 30 Days)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Present</th>
                            <th class="text-end">Rate</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceTrends as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                <td class="text-end">{{ number_format($day->total) }}</td>
                                <td class="text-end">{{ number_format($day->present) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{
                                        $day->rate >= 80 ? 'success' :
                                        ($day->rate >= 60 ? 'warning' : 'danger')
                                    }}">
                                        {{ number_format($day->rate, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ $day->rate }}%">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fa fa-list me-2"></i>Attendance Records</h5>
            <a href="{{ route('admin.reports.attendance.export', request()->all()) }}"
               class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export CSV
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Admission Number</th>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                            <tr>
                                <td>{{ $record->student?->full_name ?: ($record->student?->name ?? 'N/A') }}</td>
                                <td>{{ $record->student?->studentProfile?->admission_number ?? 'N/A' }}</td>
                                <td>{{ $record->course->code ?? $record->course->course_code ?? 'N/A' }}</td>
                                <td>{{ $record->attendance_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{
                                        $record->status === 'present' ? 'success' :
                                        ($record->status === 'late' ? 'warning' :
                                        ($record->status === 'excused' ? 'info' : 'danger'))
                                    }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td>{{ $record->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $attendanceRecords->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
