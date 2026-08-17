@extends('layouts.app')

@section('title', 'Fill Evaluation Survey')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-star text-primary me-2"></i>EVALUATE LECTURER: {{ strtoupper($course->code) }}
            </h5>
            <p class="text-muted small mb-0">{{ $course->title }} | Lecturer: {{ $course->faculty?->full_name ?? 'Assigned Lecturer' }}</p>
        </div>
        <a href="{{ route('student.evaluation-surveys.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Surveys List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2">
                    <h6 class="fw-bold mb-0 text-primary">{{ strtoupper($survey->title) }}</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student.evaluation-surveys.store', [$survey, $course]) }}" method="POST">
                        @csrf

                        @foreach($survey->questions as $index => $q)
                            <div class="mb-4 p-3 bg-light rounded border">
                                <label class="form-label fw-bold d-block mb-2">
                                    {{ $index + 1 }}. {{ $q->question_text }}
                                </label>
                                <span class="badge bg-secondary mb-3">{{ $q->category }}</span>

                                @if($q->question_type === 'rating')
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        @for($star = 1; $star <= 5; $star++)
                                            @php
                                                $savedRating = $existingResponse?->answers[$q->id] ?? 5;
                                            @endphp
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="ratings[{{ $q->id }}]" id="star_{{ $q->id }}_{{ $star }}" value="{{ $star }}" {{ $savedRating == $star ? 'checked' : '' }} required>
                                                <label class="form-check-label small" for="star_{{ $q->id }}_{{ $star }}">
                                                    {{ $star }} - {{ match($star) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent' } }}
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div class="mb-4">
                            <label class="form-label fw-bold">Additional Comments / Constructive Feedback</label>
                            <textarea name="comments" class="form-control" rows="3" placeholder="Optional comments regarding teaching methods, course content, or suggestions...">{{ $existingResponse?->comments }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="bi bi-send me-1"></i>SUBMIT EVALUATION
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
