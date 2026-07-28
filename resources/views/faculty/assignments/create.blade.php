@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Create New Assignment</h1>
        <p class="text-muted mb-0">Add a new assignment for your course</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('faculty.assignments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="course_id" class="form-label fw-semibold">Course</label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
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

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Assignment Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="instructions" class="form-label fw-semibold">Instructions (Optional)</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label fw-semibold">Due Date</label>
                                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_score" class="form-label fw-semibold">Maximum Score</label>
                                <input type="number" class="form-control @error('max_score') is-invalid @enderror" id="max_score" name="max_score" value="{{ old('max_score') }}" min="0" step="0.01" required>
                                @error('max_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="submission_type" class="form-label fw-semibold">Submission Type</label>
                            <select class="form-select @error('submission_type') is-invalid @enderror" id="submission_type" name="submission_type" required>
                                <option value="file" {{ old('submission_type') == 'file' ? 'selected' : '' }}>File Upload</option>
                                <option value="text" {{ old('submission_type') == 'text' ? 'selected' : '' }}>Text Only</option>
                                <option value="both" {{ old('submission_type') == 'both' ? 'selected' : '' }}>Both File and Text</option>
                            </select>
                            @error('submission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">Attachment (Optional)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                            <small class="text-muted">Upload assignment instructions, resources, or templates (Max: 10MB)</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Assignment
                            </button>
                            <a href="{{ route('faculty.assignments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Tips for Creating Assignments</h6>
                    <ul class="text-muted small mb-0">
                        <li>Provide clear and detailed instructions</li>
                        <li>Set realistic due dates for students</li>
                        <li>Attach relevant materials and templates</li>
                        <li>Choose appropriate submission types</li>
                        <li>Define clear grading criteria</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
