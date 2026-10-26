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
            <span class="badge bg-{{ $exam->status_badge_class }} fs-6">{{ $exam->status_label }}</span>
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
                    <div class="d-flex justify-content-between {{ $exam->room_number ? 'border-bottom pb-2 mb-2' : '' }}">
                        <span class="text-muted">End Time</span>
                        <span class="fw-semibold">{{ $exam->end_time->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($exam->room_number)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Room / Venue</span>
                        <span class="fw-semibold">{{ $exam->room_number }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @php
                $mode = strtolower($exam->exam_mode ?? 'online');
            @endphp

            @if(in_array($mode, ['offline', 'physical'], true))
                <div class="card shadow-sm mb-4 border-info">
                    <div class="card-header bg-info bg-opacity-10 py-3">
                        <h5 class="mb-0 text-info fw-semibold"><i class="bi bi-geo-alt me-2"></i>Physical Examination</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">
                            This is an offline/physical examination conducted on campus{{ $exam->room_number ? ' in Room ' . $exam->room_number : '' }}.
                            Question papers and physical answer booklets are distributed directly in the exam room and are not accessible online.
                        </p>
                    </div>
                </div>
            @else
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-download me-2 text-primary"></i>Examination Materials</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            @if($mode === 'hybrid')
                                @if($exam->exam_paper_url)
                                    @if(!empty($canDownloadPaper))
                                        <a class="btn btn-outline-primary" href="{{ route('student.exams.download-paper', $exam) }}">
                                            <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Question Paper
                                        </a>
                                    @else
                                        <div class="alert alert-secondary mb-0 small">
                                            <i class="bi bi-clock me-1"></i>
                                            <strong>Question Paper:</strong> The question paper will unlock and become downloadable once you start the examination. Your timer will begin immediately.
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="alert alert-light border mb-0 small text-muted">
                                    <i class="bi bi-laptop me-1 text-primary"></i>
                                    <strong>Online Examination:</strong> This exam is attempted directly online using the built-in exam interface. Question papers are not downloaded.
                                </div>
                            @endif

                            @if($exam->answer_booklet_url)
                                @if(!empty($canDownloadAnswerBooklet))
                                    <a class="btn btn-outline-secondary" href="{{ route('student.exams.download-booklet', $exam) }}">
                                        <i class="bi bi-file-earmark-arrow-down me-2"></i>Download Answer Booklet / Marking Guide
                                    </a>
                                @else
                                    <div class="alert alert-light border mb-0 small text-muted">
                                        <i class="bi bi-shield-lock me-1"></i>
                                        <strong>Answer Booklet:</strong> Available to download after you submit your attempt or once the examination has closed.
                                    </div>
                                @endif
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
                    @if(isset($isTuitionCleared) && !$isTuitionCleared)
                        <div class="alert alert-danger shadow-sm border-0 mb-3" style="border-left: 4px solid #dc3545 !important;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-shield-x fs-3 text-danger me-2"></i>
                                <div>
                                    <strong class="d-block text-danger">Exam Ineligible: Tuition Unpaid</strong>
                                    <p class="small text-dark mb-2">You currently have an uncleared tuition balance of <strong>{{ $currencyCode }} {{ number_format($tuitionBalance, 2) }}</strong>. University regulations require complete tuition payment (zero balance) to sit for examinations.</p>
                                    <a href="{{ route('student.fees.index') }}" class="btn btn-sm btn-danger fw-bold">
                                        <i class="bi bi-credit-card me-1"></i>Pay Tuition in Fee Portal
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-outline-danger w-100" disabled>
                            <i class="bi bi-lock-fill me-1"></i>Exam Access Blocked
                        </button>
                    @elseif($exam->required_payment > 0 && !$hasPaid)
                        <div class="alert alert-danger">
                            <div class="fw-semibold mb-2">Payment Required</div>
                            <div class="small">
                            Pay <strong>{{ $currencyCode }} {{ number_format($exam->required_payment, 2) }}</strong> to attempt this exam.
                            </div>
                            <a href="{{ route('student.fees.index') }}" class="btn btn-sm btn-danger mt-3">Pay Now</a>
                        </div>
                    @else
                        @if(isset($isTuitionCleared) && $isTuitionCleared)
                            <div class="mb-3">
                                <a href="{{ route('student.exams.permit') }}" class="btn btn-outline-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-file-earmark-check me-1"></i>Print Examination Permit
                                </a>
                            </div>
                        @endif
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Attempt Status</span>
                                <span class="fw-semibold">
                                    @if($attempt && in_array($attempt->status, ['submitted', 'graded'], true))
                                        Completed (1 / 1)
                                    @elseif($attempt && $attempt->status === 'in_progress')
                                        In Progress
                                    @else
                                        Not Started (0 / 1)
                                    @endif
                                </span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar {{ $attempt && in_array($attempt->status, ['submitted', 'graded'], true) ? 'bg-success' : ($attempt && $attempt->status === 'in_progress' ? 'bg-warning' : '') }}" style="width: {{ $attempt && in_array($attempt->status, ['submitted', 'graded'], true) ? 100 : ($attempt && $attempt->status === 'in_progress' ? 50 : 0) }}%"></div>
                            </div>
                        </div>

                        @if($attempt && in_array($attempt->status, ['submitted', 'graded'], true))
                            <div class="alert alert-success border-0 shadow-sm mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i>
                                    <div>
                                        <strong class="d-block text-success">Exam Completed</strong>
                                        <small class="text-muted">
                                            Submitted {{ optional($attempt->submitted_at)->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-secondary w-100 fw-bold" disabled>
                                <i class="bi bi-lock-fill me-1"></i> Exam Completed (Cannot Re-attempt)
                            </button>
                        @elseif($attempt && $attempt->status === 'in_progress')
                            <a href="{{ route('student.exams.take', $exam) }}" class="btn btn-warning w-100 fw-bold">
                                <i class="bi bi-play-circle me-2"></i>Resume Exam Attempt
                            </a>
                        @elseif($status === 'closed')
                            <div class="alert alert-danger text-center mb-0">
                                <i class="bi bi-x-circle me-1"></i> Examination Closed / Ended
                            </div>
                        @elseif($status === 'upcoming')
                            <div class="alert alert-secondary text-center mb-0">
                                <i class="bi bi-clock me-1"></i> Exam Not Yet Available (Starts {{ $exam->start_time->format('M d, Y h:i A') }})
                            </div>
                        @elseif($canAttempt)
                            @if($status === 'active')
                                <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 fw-bold">
                                        <i class="bi bi-play-circle me-2"></i>Start Exam
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-secondary text-center mb-0">
                                    Exam not currently available
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning text-center mb-0">
                                Exam not available
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
