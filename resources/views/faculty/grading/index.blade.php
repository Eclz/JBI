@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 600;">Grading Management</h2>
            <p class="text-muted mb-0">Manage grades and assessments for all your courses</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Courses</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $courses->count() }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-journal-text" style="font-size: 24px; color: white;"></i>
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
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Students</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $courses->sum('enrolled_students_count') }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-people" style="font-size: 24px; color: white;"></i>
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
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Pending Grades</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $pendingGrades }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-hourglass-split" style="font-size: 24px; color: white;"></i>
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
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Published Grades</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ array_sum(array_column($gradingStats, 'graded')) }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check-circle" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Grading Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4" style="color: #1a202c; font-weight: 600;">Course Grading Overview</h5>

            @if($courses->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <p class="mt-3 text-muted">No courses assigned yet</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f7fafc;">
                            <tr>
                                <th style="color: #4a5568; font-weight: 600;">Course</th>
                                <th style="color: #4a5568; font-weight: 600;">Code</th>
                                <th style="color: #4a5568; font-weight: 600;">Students</th>
                                <th style="color: #4a5568; font-weight: 600;">Graded</th>
                                <th style="color: #4a5568; font-weight: 600;">Pending</th>
                                <th style="color: #4a5568; font-weight: 600;">Average</th>
                                <th style="color: #4a5568; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                @php
                                    $stats = $gradingStats[$course->id] ?? [];
                                    $progress = $stats['total_students'] > 0 ? round(($stats['graded'] / $stats['total_students']) * 100) : 0;
                                @endphp
                                <tr>
                                    <td style="color: #2d3748; font-weight: 500;">{{ $course->name }}</td>
                                    <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                    <td style="color: #4a5568;">{{ $stats['total_students'] ?? 0 }}</td>
                                    <td><span class="badge bg-success">{{ $stats['graded'] ?? 0 }}</span></td>
                                    <td><span class="badge bg-warning">{{ $stats['pending'] ?? 0 }}</span></td>
                                    <td style="color: #4a5568;">{{ number_format($stats['average'] ?? 0, 1) }}%</td>
                                    <td>
                                        <a href="{{ route('faculty.courses.grades.index', $course) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil me-1"></i>Manage
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
