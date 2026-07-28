@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Enrollment Report</h1>
                    <p class="text-muted">Student enrollment statistics and trends</p>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Enrollments</h6>
                    <h2 class="mb-0">{{ number_format($stats['total_enrollments']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Active</h6>
                    <h2 class="mb-0">{{ number_format($stats['active_enrollments']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Completed</h6>
                    <h2 class="mb-0">{{ number_format($stats['completed_enrollments']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Dropped</h6>
                    <h2 class="mb-0">{{ number_format($stats['dropped_enrollments']) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fa fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.enrollment') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name ?? $year->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select">
                            <option value="">All Semesters</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fa fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.reports.enrollment') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fa fa-bar-chart me-2"></i>Enrollments by Department</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-end">Total Enrollments</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollmentsByDepartment as $dept)
                                    <tr>
                                        <td>{{ $dept->department_name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($dept->count) }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $stats['total_enrollments'] > 0 ? ($dept->count / $stats['total_enrollments']) * 100 : 0 }}%"
                                                     aria-valuenow="{{ $dept->count }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="{{ $stats['total_enrollments'] }}">
                                                    {{ $stats['total_enrollments'] > 0 ? number_format(($dept->count / $stats['total_enrollments']) * 100, 1) : 0 }}%
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
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fa fa-list me-2"></i>Enrollment Details</h5>
            <a href="{{ route('admin.reports.enrollment.export', request()->all()) }}"
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
                            <th>Semester</th>
                            <th>Enrollment Date</th>
                            <th>Status</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment?->student?->full_name ?: ($enrollment?->student?->name ?? 'N/A') }}</td>
                                <td>{{ $enrollment->student->studentProfile->admission_number ?? 'N/A' }}</td>
                                <td>{{ $enrollment->course->code ?? $enrollment->course->course_code }} - {{ $enrollment->course->name }}</td>
                                <td>{{ $enrollment?->semester?->name ?? 'N/A' }}</td>
                                <td>{{ $enrollment->enrollment_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{
                                        $enrollment->status === 'enrolled' ? 'success' :
                                        ($enrollment->status === 'dropped' ? 'danger' : 'info')
                                    }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td>{{ $enrollment->letter_grade ?? ($enrollment->final_grade ?? 'N/A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No enrollment records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
