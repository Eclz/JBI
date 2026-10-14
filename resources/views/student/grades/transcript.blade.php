@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Academic Transcript</h2>
            <p class="text-muted mb-0">Published academic results</p>
        </div>
        <button type="button" class="btn btn-outline-primary" onclick="window.print()">Print</button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-muted">Student</span>
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Admission number</span>
                    <div class="fw-semibold">{{ Auth::user()->studentProfile?->admission_number ?? 'Not available' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Program</span>
                    <div class="fw-semibold">{{ Auth::user()->studentProfile?->program ?? 'Not available' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="text-muted">Current GPA</span>
                    <div class="fw-semibold">{{ number_format($gpa ?? 0, 2) }} / 4.00</div>
                </div>
            </div>
        </div>
    </div>

    @forelse($grades as $courseGrades)
        @php($course = $courseGrades->first()->course)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between gap-2">
                <div class="fw-semibold">{{ $course->course_code ?? $course->code }} — {{ $course->name }}</div>
                <div class="text-muted">{{ $course->semester->name ?? 'Semester not specified' }}</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Assessment</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Letter grade</th>
                            <th>Published</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courseGrades as $grade)
                            <tr>
                                <td>{{ $grade->assignment->title ?? ucfirst($grade->grade_type) }}</td>
                                <td>{{ number_format((float) $grade->points_earned, 2) }} / {{ number_format((float) $grade->points_possible, 2) }}</td>
                                <td>{{ number_format((float) $grade->percentage, 2) }}%</td>
                                <td>{{ $grade->letter_grade ?? '—' }}</td>
                                <td>{{ $grade->graded_at?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No published grades are available for your transcript yet.</div>
    @endforelse
</div>
@endsection
