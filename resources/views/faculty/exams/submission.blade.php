@extends('layouts.app')

@section('title', 'Submission - ' . $exam->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('faculty.exams.index') }}">Exams</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('faculty.exams.show', $exam) }}">{{ $exam->title }}</a></li>
                    <li class="breadcrumb-item active">Submission</li>
                </ol>
            </nav>
            <h1 class="h4 mb-1">{{ $attempt->user->name }} - Submission</h1>
            <div class="text-muted">{{ $attempt->user->email }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('faculty.exams.show', $exam) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Exam
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Student Submission</h5>
                </div>
                <div class="card-body">
                    @if($attempt->submission_file_url)
                        <div class="mb-3">
                            <label class="form-label">Uploaded File</label>
                            <div>
                                <a href="{{ Storage::url($attempt->submission_file_url) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-download me-1"></i>Download Submission
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($attempt->answers)
                        <div class="mb-0">
                            <label class="form-label">Typed Answers</label>
                            <div class="border rounded p-3 bg-light">
                                {!! $attempt->answers !!}
                            </div>
                        </div>
                    @else
                        @if(!$attempt->submission_file_url)
                            <div class="text-muted">No submission content available.</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Grade Submission</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('faculty.exams.attempts.grade', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Marks Obtained (out of {{ $exam->total_marks }})</label>
                            <input type="number" name="marks_obtained" class="form-control"
                                   value="{{ $attempt->marks_obtained }}"
                                   min="0" max="{{ $exam->total_marks }}" step="0.5" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Feedback</label>
                            <textarea name="feedback" class="form-control" rows="4">{{ $attempt->feedback }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Grade</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <div class="mb-2"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}</div>
                    <div class="mb-2"><strong>Submitted:</strong> {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y H:i') : 'Not submitted' }}</div>
                    <div class="mb-0"><strong>Started:</strong> {{ $attempt->started_at ? $attempt->started_at->format('M d, Y H:i') : 'Not started' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
