@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Academic Performance Report</h1>
                    <p class="text-muted">Student grades, GPA analysis, and academic achievements</p>
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
                    <h6 class="card-title">Total Grades</h6>
                    <h2 class="mb-0">{{ number_format($stats['total_grades'] ?? 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Average GPA</h6>
                    <h2 class="mb-0">{{ number_format($stats['average_gpa'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Pass Rate</h6>
                    <h2 class="mb-0">{{ number_format($stats['pass_rate'] ?? 0, 1) }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Fail Rate</h6>
                    <h2 class="mb-0">{{ number_format($stats['fail_rate'] ?? 0, 1) }}%</h2>
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
            <form method="GET" action="{{ route('admin.reports.academic') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select">
                            <option value="">All Years</option>
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
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.reports.academic') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Grade Distribution -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Grade Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Grade</th>
                                    <th class="text-end">Count</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gradeDistribution as $grade)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{
                                                in_array($grade->grade, ['A', 'A+', 'A-']) ? 'success' :
                                                (in_array($grade->grade, ['B', 'B+', 'B-']) ? 'info' :
                                                (in_array($grade->grade, ['C', 'C+', 'C-']) ? 'warning' : 'danger'))
                                            }}">
                                                {{ $grade->grade }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($grade->count) }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{
                                                    in_array($grade->grade, ['A', 'A+', 'A-']) ? 'success' :
                                                    (in_array($grade->grade, ['B', 'B+', 'B-']) ? 'info' :
                                                    (in_array($grade->grade, ['C', 'C+', 'C-']) ? 'warning' : 'danger'))
                                                }}" role="progressbar"
                                                     style="width: {{ $stats['total_grades'] > 0 ? ($grade->count / $stats['total_grades']) * 100 : 0 }}%">
                                                    {{ $stats['total_grades'] > 0 ? number_format(($grade->count / $stats['total_grades']) * 100, 1) : 0 }}%
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

        <!-- Top Performers -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top Performers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Student</th>
                                    <th>Department</th>
                                    <th class="text-end">GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $index => $performer)
                                    <tr>
                                        <td>
                                            @if($index === 0)
                                                <i class="fas fa-trophy text-warning"></i>
                                            @elseif($index === 1)
                                                <i class="fas fa-medal text-secondary"></i>
                                            @elseif($index === 2)
                                                <i class="fas fa-medal text-danger"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>{{ $performer->full_name ?: ($performer->name ?? 'N/A') }}</td>
                                        <td>{{ $performer->studentProfile->department->name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-success">{{ number_format($performer->avg_gpa, 2) }}</span>
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

    <!-- Course Performance -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-book me-2"></i>Course Performance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th class="text-end">Enrolled</th>
                            <th class="text-end">Graded</th>
                            <th class="text-end">Avg Grade</th>
                            <th class="text-end">Pass Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coursePerformance as $course)
                            <tr>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->course_name }}</td>
                                <td class="text-end">{{ number_format($course->enrolled_count) }}</td>
                                <td class="text-end">{{ number_format($course->graded_count) }}</td>
                                <td class="text-end">{{ $course->avg_grade !== null ? number_format($course->avg_grade, 2) . '%' : 'N/A' }}</td>
                                <td class="text-end">
                                    @if($course->pass_rate !== null)
                                        <span class="badge bg-{{ $course->pass_rate >= 70 ? 'success' : ($course->pass_rate >= 50 ? 'warning' : 'danger') }}">
                                            {{ number_format($course->pass_rate, 1) }}%
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grade Details -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Grade Records</h5>
            <a href="{{ route('admin.reports.academic.export', request()->all()) }}"
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
                            <th class="text-end">Grade</th>
                            <th class="text-end">Points</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $grade)
                            <tr>
                                <td>{{ $grade->student?->full_name ?: ($grade->student?->name ?? 'N/A') }}</td>
                                <td>{{ $grade->student?->studentProfile?->admission_number ?? 'N/A' }}</td>
                                <td>{{ ($grade->course->code ?? $grade->course->course_code ?? 'N/A') }} - {{ $grade->course->name ?? 'N/A' }}</td>
                                <td>{{ $grade->course?->semester?->name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{
                                        in_array($grade->letter_grade, ['A', 'A+', 'A-']) ? 'success' :
                                        (in_array($grade->letter_grade, ['B', 'B+', 'B-']) ? 'info' :
                                        (in_array($grade->letter_grade, ['C', 'C+', 'C-']) ? 'warning' : 'danger'))
                                    }}">
                                        {{ $grade->letter_grade ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($grade->grade_points ?? 0, 2) }}</td>
                                <td>{{ $grade->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No grade records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $grades->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
