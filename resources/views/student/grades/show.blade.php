@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::check() && Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary px-2 py-1 fs-6">{{ $course->course_code ?? $course->code }}</span>
                <h2 class="mb-0 fw-bold text-dark">{{ $course->name }}</h2>
            </div>
            <p class="text-muted mb-0">
                Course Grade Summary & Breakdown &bull; {{ $course->credits ?? 3 }} Credits
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-outline-primary fw-semibold">
                <i class="bi bi-journal-bookmark me-1"></i>Course Page
            </a>
            <a href="{{ route('student.grades.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>All Grades
            </a>
        </div>
    </div>

    @php
        $letterGrade = 'F';
        $gradePoints = 0.0;
        $badgeClass = 'bg-danger';

        if ($percentage >= 90) {
            $letterGrade = 'A';
            $gradePoints = 4.0;
            $badgeClass = 'bg-success';
        } elseif ($percentage >= 80) {
            $letterGrade = 'B';
            $gradePoints = 3.0;
            $badgeClass = 'bg-primary';
        } elseif ($percentage >= 70) {
            $letterGrade = 'C';
            $gradePoints = 2.0;
            $badgeClass = 'bg-info text-dark';
        } elseif ($percentage >= 60) {
            $letterGrade = 'D';
            $gradePoints = 1.0;
            $badgeClass = 'bg-warning text-dark';
        }
    @endphp

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <!-- Cumulative Score -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small fw-semibold text-muted mb-1">Cumulative Score</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ number_format($percentage, 1) }}%</h3>
                            <small class="text-muted">Weighted Average</small>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(37, 99, 235, 0.1);">
                            <i class="bi bi-graph-up fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equivalent Grade -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small fw-semibold text-muted mb-1">Letter Grade</p>
                            <h3 class="fw-bold mb-0">
                                <span class="badge {{ $badgeClass }} fs-4 px-3">{{ $letterGrade }}</span>
                            </h3>
                            <small class="text-muted">Points: {{ number_format($gradePoints, 1) }} / 4.0</small>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-award fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Points Earned -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small fw-semibold text-muted mb-1">Points Earned</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $totalPoints }} <span class="fs-6 text-muted">/ {{ $maxPoints }}</span></h3>
                            <small class="text-muted">Total Graded Points</small>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-star fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graded Items Count -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase small fw-semibold text-muted mb-1">Graded Assessments</p>
                            <h3 class="fw-bold mb-0 text-dark">{{ $grades->count() }}</h3>
                            <small class="text-muted">Published Grades</small>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-check2-all fs-4 text-purple" style="color: #8b5cf6;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Grades Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-journal-check me-2 text-primary"></i>Assessment Grade Breakdown
            </h5>
            <span class="badge bg-light text-dark border">{{ $grades->count() }} Records</span>
        </div>
        <div class="card-body p-0">
            @if($grades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th style="padding: 1rem;">Assessment Title</th>
                                <th style="padding: 1rem;">Type</th>
                                <th style="padding: 1rem;">Points Earned</th>
                                <th style="padding: 1rem;">Percentage</th>
                                <th style="padding: 1rem;">Letter Grade</th>
                                <th style="padding: 1rem;">Feedback / Comments</th>
                                <th style="padding: 1rem;">Graded Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $grade)
                                @php
                                    $itemPercentage = $grade->points_possible > 0 ? ($grade->points_earned / $grade->points_possible) * 100 : ($grade->percentage ?? 0);
                                @endphp
                                <tr>
                                    <td style="padding: 1rem;">
                                        <div class="fw-bold text-dark">
                                            {{ $grade->assignment->title ?? $grade->title ?? 'Course Assessment' }}
                                        </div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span class="badge bg-light text-dark border text-capitalize">
                                            {{ $grade->grade_type ?? $grade->assignment?->type ?? 'Assignment' }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;" class="fw-bold text-dark">
                                        {{ $grade->points_earned }} / {{ $grade->points_possible }}
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span class="fw-bold {{ $itemPercentage >= 70 ? 'text-success' : ($itemPercentage >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($itemPercentage, 1) }}%
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span class="badge bg-secondary px-2 py-1">
                                            {{ $grade->letter_grade ?? ($itemPercentage >= 90 ? 'A' : ($itemPercentage >= 80 ? 'B' : ($itemPercentage >= 70 ? 'C' : ($itemPercentage >= 60 ? 'D' : 'F')))) }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        @if($grade->comments || $grade->feedback)
                                            <span class="small text-muted" title="{{ $grade->comments ?? $grade->feedback }}">
                                                <i class="bi bi-chat-left-text me-1 text-primary"></i>
                                                {{ Str::limit($grade->comments ?? $grade->feedback, 40) }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem;" class="small text-muted">
                                        {{ $grade->graded_at ? \Carbon\Carbon::parse($grade->graded_at)->format('M d, Y') : ($grade->updated_at ? $grade->updated_at->format('M d, Y') : '-') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                    <h5 class="fw-bold text-dark mt-3">No Grades Published Yet</h5>
                    <p class="text-muted mb-0">Your lecturer has not published grades for this course yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
