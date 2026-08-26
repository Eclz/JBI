@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Edit Exam</h1>
        <p class="text-muted mb-0">Update examination details for {{ $exam->title }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('faculty.exams.update', $exam) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="course_id" class="form-label fw-semibold">Course *</label>
                            <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (old('course_id', $exam->course_id) == $course->id) ? 'selected' : '' }}>
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
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $exam->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="exam_type" class="form-label fw-semibold">Exam Type *</label>
                                <select name="exam_type" id="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    @foreach($examTypes as $type)
                                        <option value="{{ strtolower($type) }}" {{ old('exam_type', $exam->exam_type) == strtolower($type) ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('exam_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="exam_mode" class="form-label fw-semibold">Exam Mode *</label>
                                <select name="exam_mode" id="exam_mode" class="form-select @error('exam_mode') is-invalid @enderror" required>
                                    <option value="">Select Mode</option>
                                    <option value="online" {{ old('exam_mode', $exam->exam_mode) == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="offline" {{ old('exam_mode', $exam->exam_mode) == 'offline' ? 'selected' : '' }}>Offline</option>
                                    <option value="hybrid" {{ old('exam_mode', $exam->exam_mode) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                </select>
                                @error('exam_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-semibold">Start Date & Time *</label>
                                <input type="datetime-local" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-semibold">End Date & Time *</label>
                                <input type="datetime-local" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (minutes) *</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="1">
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="total_marks" class="form-label fw-semibold">Total Marks *</label>
                                <input type="number" name="total_marks" id="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', $exam->total_marks) }}" required min="0" step="0.01">
                                @error('total_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="passing_marks" class="form-label fw-semibold">Passing Marks *</label>
                                <input type="number" name="passing_marks" id="passing_marks" class="form-control @error('passing_marks') is-invalid @enderror" value="{{ old('passing_marks', $exam->passing_marks) }}" required min="0" step="0.01">
                                @error('passing_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="room_number" class="form-label fw-semibold">Room Number</label>
                            <input type="text" name="room_number" id="room_number" class="form-control @error('room_number') is-invalid @enderror" value="{{ old('room_number', $exam->room_number) }}">
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="instructions" class="form-label fw-semibold">Instructions</label>
                            <textarea name="instructions" id="instructions" rows="4" class="form-control @error('instructions') is-invalid @enderror">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="require_payment" id="require_payment" value="1" {{ old('require_payment', $exam->require_payment) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="require_payment">
                                    Require Payment
                                </label>
                            </div>
                        </div>

                        <div class="mb-4" id="payment_amount_field" style="display: {{ old('require_payment', $exam->require_payment) ? 'block' : 'none' }};">
                            <label for="payment_amount" class="form-label fw-semibold">Payment Amount</label>
                            <input type="number" name="payment_amount" id="payment_amount" class="form-control @error('payment_amount') is-invalid @enderror" value="{{ old('payment_amount', $exam->payment_amount) }}" min="0" step="0.01">
                            @error('payment_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($exam->exam_paper_url)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Question Paper</label>
                                <div>
                                    <a href="{{ Storage::url($exam->exam_paper_url) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf"></i> View Current Paper
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="exam_paper" class="form-label fw-semibold">Question Paper (PDF/DOC)</label>
                            <input type="file" name="exam_paper" id="exam_paper" class="form-control @error('exam_paper') is-invalid @enderror" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Maximum size: 10MB. Leave empty to keep existing file.</small>
                            @error('exam_paper')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($exam->answer_booklet_url)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Answer Booklet</label>
                                <div>
                                    <a href="{{ Storage::url($exam->answer_booklet_url) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf"></i> View Current Booklet
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="answer_booklet" class="form-label fw-semibold">Answer Booklet (PDF/DOC)</label>
                            <input type="file" name="answer_booklet" id="answer_booklet" class="form-control @error('answer_booklet') is-invalid @enderror" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Maximum size: 10MB. Leave empty to keep existing file.</small>
                            @error('answer_booklet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('faculty.exams.show', $exam) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Current Status</h5>
                    <div class="mb-2">
                        <strong>Created:</strong> {{ $exam->created_at->format('M d, Y') }}
                    </div>
                    <div class="mb-2">
                        <strong>Last Updated:</strong> {{ $exam->updated_at->format('M d, Y') }}
                    </div>
                    <div class="mb-2">
                        <strong>Total Attempts:</strong> {{ $exam->attempts->count() }}
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-3">Edit Tips</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Update dates carefully if students have already registered</li>
                        <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Upload new files to replace existing ones</li>
                        <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Leave file fields empty to keep current uploads</li>
                        <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Changing payment requirements affects access</li>
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
</script>
@endsection
