@extends('layouts.app')

@section('title', 'Course Grades - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Course Grades</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->name }}</a></li>
                            <li class="breadcrumb-item active">Grades</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.courses.export-grades', ['course' => $course, 'format' => 'excel']) }}">
                                <i class="bi bi-file-earmark-excel me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.courses.export-grades', ['course' => $course, 'format' => 'pdf']) }}">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Course Info & Stats -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-1">{{ $course->name }}</h5>
                            <p class="text-muted mb-0">{{ $course->code }} • {{ $course->credits }} Credits</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-0">{{ number_format($averageGrade, 1) }}%</h3>
                            <small class="text-muted">Class Average</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Grade Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="gradeChart" height="100"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <h4 class="text-success mb-0">{{ $gradeDistribution[0] }}</h4>
                                    <small class="text-muted">A (90-100%)</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-info mb-0">{{ $gradeDistribution[1] }}</h4>
                                    <small class="text-muted">B (80-89%)</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-warning mb-0">{{ $gradeDistribution[2] }}</h4>
                                    <small class="text-muted">C (70-79%)</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-orange mb-0">{{ $gradeDistribution[3] }}</h4>
                                    <small class="text-muted">D (60-69%)</small>
                                </div>
                                <div class="col-12">
                                    <h4 class="text-danger mb-0">{{ $gradeDistribution[4] }}</h4>
                                    <small class="text-muted">F (Below 60%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gradebook -->
            @if($enrollments->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gradebook</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="sticky-column">Student</th>
                                        @foreach($assignments as $assignment)
                                            <th class="text-center" style="min-width: 120px;">
                                                <div>{{ Str::limit($assignment->title, 15) }}</div>
                                                <small class="text-muted">({{ $assignment->max_points }}pts)</small>
                                            </th>
                                        @endforeach
                                        <th class="text-center bg-primary text-white">Total</th>
                                        <th class="text-center bg-primary text-white">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        @php
                                            $studentGrades = $grades->where('student_id', $enrollment->student->id);
                                            $totalEarned = $studentGrades->sum('points_earned');
                                            $totalPossible = $assignments->sum('max_points');
                                            $percentage = $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;

                                            // Calculate letter grade
                                            if ($percentage >= 90) $letterGrade = 'A';
                                            elseif ($percentage >= 80) $letterGrade = 'B';
                                            elseif ($percentage >= 70) $letterGrade = 'C';
                                            elseif ($percentage >= 60) $letterGrade = 'D';
                                            else $letterGrade = 'F';
                                        @endphp
                                        <tr>
                                            <td class="sticky-column bg-white">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $enrollment->student->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($enrollment->student->name) . '&background=007bff&color=fff' }}"
                                                         alt="{{ $enrollment->student->name }}"
                                                         class="rounded-circle me-2"
                                                         width="32" height="32">
                                                    <div>
                                                        <div class="fw-medium">{{ $enrollment->student->name }}</div>
                                                        <small class="text-muted">{{ $enrollment->student->studentProfile->student_id ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            @foreach($assignments as $assignment)
                                                @php
                                                    $grade = $studentGrades->where('assignment_id', $assignment->id)->first();
                                                @endphp
                                                <td class="text-center">
                                                    @if($grade)
                                                        <div>
                                                            <span class="fw-medium">{{ $grade->points_earned }}</span>
                                                            <small class="text-muted">/ {{ $assignment->max_points }}</small>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ number_format(($grade->points_earned / $assignment->max_points) * 100, 1) }}%
                                                        </small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center bg-light">
                                                <div>
                                                    <span class="fw-bold">{{ $totalEarned }}</span>
                                                    <small class="text-muted">/ {{ $totalPossible }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center bg-light">
                                                <div>
                                                    <span class="fw-bold fs-5
                                                        @if($letterGrade === 'A') text-success
                                                        @elseif($letterGrade === 'B') text-info
                                                        @elseif($letterGrade === 'C') text-warning
                                                        @elseif($letterGrade === 'D') text-orange
                                                        @else text-danger
                                                        @endif">
                                                        {{ $letterGrade }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-graph-up display-1 text-muted"></i>
                        <h5 class="mt-3">No Students Enrolled</h5>
                        <p class="text-muted">Grades will appear here once students are enrolled in the course.</p>
                        <a href="{{ route('admin.courses.enrollments', $course) }}" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i>Enroll Students
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grade Distribution Chart
const ctx = document.getElementById('gradeChart').getContext('2d');
const gradeChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['A (90-100%)', 'B (80-89%)', 'C (70-79%)', 'D (60-69%)', 'F (Below 60%)'],
        datasets: [{
            label: 'Number of Students',
            data: @json($gradeDistribution),
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(253, 126, 20, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(253, 126, 20, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.sticky-column {
    position: sticky;
    left: 0;
    z-index: 10;
    background-color: white;
    border-right: 2px solid #dee2e6;
}

.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

.sticky-top {
    z-index: 20;
}

.text-orange {
    color: #fd7e14 !important;
}

.table td {
    vertical-align: middle;
}

#gradeChart {
    max-height: 300px;
}
</style>
@endpush
