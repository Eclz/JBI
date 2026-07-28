@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Redesigned header section with better styling -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2" style="color: #212529; font-weight: 700; font-size: 1.75rem;">My Grades</h2>
            <p class="mb-0" style="color: #6c757d;">View your academic performance across all courses</p>
        </div>
    </div>

    <!-- Improved GPA summary cards with modern card design -->
    <div class="row g-3 mb-4">
        <!-- Current GPA Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Current GPA
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ number_format($gpa ?? 0, 2) }}
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">out of 4.0</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(102, 126, 234, 0.1);">
                            <i class="bi bi-mortarboard fs-4" style="color: #667eea;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Credits Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Total Credits
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ $totalCredits ?? 0 }}
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">credits earned</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-award fs-4" style="color: #198754;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Courses Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Completed Courses
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ $completedCourses ?? 0 }}
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">courses finished</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-check-circle fs-4" style="color: #0d6efd;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Score Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Average Score
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ number_format($averageScore ?? 0, 1) }}%
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">overall average</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(255, 193, 7, 0.1);">
                            <i class="bi bi-graph-up-arrow fs-4" style="color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Improved course grades table with modern card design -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Course Grades</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Course</th>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Code</th>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Credits</th>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Score</th>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Letter Grade</th>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                                    <th class="px-4 py-3 border-0" style="color: #6c757d; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grades as $grade)
                                <tr style="border-top: 1px solid #f1f3f5;">
                                    <td class="px-4 py-3">
                                        <div style="color: #212529; font-weight: 600; font-size: 0.875rem;">{{ $grade->course->name }}</div>
                                        <div style="color: #6c757d; font-size: 0.8rem;">{{ $grade->course->semester->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span style="color: #495057; font-family: monospace; font-size: 0.875rem;">{{ $grade->course->course_code }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span style="color: #212529; font-weight: 500;">{{ $grade->course->credits }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span style="color: #212529; font-weight: 600; font-size: 0.95rem;">{{ number_format($grade->final_grade ?? 0, 1) }}%</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge px-3 py-2" style="font-size: 0.875rem; font-weight: 600;
                                            @if($grade->letter_grade == 'A' || $grade->letter_grade == 'A+')
                                                background-color: #d1f4e0; color: #0f5132;
                                            @elseif($grade->letter_grade == 'B' || $grade->letter_grade == 'B+')
                                                background-color: #cfe2ff; color: #084298;
                                            @elseif($grade->letter_grade == 'C' || $grade->letter_grade == 'C+')
                                                background-color: #fff3cd; color: #997404;
                                            @else
                                                background-color: #f8d7da; color: #842029;
                                            @endif">
                                            {{ $grade->letter_grade ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge px-2 py-1" style="font-size: 0.75rem; font-weight: 500;
                                            @if($grade->status == 'completed')
                                                background-color: #d1f4e0; color: #0f5132;
                                            @elseif($grade->status == 'enrolled')
                                                background-color: #cfe2ff; color: #084298;
                                            @else
                                                background-color: #e9ecef; color: #495057;
                                            @endif">
                                            {{ ucfirst($grade->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('student.grades.show', $grade->course_id) }}" class="btn btn-sm" style="color: #667eea; font-weight: 500; text-decoration: none;">
                                            View Details →
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                            <div class="mb-3">
                                                <i class="bi bi-file-earmark-text" style="font-size: 3rem; color: #adb5bd;"></i>
                                            </div>
                                            <h5 class="mb-2" style="color: #495057; font-weight: 600;">No grades available</h5>
                                            <p class="mb-0" style="color: #6c757d;">Grades will appear here once your instructors post them.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($grades->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $grades->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
