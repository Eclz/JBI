@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">
                <i class="bi bi-patch-question me-2" style="color: #8b5cf6;"></i>
                Quizzes & Practice Tests
            </h1>
            <p class="text-muted mb-0">Test your knowledge and prepare for exams</p>
        </div>
    </div>

    <!-- Quiz Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: #dbeafe;">
                                <i class="bi bi-clipboard-check" style="font-size: 1.5rem; color: #3b82f6;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1" style="font-size: 0.875rem;">Available Quizzes</p>
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 600;">{{ $availableQuizzes->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: #dcfce7;">
                                <i class="bi bi-check-circle" style="font-size: 1.5rem; color: #22c55e;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1" style="font-size: 0.875rem;">Completed</p>
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 600;">{{ $completedAttempts->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: #fef3c7;">
                                <i class="bi bi-star" style="font-size: 1.5rem; color: #f59e0b;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1" style="font-size: 0.875rem;">Average Score</p>
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 600;">
                                {{ $completedAttempts->avg('percentage') ? number_format($completedAttempts->avg('percentage'), 1) : '0' }}%
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px; background-color: #e0e7ff;">
                                <i class="bi bi-trophy" style="font-size: 1.5rem; color: #8b5cf6;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1" style="font-size: 0.875rem;">Best Score</p>
                            <h3 class="mb-0" style="color: #1e293b; font-weight: 600;">
                                {{ $completedAttempts->max('percentage') ? number_format($completedAttempts->max('percentage'), 1) : '0' }}%
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Quizzes -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom" style="padding: 1.25rem;">
            <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Available Quizzes</h5>
        </div>
        <div class="card-body p-0">
            @if($availableQuizzes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Quiz Title</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Course</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Type</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Duration</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Questions</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Attempts</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableQuizzes as $quiz)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 500; color: #1e293b;">{{ $quiz->title }}</div>
                                    @if($quiz->description)
                                        <div class="text-muted" style="font-size: 0.875rem;">{{ Str::limit($quiz->description, 60) }}</div>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge" style="background-color: #e0e7ff; color: #6366f1; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-weight: 500;">
                                        {{ $quiz->course->code }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    @if($quiz->quiz_type === 'practice')
                                        <span class="badge bg-info">Practice</span>
                                    @elseif($quiz->quiz_type === 'graded')
                                        <span class="badge bg-warning">Graded</span>
                                    @else
                                        <span class="badge bg-primary">Exam Prep</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; color: #64748b;">{{ $quiz->duration_minutes }} min</td>
                                <td style="padding: 1rem; color: #64748b;">{{ $quiz->questions->count() }}</td>
                                <td style="padding: 1rem;">
                                    @php
                                        $attempts = $quiz->studentAttempts(auth()->id())->count();
                                    @endphp
                                    <span class="text-muted">{{ $attempts }}/{{ $quiz->max_attempts }}</span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($quiz->canAttempt(auth()->id()))
                                        <a href="{{ route('student.quizzes.show', $quiz) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-play-fill me-1"></i>Start Quiz
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="bi bi-lock-fill me-1"></i>Unavailable
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                    <p class="text-muted mt-3 mb-0">No quizzes available at the moment.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Quiz Attempts -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom" style="padding: 1.25rem;">
            <h5 class="mb-0" style="color: #1e293b; font-weight: 600;">Recent Quiz Attempts</h5>
        </div>
        <div class="card-body p-0">
            @if($completedAttempts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8fafc;">
                            <tr>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Quiz</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Course</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Date</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Score</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem;">Grade</th>
                                <th style="padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.875rem; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedAttempts as $attempt)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem; font-weight: 500; color: #1e293b;">{{ $attempt->quiz->title }}</td>
                                <td style="padding: 1rem;">
                                    <span class="badge" style="background-color: #e0e7ff; color: #6366f1; padding: 0.375rem 0.75rem; border-radius: 0.375rem;">
                                        {{ $attempt->quiz->course->code }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #64748b;">{{ $attempt->submitted_at->format('M d, Y') }}</td>
                                <td style="padding: 1rem;">
                                    <span style="font-weight: 600; color: #1e293b;">{{ $attempt->score }}/{{ $attempt->quiz->total_marks }}</span>
                                    <span class="text-muted">({{ number_format($attempt->percentage, 1) }}%)</span>
                                </td>
                                <td style="padding: 1rem;">
                                    @php
                                        $gradeColor = $attempt->grade === 'A' ? '#22c55e' :
                                                     ($attempt->grade === 'B' ? '#3b82f6' :
                                                     ($attempt->grade === 'C' ? '#f59e0b' : '#ef4444'));
                                    @endphp
                                    <span class="badge" style="background-color: {{ $gradeColor }}; color: white; padding: 0.375rem 0.75rem;">
                                        {{ $attempt->grade }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <a href="{{ route('student.quizzes.result', [$attempt->quiz, $attempt]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View Result
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $completedAttempts->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                    <p class="text-muted mt-3 mb-0">No quiz attempts yet. Start a quiz to see your results here!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
