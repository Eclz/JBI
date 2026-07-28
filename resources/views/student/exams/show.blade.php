@extends('layouts.app')

@section('title', 'Exam - ' . $exam->title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">{{ $exam->title }}</h1>
            <div class="text-muted">{{ $exam->course->name }} ({{ $exam->course->code }})</div>
        </div>
        <div class="mt-2">
            @if($status === 'upcoming')
                <span class="badge bg-info">Upcoming</span>
            @elseif($status === 'active')
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Completed</span>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Exam Details</h5>
                </div>
                <div class="card-body">
                    @if($exam->description)
                        <p class="text-muted mb-3">{{ $exam->description }}</p>
                    @endif
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="border rounded py-2">
                                <div class="fw-semibold text-primary">{{ $exam->duration_minutes }}</div>
                                <div class="text-muted small">Minutes</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="border rounded py-2">
                                <div class="fw-semibold text-primary">{{ $exam->total_marks }}</div>
                                <div class="text-muted small">Total Marks</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="border rounded py-2">
                                <div class="fw-semibold text-primary">{{ $exam->passing_marks }}</div>
                                <div class="text-muted small">Pass Mark</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="border rounded py-2">
                                <div class="fw-semibold text-primary">{{ ucfirst($exam->exam_type) }}</div>
                                <div class="text-muted small">Exam Type</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="border rounded py-2">
                                <div class="fw-semibold text-primary">{{ ucfirst($exam->exam_mode ?? 'online') }}</div>
                                <div class="text-muted small">Exam Mode</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">Start Time</span>
                        <span class="fw-semibold">{{ $exam->start_time->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">End Time</span>
                        <span class="fw-semibold">{{ $exam->end_time->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            @if($exam->exam_paper_url || $exam->answer_booklet_url)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Downloads</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        @if($exam->exam_paper_url)
                            <a class="btn btn-outline-primary" href="{{ route('student.exams.download-paper', $exam) }}">
                                <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Question Paper
                            </a>
                        @endif
                        @if($exam->answer_booklet_url)
                            <a class="btn btn-outline-secondary" href="{{ route('student.exams.download-booklet', $exam) }}">
                                <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Answer Booklet
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($exam->instructions)
            <div class="card border-warning shadow-sm">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0 text-warning">Instructions</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($exam->instructions)) !!}
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Exam Access</h5>
                </div>
                <div class="card-body">
                    @if($exam->required_payment > 0 && !$hasPaid)
                            <div class="alert alert-danger">
                                <div class="fw-semibold mb-2">Payment Required</div>
                                <div class="small">
                                Pay <strong>{{ $currencyCode }} {{ number_format($exam->required_payment, 2) }}</strong> to attempt this exam.
                                </div>
                                <a href="{{ route('student.fees.index') }}" class="btn btn-sm btn-danger mt-3">Pay Now</a>
                            </div>
                    @else
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Submissions Used</span>
                                <span class="fw-semibold">{{ $attemptsCount }} / {{ $maxAttempts }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $maxAttempts > 0 ? ($attemptsCount / $maxAttempts) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        @if($canAttempt)
                            @if($status === 'active')
                                <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-play-circle me-2"></i>Start Exam
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-secondary text-center mb-0">
                                    Exam not yet available
                                </div>
                            @endif
                        @elseif($attempt && $attempt->status === 'submitted' && $status === 'active')
                            @if($canResubmit)
                                <a href="{{ route('student.exams.take', $exam) }}" class="btn btn-warning w-100">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Resubmit ({{ $attemptsCount }}/{{ $maxAttempts }})
                                </a>
                            @else
                                <div class="alert alert-secondary text-center mb-0">
                                    Submitted ({{ $attemptsCount }}/{{ $maxAttempts }})
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning text-center mb-0">
                                Maximum attempts reached
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($attempts->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Your Attempts</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($attempts as $attemptItem)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Attempt #{{ $loop->iteration }}</span>
                                    <span class="small text-muted">{{ optional($attemptItem->started_at)->format('M d, Y h:i A') }}</span>
                                </div>
                                <div class="small text-muted">
                                    Status: {{ ucfirst(str_replace('_', ' ', $attemptItem->status)) }}
                                    @if($attemptItem->marks_obtained !== null)
                                        · Score: {{ $attemptItem->marks_obtained }} / {{ $exam->total_marks }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
