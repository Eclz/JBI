@extends('layouts.app')

@section('title', 'Quiz Details - ' . $quiz->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('faculty.quizzes.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 600;">{{ $quiz->title }}</h1>
                <span class="badge bg-{{ $quiz->status_badge_class }} fs-6">{{ $quiz->status_label }}</span>
            </div>
            <p class="text-muted mb-0">
                <i class="bi bi-book me-1"></i>{{ $quiz->course->code }} &bull; {{ $quiz->course->name }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('faculty.quizzes.questions', $quiz) }}" class="btn btn-outline-success">
                <i class="bi bi-list-task me-1"></i>Manage Questions ({{ $quiz->questions->count() }})
            </a>
            <a href="{{ route('faculty.quizzes.edit', $quiz) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i>Edit Settings
            </a>
            @if($quiz->isActive())
                <form action="{{ route('faculty.quizzes.close', $quiz) }}" method="POST" class="d-inline" onsubmit="return confirm('Close quiz {{ addslashes($quiz->title) }}? Students will no longer be able to attempt it.');">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Close Quiz
                    </button>
                </form>
            @else
                <form action="{{ route('faculty.quizzes.reopen', $quiz) }}" method="POST" class="d-inline" onsubmit="return confirm('Reopen/Activate quiz {{ addslashes($quiz->title) }} for students?');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-play-circle me-1"></i>Open / Activate Quiz
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-white me-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total Attempts</span>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total_attempts'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Completed</span>
                            <h3 class="mb-0 fw-bold">{{ $statistics['completed'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-graph-up fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Average Score</span>
                            <h3 class="mb-0 fw-bold">{{ $statistics['average_score'] ?? 0 }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-white me-3">
                            <i class="bi bi-trophy fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Pass Rate</span>
                            <h3 class="mb-0 fw-bold">
                                @if(($statistics['completed'] ?? 0) > 0)
                                    {{ round((($statistics['pass_count'] ?? 0) / $statistics['completed']) * 100) }}%
                                @else
                                    0%
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quiz Details & Status Management -->
        <div class="col-lg-4">
            <!-- Status Controls -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-sliders me-2 text-primary"></i>Status & Controls
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('faculty.quizzes.status.update', $quiz) }}" method="POST" class="mb-3">
                        @csrf
                        <label for="status" class="form-label small fw-semibold text-muted">Current Status</label>
                        <div class="input-group">
                            <select name="status" id="status" class="form-select">
                                <option value="active" {{ $quiz->status === 'active' ? 'selected' : '' }}>Active / Open</option>
                                <option value="published" {{ $quiz->status === 'published' ? 'selected' : '' }}>Published (Scheduled)</option>
                                <option value="draft" {{ $quiz->status === 'draft' ? 'selected' : '' }}>Draft / Unpublished</option>
                                <option value="closed" {{ $quiz->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Select 'Active / Open' to immediately let students attempt this quiz.
                        </small>
                    </form>

                    <div class="d-grid gap-2">
                        @if($quiz->isActive())
                            <form action="{{ route('faculty.quizzes.close', $quiz) }}" method="POST" onsubmit="return confirm('Close quiz? Students will immediately be blocked from starting attempts.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-lock-fill me-1"></i>Close Quiz Now
                                </button>
                            </form>
                        @else
                            <form action="{{ route('faculty.quizzes.reopen', $quiz) }}" method="POST">
                                @csrf
                                <input type="hidden" name="days" value="7">
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="bi bi-unlock-fill me-1"></i>Reopen / Activate (7 Days)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quiz Parameters -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Quiz Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Duration:</span>
                            <span class="fw-semibold">{{ $quiz->duration_minutes }} minutes</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Total Marks:</span>
                            <span class="fw-semibold">{{ $quiz->total_marks }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Passing Marks:</span>
                            <span class="fw-semibold">{{ $quiz->passing_marks }} ({{ $quiz->passing_percentage }}%)</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Max Attempts:</span>
                            <span class="fw-semibold">{{ $quiz->max_attempts ?: 'Unlimited' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Start Time:</span>
                            <span class="fw-semibold">{{ $quiz->start_time ? $quiz->start_time->format('M d, Y H:i') : 'Not set' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">End Time:</span>
                            <span class="fw-semibold">{{ $quiz->end_time ? $quiz->end_time->format('M d, Y H:i') : 'Not set' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Shuffle Questions:</span>
                            <span class="fw-semibold">{{ $quiz->shuffle_questions ? 'Yes' : 'No' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Results Release:</span>
                            <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $quiz->show_results)) }}</span>
                        </li>
                    </ul>

                    @if($quiz->description)
                        <div class="mt-3">
                            <span class="text-muted small fw-semibold">Description:</span>
                            <p class="mb-0 mt-1 small">{{ $quiz->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Questions & Student Attempts -->
        <div class="col-lg-8">
            <!-- Questions Preview Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-question-circle me-2 text-primary"></i>Questions ({{ $quiz->questions->count() }})
                    </h5>
                    <a href="{{ route('faculty.quizzes.questions', $quiz) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Manage Questions
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($quiz->questions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($quiz->questions->take(5) as $index => $question)
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="fw-semibold">#{{ $index + 1 }} {{ Str::limit($question->question, 100) }}</span>
                                        <span class="badge bg-secondary ms-2">{{ $question->points }} pts</span>
                                    </div>
                                    <small class="text-muted">
                                        Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                        @if($quiz->questions->count() > 5)
                            <div class="card-footer bg-white text-center border-top">
                                <a href="{{ route('faculty.quizzes.questions', $quiz) }}" class="text-decoration-none small fw-semibold">
                                    View all {{ $quiz->questions->count() }} questions &rarr;
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard-x fs-2"></i>
                            <p class="mt-2 mb-0">No questions added yet.</p>
                            <a href="{{ route('faculty.quizzes.questions', $quiz) }}" class="btn btn-sm btn-outline-primary mt-2">
                                Add Questions Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Attempts Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-card-checklist me-2 text-primary"></i>Recent Student Attempts
                    </h5>
                    <a href="{{ route('faculty.quizzes.attempts', $quiz) }}" class="btn btn-sm btn-outline-secondary">
                        View All ({{ $quiz->attempts->count() }})
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($quiz->attempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th style="padding: 0.75rem 1rem;">Student</th>
                                        <th style="padding: 0.75rem 1rem;">Attempt #</th>
                                        <th style="padding: 0.75rem 1rem;">Score</th>
                                        <th style="padding: 0.75rem 1rem;">Status</th>
                                        <th style="padding: 0.75rem 1rem;">Submitted At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quiz->attempts->take(10) as $attempt)
                                        @php
                                            $studentName = $attempt->student?->name ?? $attempt->user?->name ?? 'Student #' . $attempt->user_id;
                                        @endphp
                                        <tr>
                                            <td style="padding: 0.75rem 1rem;">
                                                <strong>{{ $studentName }}</strong>
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                Attempt {{ $attempt->attempt_number }}
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                @if(in_array($attempt->status, ['submitted', 'graded', 'completed']))
                                                    <span class="badge bg-{{ ($attempt->percentage ?? 0) >= $quiz->passing_percentage ? 'success' : 'danger' }}">
                                                        {{ number_format($attempt->score ?? 0, 1) }} / {{ $quiz->total_marks }} ({{ number_format($attempt->percentage ?? 0, 1) }}%)
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">
                                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y H:i') : ($attempt->started_at ? 'Started ' . $attempt->started_at->diffForHumans() : '-') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2"></i>
                            <p class="mt-2 mb-0">No student attempts recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
