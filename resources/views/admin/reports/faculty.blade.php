@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Faculty Report</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                            <li class="breadcrumb-item active">Faculty</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.reports.faculty.export') }}" class="btn btn-success">
                        <i class="bi bi-file-excel"></i> Export to CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Faculty</h6>
                            <h3 class="mb-0">{{ $stats['total_faculty'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Active Faculty</h6>
                            <h3 class="mb-0">{{ $stats['active_faculty'] ?? 0 }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-person-check-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Inactive Faculty</h6>
                            <h3 class="mb-0">{{ $stats['inactive_faculty'] ?? 0 }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="bi bi-person-x-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty by Department -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Faculty by Department</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyByDepartment as $dept)
                                <tr>
                                    <td>{{ $dept->department->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ $dept->count }}</td>
                                    <td class="text-end">
                                        {{ $stats['total_faculty'] > 0 ? number_format(($dept->count / $stats['total_faculty']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faculty by Employment Status -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Faculty by Employment Status</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyByStatus as $status)
                                <tr>
                                    <td>{{ ucfirst(str_replace('-', ' ', $status->employment_status)) }}</td>
                                    <td class="text-end">{{ $status->count }}</td>
                                    <td class="text-end">
                                        {{ $stats['total_faculty'] > 0 ? number_format(($status->count / $stats['total_faculty']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Load per Faculty -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top 10 Faculty by Course Load</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Faculty Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th class="text-end">Active Courses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyLoad as $index => $member)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $member->full_name ?: ($member->name ?? 'N/A') }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->facultyProfile->department->name ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $member->active_courses ?? 0 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Filter Faculty</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.faculty') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Name, email, employee ID..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Employment Status</label>
                                <select name="employment_status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach($employmentStatuses as $empStatus)
                                    <option value="{{ $empStatus }}" {{ request('employment_status') == $empStatus ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('-', ' ', $empStatus)) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="{{ route('admin.reports.faculty') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Faculty Members ({{ $faculty->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Employment Status</th>
                                    <th>Active Courses</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculty as $member)
                                <tr>
                                    <td>{{ $member->facultyProfile->employee_id ?? 'N/A' }}</td>
                                    <td>{{ $member->full_name ?: ($member->name ?? 'N/A') }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->facultyProfile->department->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $member->facultyProfile->employment_status ? ucfirst(str_replace('-', ' ', $member->facultyProfile->employment_status)) : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $member->taughtCourses->where('status', 'active')->count() }}</td>
                                    <td>
                                        @if($member->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No faculty members found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($faculty->hasPages())
                    <div class="mt-3">
                        {{ $faculty->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
