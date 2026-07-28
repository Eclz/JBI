@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Student Reports</h1>
                    <p class="text-muted">Student demographics and enrollment statistics</p>
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
                    <h6 class="card-title">Total Students</h6>
                    <h2 class="mb-0">{{ number_format($stats['total_students']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Active</h6>
                    <h2 class="mb-0">{{ number_format($stats['active_students']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Male</h6>
                    <h2 class="mb-0">{{ number_format($stats['male_students']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Female</h6>
                    <h2 class="mb-0">{{ number_format($stats['female_students']) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.students') }}">
                <div class="row">
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
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">All Gender</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.reports.students') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Students by Department -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Students by Department</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-end">Count</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentsByDepartment as $dept)
                                    <tr>
                                        <td>{{ $dept->department->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($dept->count) }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $stats['total_students'] > 0 ? ($dept->count / $stats['total_students']) * 100 : 0 }}%">
                                                    {{ $stats['total_students'] > 0 ? number_format(($dept->count / $stats['total_students']) * 100, 1) : 0 }}%
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

        <!-- Students by Status -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Students by Status</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentsByStatus as $status)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{
                                                $status->status === 'active' ? 'success' :
                                                ($status->status === 'graduated' ? 'info' :
                                                ($status->status === 'suspended' ? 'warning' : 'danger'))
                                            }}">
                                                {{ ucfirst($status->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($status->count) }}</td>
                                        <td class="text-end">{{ number_format(($status->count / $stats['total_students']) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Admissions -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-user-plus me-2"></i>Recent Admissions (Last 30 Days)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">New Students</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAdmissions as $admission)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($admission->date)->format('M d, Y') }}</td>
                                <td class="text-end">{{ number_format($admission->count) }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                             style="width: {{ $recentAdmissions->max('count') > 0 ? ($admission->count / $recentAdmissions->max('count')) * 100 : 0 }}%">
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

    <!-- Student List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Student Details</h5>
            <a href="{{ route('admin.reports.students.export', request()->all()) }}"
               class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export CSV
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Admission Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Program</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->studentProfile->admission_number ?? 'N/A' }}</td>
                                <td>{{ $student->full_name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->studentProfile->department->name ?? 'N/A' }}</td>
                                <td>{{ $student->studentProfile->program ?? 'N/A' }}</td>
                                <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                <td>{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No students found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
