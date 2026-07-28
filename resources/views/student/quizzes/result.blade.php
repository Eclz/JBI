@extends('layouts.app')

@section('title', 'Quiz Results')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-{{ $attempt->score >= $quiz->passing_marks ? 'success' : 'danger' }} text-white">
                    <h4 class="mb-0">
                        @if($attempt->score >= $quiz->passing_marks)
                            <i class="bi bi-check-circle me-2"></i>Congratulations! You Passed
                        @else
                            <i class="bi bi-x-circle me-2"></i>Quiz Not Passed
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ number_format($attempt->percentage, 1) }}%</h3>
                            <p class="text-muted">Your Score</p>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ $attempt->score }}/{{ $questions->sum('points') }}</h3>
                            <p class="text-muted">Points</p>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ gmdate('H:i:s', $attempt->time_taken_seconds) }}</h3>
                            <p class="text-muted">Time Taken</p>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ $quiz->passing_marks }}</h3>
                            <p class="text-muted">Passing Marks</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($quiz->show_correct_answers)
                @foreach($questions as $index => $question)
                    @php
                        $studentAnswer = $attempt->answers[$question->id] ?? null;
                        $isCorrect = $question->checkAnswer($studentAnswer);
                    @endphp
                    <div class="card mb-3">
                        <div class="card-header bg-{{ $isCorrect ? 'success' : 'danger' }} bg-opacity-10">
                            <h6 class="mb-0">
                                Question {{ $index + 1 }}
                                @if($isCorrect)
                                    <span class="badge bg-success">Correct</span>
                                @else
                                    <span class="badge bg-danger">Incorrect</span>
                                @endif
                                <span class="badge bg-primary">{{ $question->points }} points</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-3"><strong>{{ $question->question }}</strong></p>

                            @if($question->question_type === 'multiple_choice')
                                @foreach($question->options as $key => $option)
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $option === $question->correct_answer ? 'success' : ($option === $studentAnswer ? 'danger' : 'secondary') }}">
                                            {{ $key + 1 }}
                                        </span>
                                        {{ $option }}
                                    </div>
                                @endforeach
                            @elseif($question->question_type === 'true_false')
                                <p><strong>Your Answer:</strong> {{ ucfirst($studentAnswer) }}</p>
                                <p><strong>Correct Answer:</strong> {{ ucfirst($question->correct_answer) }}</p>
                            @elseif($question->question_type === 'short_answer')
                                <p><strong>Your Answer:</strong> {{ $studentAnswer }}</p>
                                <p><strong>Expected Answer:</strong> {{ $question->correct_answer }}</p>
                            @endif

                            @if($question->explanation)
                                <div class="alert alert-info mt-3 mb-0">
                                    <strong>Explanation:</strong> {{ $question->explanation }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="text-center mb-4">
                <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-2"></i> Back to Quiz
                </a>
                <a href="{{ route('student.courses.show', $quiz->course) }}" class="btn btn-secondary">
                    <i class="bi bi-book me-2"></i> Back to Course
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quiz Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Quiz:</strong> {{ $quiz->title }}</p>
                    <p class="mb-2"><strong>Course:</strong> {{ $quiz->course->name }}</p>
                    <p class="mb-2"><strong>Attempt:</strong> #{{ $attempt->attempt_number }}</p>
                    <p class="mb-2"><strong>Submitted:</strong> {{ $attempt->submitted_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
