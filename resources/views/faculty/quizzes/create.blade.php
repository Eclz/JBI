@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Create New Quiz</h1>
        <p class="text-muted mb-0">Set up a new quiz for your course</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('faculty.quizzes.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="course_id" class="form-label fw-semibold">Course *</label>
                            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->code }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">Quiz Title *</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (Minutes) *</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', 30) }}" required min="1">
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="max_attempts" class="form-label fw-semibold">Max Attempts *</label>
                                <input type="number" name="max_attempts" id="max_attempts" class="form-control @error('max_attempts') is-invalid @enderror" value="{{ old('max_attempts', 1) }}" required min="1">
                                @error('max_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="total_marks" class="form-label fw-semibold">Total Marks *</label>
                                <input type="number" name="total_marks" id="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', 100) }}" required min="0" step="0.01">
                                @error('total_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="passing_marks" class="form-label fw-semibold">Passing Marks *</label>
                                <input type="number" name="passing_marks" id="passing_marks" class="form-control @error('passing_marks') is-invalid @enderror" value="{{ old('passing_marks', 50) }}" required min="0" step="0.01">
                                @error('passing_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-semibold">Start Time *</label>
                                <input type="datetime-local" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-semibold">End Time *</label>
                                <input type="datetime-local" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="show_results" class="form-label fw-semibold">Show Results *</label>
                            <select name="show_results" id="show_results" class="form-select @error('show_results') is-invalid @enderror" required>
                                <option value="immediately" {{ old('show_results') == 'immediately' ? 'selected' : '' }}>Immediately After Submission</option>
                                <option value="after_deadline" {{ old('show_results') == 'after_deadline' ? 'selected' : '' }}>After Deadline</option>
                                <option value="manual" {{ old('show_results') == 'manual' ? 'selected' : '' }}>Manual Release</option>
                            </select>
                            @error('show_results')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="shuffle_questions" id="shuffle_questions" class="form-check-input" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                <label for="shuffle_questions" class="form-check-label">Shuffle Questions</label>
                            </div>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="shuffle_options" id="shuffle_options" class="form-check-input" value="1" {{ old('shuffle_options') ? 'checked' : '' }}>
                                <label for="shuffle_options" class="form-check-label">Shuffle Answer Options</label>
                            </div>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="is_published" id="is_published" class="form-check-input" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                                <label for="is_published" class="form-check-label">Publish immediately</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-2"></i>Create Quiz
                            </button>
                            <a href="{{ route('faculty.quizzes.index') }}" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-3">Quiz Guidelines</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Set appropriate time duration</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Define clear passing criteria</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Add questions after creating quiz</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Review before publishing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
