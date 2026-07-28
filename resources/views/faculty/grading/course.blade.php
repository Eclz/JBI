@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 600;">{{ $course->name }} - Grading</h2>
            <p class="text-muted mb-0">{{ $course->code }} | {{ $course->semester->name ?? 'N/A' }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('faculty.courses.gradebook', $course) }}" class="btn btn-primary">
                <i class="bi bi-table me-2"></i>View Gradebook
            </a>
            <a href="{{ route('faculty.grading.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Students</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $students->count() }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-people" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Assignments</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $assignments->count() }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-text" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Graded</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">
                                {{ $course->grades()->where('is_published', true)->distinct('user_id')->count('user_id') }}
                            </h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check-circle" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Class Average</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">
                                {{ number_format($course->grades()->where('is_published', true)->avg('percentage') ?? 0, 1) }}%
                            </h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-star" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4" style="color: #1a202c; font-weight: 600;">Course Assignments</h5>

            @if($assignments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <p class="mt-3 text-muted">No assignments created yet</p>
                    <a href="{{ route('faculty.assignments.create', ['course_id' => $course->id]) }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Create Assignment
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f7fafc;">
                            <tr>
                                <th style="color: #4a5568; font-weight: 600;">Assignment</th>
                                <th style="color: #4a5568; font-weight: 600;">Due Date</th>
                                <th style="color: #4a5568; font-weight: 600;">Points</th>
                                <th style="color: #4a5568; font-weight: 600;">Submissions</th>
                                <th style="color: #4a5568; font-weight: 600;">Graded</th>
                                <th style="color: #4a5568; font-weight: 600;">Average</th>
                                <th style="color: #4a5568; font-weight: 600;">Status</th>
                                <th style="color: #4a5568; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                @php
                                    $submissionCount = $assignment->submissions()->count();
                                    $gradedCount = $assignment->grades()->where('is_published', true)->count();
                                    $avgScore = $assignment->grades()->where('is_published', true)->avg('percentage');
                                    $isPastDue = $assignment->due_date < now();
                                @endphp
                                <tr>
                                    <td style="color: #2d3748; font-weight: 500;">{{ $assignment->title }}</td>
                                    <td style="color: #4a5568;">{{ $assignment->due_date->format('M d, Y') }}</td>
                                    <td><span class="badge bg-info">{{ $assignment->points }} pts</span></td>
                                    <td><span class="badge bg-primary">{{ $submissionCount }}/{{ $students->count() }}</span></td>
                                    <td><span class="badge bg-success">{{ $gradedCount }}</span></td>
                                    <td style="color: #4a5568;">{{ $avgScore ? number_format($avgScore, 1) . '%' : 'N/A' }}</td>
                                    <td>
                                        @if($isPastDue)
                                            <span class="badge bg-danger">Past Due</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('faculty.assignments.show', $assignment) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil me-1"></i>Grade
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Grades Overview -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <h5 class="card-title mb-4" style="color: #1a202c; font-weight: 600;">Student Grades Overview</h5>

            @if($students->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <p class="mt-3 text-muted">No students enrolled in this course</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f7fafc;">
                            <tr>
                                <th style="color: #4a5568; font-weight: 600;">Student</th>
                                <th style="color: #4a5568; font-weight: 600;">Student ID</th>
                                <th style="color: #4a5568; font-weight: 600;">Assignments Graded</th>
                                <th style="color: #4a5568; font-weight: 600;">Current Average</th>
                                <th style="color: #4a5568; font-weight: 600;">Letter Grade</th>
                                <th style="color: #4a5568; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $studentGrades = $student->grades()->where('course_id', $course->id)->where('is_published', true)->get();
                                    $avgPercentage = $studentGrades->avg('percentage') ?? 0;
                                    $letterGrade = $avgPercentage >= 90 ? 'A' : ($avgPercentage >= 80 ? 'B' : ($avgPercentage >= 70 ? 'C' : ($avgPercentage >= 60 ? 'D' : 'F')));
                                @endphp
                                <tr>
                                    <td style="color: #2d3748; font-weight: 500;">{{ $student->name }}</td>
                                    <td style="color: #4a5568;">{{ $student->studentProfile->admission_number ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ $studentGrades->count() }}/{{ $assignments->count() }}</span></td>
                                    <td style="color: #4a5568;">{{ number_format($avgPercentage, 1) }}%</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $avgPercentage >= 70 ? '#22c55e' : ($avgPercentage >= 60 ? '#f59e0b' : '#ef4444') }};">
                                            {{ $letterGrade }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('faculty.courses.gradebook', $course) }}#student-{{ $student->id }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
