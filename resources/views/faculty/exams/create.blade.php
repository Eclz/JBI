@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Create New Exam</h1>
        <p class="text-muted mb-0">Set up a new examination for your course</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('faculty.exams.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="title" class="form-label fw-semibold">Exam Title *</label>
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
                                <label for="exam_type" class="form-label fw-semibold">Exam Type *</label>
                                <select name="exam_type" id="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="midterm" {{ old('exam_type') == 'midterm' ? 'selected' : '' }}>Midterm</option>
                                    <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>Final</option>
                                    <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="assignment" {{ old('exam_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                </select>
                                @error('exam_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="exam_mode" class="form-label fw-semibold">Exam Mode *</label>
                                <select name="exam_mode" id="exam_mode" class="form-select @error('exam_mode') is-invalid @enderror" required>
                                    <option value="">Select Mode</option>
                                    <option value="online" {{ old('exam_mode') == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="offline" {{ old('exam_mode') == 'offline' ? 'selected' : '' }}>Offline</option>
                                    <option value="hybrid" {{ old('exam_mode') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                                @error('exam_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="exam_date" class="form-label fw-semibold">Exam Date *</label>
                                <input type="date" name="exam_date" id="exam_date" class="form-control @error('exam_date') is-invalid @enderror" value="{{ old('exam_date') }}" required>
                                @error('exam_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="start_time" class="form-label fw-semibold">Start Time *</label>
                                <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_time" class="form-label fw-semibold">End Time *</label>
                                <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (minutes) *</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes') }}" required min="1">
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="total_marks" class="form-label fw-semibold">Total Marks *</label>
                                <input type="number" name="total_marks" id="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks') }}" required min="0" step="0.01">
                                @error('total_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="passing_marks" class="form-label fw-semibold">Passing Marks *</label>
                                <input type="number" name="passing_marks" id="passing_marks" class="form-control @error('passing_marks') is-invalid @enderror" value="{{ old('passing_marks') }}" required min="0" step="0.01">
                                @error('passing_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="room_number" class="form-label fw-semibold">Room Number</label>
                            <input type="text" name="room_number" id="room_number" class="form-control @error('room_number') is-invalid @enderror" value="{{ old('room_number') }}">
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="instructions" class="form-label fw-semibold">Instructions</label>
                            <textarea name="instructions" id="instructions" rows="4" class="form-control @error('instructions') is-invalid @enderror">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="require_payment" id="require_payment" value="1" {{ old('require_payment') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="require_payment">
                                    Require Payment
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_published">
                                    Publish immediately
                                </label>
                            </div>
                        </div>

                        <div class="mb-4" id="payment_amount_field" style="display: none;">
                            <label for="payment_amount" class="form-label fw-semibold">Payment Amount</label>
                            <input type="number" name="payment_amount" id="payment_amount" class="form-control @error('payment_amount') is-invalid @enderror" value="{{ old('payment_amount') }}" min="0" step="0.01">
                            @error('payment_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="exam_paper" class="form-label fw-semibold">Question Paper (PDF/DOC)</label>
                            <input type="file" name="exam_paper" id="exam_paper" class="form-control @error('exam_paper') is-invalid @enderror" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Maximum size: 10MB</small>
                            @error('exam_paper')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="answer_booklet" class="form-label fw-semibold">Answer Booklet (PDF/DOC)</label>
                            <input type="file" name="answer_booklet" id="answer_booklet" class="form-control @error('answer_booklet') is-invalid @enderror" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Maximum size: 10MB</small>
                            @error('answer_booklet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('faculty.exams.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Exam Setup Tips</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Set clear exam dates and times</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Upload question papers for offline access</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Enable payment if required for exam participation</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Provide detailed instructions for students</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Choose appropriate exam mode (online/offline/hybrid)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('require_payment').addEventListener('change', function() {
    document.getElementById('payment_amount_field').style.display = this.checked ? 'block' : 'none';
});

// Show payment field if already checked (on validation error)
if (document.getElementById('require_payment').checked) {
    document.getElementById('payment_amount_field').style.display = 'block';
}
</script>
@endsection
