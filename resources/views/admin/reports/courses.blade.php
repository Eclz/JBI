@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Course Reports</h1>
            <p class="text-muted">Comprehensive overview of course statistics and enrollment data</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.courses.export', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Export Report
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Courses</p>
                            <h3 class="mb-0">{{ $stats['total_courses'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-book text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Courses</p>
                            <h3 class="mb-0">{{ $stats['active_courses'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Inactive Courses</p>
                            <h3 class="mb-0">{{ $stats['inactive_courses'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-pause-circle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Enrollments</p>
                            <h3 class="mb-0">{{ $stats['total_enrollments'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Courses by Department -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Courses by Department</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-end">Courses</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coursesByDepartment as $dept)
                                <tr>
                                    <td>{{ $dept->department->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ $dept->count }}</td>
                                    <td class="text-end">
                                        {{ $stats['total_courses'] > 0 ? number_format(($dept->count / $stats['total_courses']) * 100, 1) : 0 }}%
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

        <!-- Most Enrolled Courses -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top 10 Most Enrolled Courses</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Code</th>
                                    <th class="text-end">Enrollments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularCourses as $course)
                                <tr>
                                    <td>{{ $course->name }}</td>
                                    <td><span class="badge bg-secondary">{{ $course->code }}</span></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $course->enrollment_count ?? 0 }}</span>
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.courses') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Course name or code..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
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
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.reports.courses') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Courses List</h5>
            <span class="badge bg-secondary">{{ $courses->total() }} courses</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Department</th>
                            <th>Instructor</th>
                            <th>Semester</th>
                            <th class="text-center">Credits</th>
                            <th class="text-center">Capacity</th>
                            <th class="text-center">Enrollments</th>
                            <th class="text-center">Active</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $course->code }}</span></td>
                            <td>
                                <strong>{{ $course->name }}</strong>
                            </td>
                            <td>{{ $course->department->name ?? 'N/A' }}</td>
                            <td>{{ $course->instructor?->full_name ?: ($course->instructor?->name ?? 'N/A') }}</td>
                            <td>{{ $course->semester->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ $course->credits ?? 0 }}</td>
                            <td class="text-center">{{ $course->capacity ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $course->enrollments_count ?? 0 }}</span>
                                <small class="text-muted">({{ $course->active_enrollments ?? 0 }} active)</small>
                            </td>
                            <td class="text-center">
                                @php
                                    $utilization = $course->capacity > 0 ? (($course->active_enrollments ?? 0) / $course->capacity) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 20px; min-width: 60px;">
                                    <div class="progress-bar {{ $utilization >= 90 ? 'bg-danger' : ($utilization >= 75 ? 'bg-warning' : 'bg-success') }}"
                                         role="progressbar"
                                         style="width: {{ min($utilization, 100) }}%">
                                        {{ number_format($utilization, 0) }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($course->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($course->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No courses found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($courses->hasPages())
        <div class="card-footer bg-white">
            {{ $courses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
