@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-dark fw-bold">My Exams</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Exams</li>
                        </ol>
                    </nav>
                </div>
            </div>

            {{-- Active Exams --}}
            @if($activeExams->count() > 0)
                <div class="alert alert-warning border-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>You have {{ $activeExams->count() }} active exam(s) available right now!</strong>
                </div>

                <div class="row mb-4">
                    @foreach($activeExams as $exam)
                        @php
                            $attempt = $exam->studentAttempt(auth()->id());
                        @endphp
                        <div class="col-md-6 mb-3">
                            <div class="card border-success shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 text-dark fw-bold">{{ $exam->title }}</h5>
                                            <p class="text-muted mb-0">{{ $exam->course->name }}</p>
                                        </div>
                                        <span class="badge bg-success">Active Now</span>
                                    </div>

                                    <p class="text-muted mb-3">{{ $exam->description }}</p>

                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <div class="border-end">
                                                <strong class="d-block text-primary">{{ $exam->duration_minutes }}</strong>
                                                <small class="text-muted">Minutes</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-end">
                                                <strong class="d-block text-primary">{{ $exam->total_marks }}</strong>
                                                <small class="text-muted">Total Marks</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <strong class="d-block text-primary">{{ $exam->exam_type }}</strong>
                                            <small class="text-muted">Type</small>
                                        </div>
                                    </div>

                                    @if($attempt)
                                        @if($attempt->status === 'in_progress')
                                            <a href="{{ route('student.exams.take', $exam) }}" class="btn btn-warning w-100">
                                                <i class="bi bi-play-circle me-1"></i> Resume Exam
                                            </a>
                                            @if(!empty($timeLeftByExam[$exam->id]))
                                                <div class="mt-2 text-center">
                                                    <small class="text-danger">
                                                        <i class="bi bi-alarm me-1"></i>
                                                        Time left:
                                                        <span class="js-time-left" data-remaining="{{ $timeLeftByExam[$exam->id] }}"></span>
                                                    </small>
                                                </div>
                                            @endif
                                        @elseif($attempt->status === 'submitted')
                                            @if(!empty($timeLeftByExam[$exam->id]) && !empty($remainingSubmissionsByExam[$exam->id]))
                                                <a href="{{ route('student.exams.take', $exam) }}" class="btn btn-warning w-100">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Resubmit ({{ 2 - $remainingSubmissionsByExam[$exam->id] }}/2)
                                                </a>
                                            @else
                                                <button class="btn btn-secondary w-100" disabled>
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Submitted ({{ $attempt->submission_count ?? 0 }}/2)
                                                </button>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-success w-100">
                                            <i class="bi bi-play-fill me-1"></i> Start Exam
                                        </a>
                                    @endif

                                    <div class="mt-2 text-center">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Ends: {{ $exam->end_time->format('M d, Y g:i A') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Upcoming Exams --}}
            @if($upcomingExams->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 text-dark">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            Upcoming Exams ({{ $upcomingExams->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($upcomingExams as $exam)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="card-title mb-1 text-dark fw-bold">{{ $exam->title }}</h6>
                                                    <p class="text-muted small mb-0">{{ $exam->course->name }}</p>
                                                </div>
                                                <span class="badge bg-warning">Upcoming</span>
                                            </div>

                                            <div class="small text-muted mb-2">
                                                <div><i class="bi bi-clock me-1"></i> Duration: {{ $exam->duration_minutes }} minutes</div>
                                                <div><i class="bi bi-calendar me-1"></i> Starts: {{ $exam->start_time->format('M d, Y g:i A') }}</div>
                                                @if($exam->required_payment > 0)
                                                    <div class="text-danger">
                                                        <i class="bi bi-cash me-1"></i> Payment Required: {{ $currencyCode }} {{ number_format($exam->required_payment, 2) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Completed Attempts --}}
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="bi bi-check-circle text-primary me-2"></i>
                        Completed & Expired Exams
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($completedAttempts->count() > 0 || $expiredAttempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-dark">Exam</th>
                                        <th class="text-dark">Course</th>
                                        <th class="text-dark">Submitted</th>
                                        <th class="text-dark">Grade</th>
                                        <th class="text-dark">Status</th>
                                        <th class="text-dark">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedAttempts as $attempt)
                                        <tr>
                                            <td class="text-dark fw-medium">{{ $attempt->exam->title }}</td>
                                            <td class="text-muted">{{ $attempt->exam->course->name }}</td>
                                            <td class="text-muted">{{ $attempt->submitted_at->format('M d, Y g:i A') }}</td>
                                            <td>
                                                @if($attempt->status === 'graded')
                                                    <span class="fw-bold text-dark">
                                                        {{ $attempt->marks_obtained }}/{{ $attempt->exam->total_marks }}
                                                    </span>
                                                    <br>
                                                    <span class="badge {{ $attempt->hasPassed() ? 'bg-success' : 'bg-danger' }}">
                                                        {{ number_format($attempt->percentage, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->status === 'graded')
                                                    <span class="badge bg-success">Graded</span>
                                                @else
                                                    <span class="badge bg-info">Awaiting Grade</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('student.exams.show', $attempt->exam) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach($expiredAttempts as $attempt)
                                        <tr>
                                            <td class="text-dark fw-medium">{{ $attempt->exam->title }}</td>
                                            <td class="text-muted">{{ $attempt->exam->course->name }}</td>
                                            <td class="text-muted">—</td>
                                            <td><span class="text-muted">Not submitted</span></td>
                                            <td><span class="badge bg-secondary">Expired</span></td>
                                            <td>
                                                <a href="{{ route('student.exams.show', $attempt->exam) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{ $completedAttempts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x display-1 text-muted"></i>
                            <h5 class="mt-3 text-dark">No Completed Exams</h5>
                            <p class="text-muted">You haven't completed any exams yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatCountdown(totalSeconds) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return (
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
    }

    function updateCountdowns() {
        document.querySelectorAll('.js-time-left').forEach(function(el) {
            let remaining = parseInt(el.getAttribute('data-remaining'), 10) || 0;
            if (remaining <= 0) {
                el.textContent = '00:00:00';
                return;
            }
            el.textContent = formatCountdown(remaining);
            remaining -= 1;
            el.setAttribute('data-remaining', remaining);
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
</script>
@endpush
@endsection
