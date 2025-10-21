@extends('layouts.app')

@section('title', 'Academic Record - ' . $student->name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Academic Record</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.students.show', $student) }}">{{ $student->name }}</a></li>
                            <li class="breadcrumb-item active">Academic Record</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" onclick="window.print()">
                        <i class="fa fa-print"></i> Print Transcript
                    </button>
                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Information Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <img src="{{ $student->profile_picture_url }}"
                                 alt="{{ $student->name }}"
                                 class="rounded-circle mb-2"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="mb-1">{{ $student->name }}</h4>
                                    <p class="text-muted mb-1">{{ $student->email }}</p>
                                    <p class="mb-0">
                                        <strong>Admission Number:</strong>
                                        {{ $student->studentProfile?->admission_number ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Department:</strong>
                                        {{ $student->studentProfile?->department?->name ?? 'N/A' }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Program:</strong>
                                        {{ $student->studentProfile?->program ?? 'N/A' }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>Status:</strong>
                                        <span class="badge bg-{{ $student->studentProfile?->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($student->studentProfile?->status ?? 'N/A') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">Current GPA</h5>
                    <h2 class="mb-0">{{ number_format($student->studentProfile?->current_gpa ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Cumulative GPA</h5>
                    <h2 class="mb-0">{{ number_format($student->studentProfile?->cumulative_gpa ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">Credits Earned</h5>
                    <h2 class="mb-0">{{ $student->studentProfile?->total_credits_earned ?? 0 }}</h2>
                    <small class="text-muted">of {{ $student->studentProfile?->total_credits_required ?? 0 }} required</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">Progress</h5>
                    <h2 class="mb-0">{{ $student->studentProfile?->progress_percentage ?? 0 }}%</h2>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar"
                             style="width: {{ $student->studentProfile?->progress_percentage ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course History by Semester -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Course History</h5>
                </div>
                <div class="card-body">
                    @if(isset($enrollments) && $enrollments->count() > 0)
                        @php
                            $groupedEnrollments = $enrollments->groupBy(function($enrollment) {
                                return $enrollment->course->semester->name . ' - ' . $enrollment->course->semester->academic_year;
                            });
                        @endphp

                        @foreach($groupedEnrollments as $semesterName => $semesterEnrollments)
                            <div class="semester-section mb-4">
                                <h6 class="text-primary border-bottom pb-2 mb-3">{{ $semesterName }}</h6>

                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Course Code</th>
                                                <th>Course Name</th>
                                                <th>Credits</th>
                                                <th>Grade</th>
                                                <th>Letter Grade</th>
                                                <th>Grade Points</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $semesterCredits = 0;
                                                $semesterGradePoints = 0;
                                            @endphp
                                            @foreach($semesterEnrollments as $enrollment)
                                                @php
                                                    $semesterCredits += $enrollment->course->credits ?? 0;
                                                    $semesterGradePoints += ($enrollment->grade_points ?? 0) * ($enrollment->course->credits ?? 0);
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $enrollment->course->code ?? 'N/A' }}</strong></td>
                                                    <td>{{ $enrollment->course->name ?? 'N/A' }}</td>
                                                    <td>{{ $enrollment->course->credits ?? 0 }}</td>
                                                    <td>
                                                        @if($enrollment->final_grade)
                                                            <span class="badge bg-{{ $enrollment->final_grade >= 70 ? 'success' : ($enrollment->final_grade >= 60 ? 'warning' : 'danger') }}">
                                                                {{ number_format($enrollment->final_grade, 1) }}%
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($enrollment->letter_grade)
                                                            <strong class="text-{{ $enrollment->letter_grade === 'F' ? 'danger' : 'success' }}">
                                                                {{ $enrollment->letter_grade }}
                                                            </strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($enrollment->grade_points ?? 0, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $enrollment->status === 'completed' ? 'success' : ($enrollment->status === 'enrolled' ? 'primary' : 'secondary') }}">
                                                            {{ ucfirst($enrollment->status ?? 'N/A') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="2">Semester Totals</th>
                                                <th>{{ $semesterCredits }}</th>
                                                <th colspan="2">Semester GPA</th>
                                                <th>{{ $semesterCredits > 0 ? number_format($semesterGradePoints / $semesterCredits, 2) : '0.00' }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Course Enrollments Found</h5>
                            <p class="text-muted">This student has not been enrolled in any courses yet.</p>
                            <a href="{{ route('admin.students.enroll-course', $student) }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Enroll in Course
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Achievements -->
    @if($student->studentProfile?->achievements)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Academic Achievements</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $achievements = $student->studentProfile->achievements;
                            if (is_string($achievements)) {
                                $achievements = json_decode($achievements, true) ?? [];
                            }
                            if (!is_array($achievements)) {
                                $achievements = [];
                            }
                        @endphp

                        @if(count($achievements) > 0)
                            @foreach($achievements as $achievement)
                                <div class="achievement-item mb-2">
                                    <i class="fa fa-trophy text-warning me-2"></i>
                                    {{ is_string($achievement) ? $achievement : (is_array($achievement) ? implode(', ', $achievement) : 'Achievement') }}
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No achievements recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Academic History -->
    @if($student->studentProfile?->academic_history)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Academic History</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $academicHistory = $student->studentProfile->academic_history;
                            if (is_string($academicHistory)) {
                                $academicHistory = json_decode($academicHistory, true) ?? [];
                            }
                            if (!is_array($academicHistory)) {
                                $academicHistory = [];
                            }
                        @endphp

                        @if(isset($academicHistory['high_school']))
                            <div class="history-section mb-3">
                                <h6 class="text-info"><i class="fa fa-school me-2"></i>High School Education</h6>
                                <div class="ms-4">
                                    @if(isset($academicHistory['high_school']['name']))
                                        <p class="mb-1"><strong>School:</strong> {{ $academicHistory['high_school']['name'] }}</p>
                                    @endif
                                    @if(isset($academicHistory['high_school']['address']))
                                        <p class="mb-1"><strong>Address:</strong> {{ $academicHistory['high_school']['address'] }}</p>
                                    @endif
                                    @if(isset($academicHistory['high_school']['graduation_year']))
                                        <p class="mb-1"><strong>Graduation Year:</strong> {{ $academicHistory['high_school']['graduation_year'] }}</p>
                                    @endif
                                    @if(isset($academicHistory['high_school']['gpa']))
                                        <p class="mb-1"><strong>GPA:</strong> {{ $academicHistory['high_school']['gpa'] }}</p>
                                    @endif
                                    @if(isset($academicHistory['high_school']['major_subjects']) && is_array($academicHistory['high_school']['major_subjects']))
                                        <p class="mb-1"><strong>Major Subjects:</strong> {{ implode(', ', $academicHistory['high_school']['major_subjects']) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(empty($academicHistory) || (isset($academicHistory['high_school']) && empty(array_filter($academicHistory['high_school']))))
                            <p class="text-muted">No academic history recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Qualifications -->
    @if($student->studentProfile?->qualifications)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Qualifications & Test Scores</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $qualifications = $student->studentProfile->qualifications;
                            if (is_string($qualifications)) {
                                $qualifications = json_decode($qualifications, true) ?? [];
                            }
                            if (!is_array($qualifications)) {
                                $qualifications = [];
                            }
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Test Scores</h6>
                                @if(isset($qualifications['sat_score']) && $qualifications['sat_score'])
                                    <p class="mb-1"><strong>SAT Score:</strong> {{ $qualifications['sat_score'] }}</p>
                                @endif
                                @if(isset($qualifications['act_score']) && $qualifications['act_score'])
                                    <p class="mb-1"><strong>ACT Score:</strong> {{ $qualifications['act_score'] }}</p>
                                @endif
                                @if(isset($qualifications['toefl_score']) && $qualifications['toefl_score'])
                                    <p class="mb-1"><strong>TOEFL Score:</strong> {{ $qualifications['toefl_score'] }}</p>
                                @endif
                                @if(isset($qualifications['ielts_score']) && $qualifications['ielts_score'])
                                    <p class="mb-1"><strong>IELTS Score:</strong> {{ $qualifications['ielts_score'] }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Certifications</h6>
                                @if(isset($qualifications['high_school_diploma']) && $qualifications['high_school_diploma'])
                                    <p class="mb-1"><i class="fa fa-check text-success me-2"></i>High School Diploma</p>
                                @endif
                                @if(isset($qualifications['other_certifications']) && is_array($qualifications['other_certifications']) && count($qualifications['other_certifications']) > 0)
                                    <p class="mb-1"><strong>Other Certifications:</strong></p>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($qualifications['other_certifications'] as $cert)
                                            <li><i class="fa fa-certificate text-warning me-2"></i>{{ $cert }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        @if(empty(array_filter($qualifications)))
                            <p class="text-muted">No qualifications recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
@media print {
    .btn, .breadcrumb, nav {
        display: none !important;
    }

    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }

    .semester-section {
        page-break-inside: avoid;
    }
}

.achievement-item, .history-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.achievement-item:last-child, .history-item:last-child {
    border-bottom: none;
}

.history-section {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 4px solid #17a2b8;
}
</style>
@endsection
