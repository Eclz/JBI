@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom">
                    <h4 class="mb-0 text-dark fw-bold">{{ $quiz->title }}</h4>
                    <p class="text-muted mb-0">{{ $quiz->course->name }}</p>
                </div>
                <div class="card-body">
                    <p>{{ $quiz->description }}</p>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Duration:</strong> {{ $quiz->duration }} minutes</p>
                            <p class="mb-2"><strong>Total Points:</strong> {{ $quiz->questions->sum('points') }}</p>
                            <p class="mb-2"><strong>Passing Score:</strong> {{ $quiz->passing_percentage }}%</p>
                        </div>
                        <div class="col-md-6">
                            @if($quiz->start_time && $quiz->end_time)
                                <p class="mb-2"><strong>Available From:</strong> {{ $quiz->start_time->format('M d, Y H:i') }}</p>
                                <p class="mb-2"><strong>Available Until:</strong> {{ $quiz->end_time->format('M d, Y H:i') }}</p>
                            @endif
                            <p class="mb-2"><strong>Max Attempts:</strong> {{ $quiz->max_attempts ?: 'Unlimited' }}</p>
                        </div>
                    </div>

                    @if($quiz->canAttempt(auth()->id()))
                        <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-play-circle me-2"></i>
                                @if($attempts->count() > 0) Retake Quiz @else Start Quiz @endif
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            You cannot attempt this quiz at this time.
                        </div>
                    @endif
                </div>
            </div>

            @if($attempts->count() > 0)
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-bold">Your Attempts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Attempt</th>
                                        <th>Date</th>
                                        <th>Score</th>
                                        <th>Time Taken</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $attempt)
                                        <tr>
                                            <td>#{{ $attempt->attempt_number }}</td>
                                            <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y H:i') : '-' }}</td>
                                            <td>
                                                @if($attempt->status === 'graded')
                                                    <span class="badge bg-{{ $attempt->percentage >= $quiz->passing_percentage ? 'success' : 'danger' }}">
                                                        {{ number_format($attempt->percentage, 1) }}% ({{ $attempt->score }}/{{ $quiz->questions->sum('points') }})
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $attempt->time_taken_seconds ? gmdate('H:i:s', $attempt->time_taken_seconds) : '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->status === 'graded' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($attempt->status === 'in_progress')
                                                    <a href="{{ route('student.quizzes.take', [$quiz, $attempt]) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-play me-1"></i> Continue
                                                    </a>
                                                @elseif($attempt->status === 'graded' && $quiz->show_results)
                                                    <a href="{{ route('student.quizzes.result', [$quiz, $attempt]) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye me-1"></i> View Results
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark fw-bold">Quiz Statistics</h5>
                </div>
                <div class="card-body">
                    @if($bestAttempt)
                        <div class="mb-3">
                            <h6 class="text-muted">Your Best Score</h6>
                            <h2 class="text-success">{{ number_format($bestAttempt->percentage, 1) }}%</h2>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h6 class="text-muted">Attempts Made</h6>
                        <h3>{{ $attempts->count() }}@if($quiz->max_attempts)/{{ $quiz->max_attempts }}@endif</h3>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">Questions</h6>
                        <h3>{{ $quiz->questions->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark fw-bold">Instructions</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Read each question carefully</li>
                        <li>You have {{ $quiz->duration }} minutes to complete</li>
                        <li>Make sure to submit before time runs out</li>
                        @if($quiz->shuffle_questions)
                            <li>Questions will be shuffled</li>
                        @endif
                        @if(!$quiz->allow_review)
                            <li>You cannot go back once you move to the next question</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
