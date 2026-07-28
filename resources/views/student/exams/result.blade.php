@extends('layouts.app')

@section('title', 'Exam Result - ' . $exam->title)

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="mb-0">Exam Result</h4>
        </div>
        <div class="card-body">
            <h5 class="fw-semibold mb-2">{{ $exam->title }}</h5>
            <div class="text-muted mb-4">{{ $exam->course->name }} ({{ $exam->course->code }})</div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Score</div>
                        <div class="h4 mb-0">{{ $attempt->marks_obtained ?? 0 }}</div>
                        <div class="text-muted small">out of {{ $exam->total_marks }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Percentage</div>
                        <div class="h4 mb-0">{{ number_format($attempt->percentage ?? 0, 1) }}%</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Status</div>
                        <div class="h4 mb-0">
                            {{ ($attempt->marks_obtained ?? 0) >= $exam->passing_marks ? 'Passed' : 'Not Passed' }}
                        </div>
                    </div>
                </div>
            </div>

            @if($attempt->feedback)
                <div class="mt-4">
                    <h6 class="fw-semibold">Instructor Feedback</h6>
                    <div class="border rounded p-3 bg-light">{{ $attempt->feedback }}</div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('student.exams.index') }}" class="btn btn-outline-secondary">
                    Back to Exams
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
