@extends('layouts.app')

@section('title', 'Taking Quiz - ' . $quiz->title)

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Persistent Timer Card --}}
    <div id="timer-card" style="position: fixed; top: 80px; left: 300px; right: 24px; z-index: 2000;">
        <div class="card shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-semibold"><i class="bi bi-clock me-2"></i>{{ $quiz->title }}</div>
                    <div class="fs-5 fw-bold" id="timer">{{ sprintf('%02d:%02d:00', $quiz->duration / 60, $quiz->duration % 60) }}</div>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div id="timerProgress" class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 90px;"></div>
    <form action="{{ route('student.quizzes.submit', [$quiz, $attempt]) }}" method="POST" id="quizForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                @foreach($questions as $index => $question)
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Question {{ $index + 1 }} <span class="badge bg-primary">{{ $question->points }} points</span></h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">{{ $question->question }}</p>

                            @if($question->question_type === 'multiple_choice')
                                @foreach($question->options as $optionKey => $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_{{ $optionKey }}" value="{{ $option }}" required>
                                        <label class="form-check-label" for="q{{ $question->id }}_{{ $optionKey }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif($question->question_type === 'true_false')
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_true" value="true" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_true">True</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_false" value="false" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_false">False</label>
                                </div>
                            @elseif($question->question_type === 'short_answer')
                                <textarea class="form-control" name="answers[{{ $question->id }}]" rows="3" required></textarea>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i> Submit Quiz
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card" style="position: sticky; top: 200px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Quiz Progress</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Total Questions:</strong> {{ $questions->count() }}</p>
                        <p class="mb-2"><strong>Total Points:</strong> {{ $questions->sum('points') }}</p>
                        <p class="mb-2"><strong>Time Remaining:</strong> <span id="time-remaining"></span></p>

                        <hr>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Make sure to submit before time runs out!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Timer functionality
    const duration = {{ $quiz->duration_minutes * 60 }}; // Convert to seconds
    const startedAt = {{ $attempt->started_at ? $attempt->started_at->timestamp : 'null' }};
    const serverNow = {{ now()->timestamp }};
    const fallbackRemaining = duration;
    const endTimeEpoch = startedAt
        ? startedAt + duration
        : Math.floor(Date.now() / 1000) + fallbackRemaining;
    let timeLeft = Math.max(endTimeEpoch - serverNow, 0);
    let isDirty = false;
    let isSubmitting = false;

    function updateTimer() {
        timeLeft = Math.max(endTimeEpoch - Math.floor(Date.now() / 1000), 0);
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;

        const timerElement = document.getElementById('timer');
        const timeRemainingElement = document.getElementById('time-remaining');
        const timerCard = document.getElementById('timer-card');
        const timerProgress = document.getElementById('timerProgress');

        const timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        timerElement.textContent = timeString;
        timeRemainingElement.textContent = timeString;
        const progressPercent = Math.max(Math.min((timeLeft / duration) * 100, 100), 0);
        timerProgress.style.width = progressPercent + '%';

        // Change color based on time left
        if (timeLeft <= 300) { // 5 minutes
            timerCard.className = 'alert alert-danger';
        } else if (timeLeft <= 600) { // 10 minutes
            timerCard.className = 'alert alert-warning';
        }

        if (timeLeft <= 0) {
            isSubmitting = true;
            window.onbeforeunload = null;
            document.getElementById('quizForm').submit();
        }
    }

    // Update timer every second
    setInterval(updateTimer, 1000);
    updateTimer(); // Initial call

    // Auto-save functionality
    setInterval(function() {
        localStorage.setItem('quiz_{{ $quiz->id }}_{{ $attempt->id }}', JSON.stringify(getFormData()));
        isDirty = false;
    }, 60000); // Save every minute

    function getFormData() {
        const form = document.getElementById('quizForm');
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        return data;
    }

    // Prevent accidental navigation
    window.onbeforeunload = function() {
        if (isSubmitting || !isDirty) {
            return null;
        }
        return "Are you sure you want to leave? Your quiz progress will be lost.";
    };

    // Remove warning on form submit
    document.getElementById('quizForm').addEventListener('submit', function() {
        isSubmitting = true;
        window.onbeforeunload = null;
    });

    document.getElementById('quizForm').addEventListener('input', function() {
        isDirty = true;
    });
</script>
@endsection
