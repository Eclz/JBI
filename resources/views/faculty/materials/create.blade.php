@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="h3 mb-2" style="color: #1a202c; font-weight: 600;">Upload Course Material</h2>
        <p class="text-muted">{{ $course->code }} - {{ $course->name }}</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('faculty.courses.materials.store', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="title" class="form-label" style="font-weight: 600;">Material Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="type" class="form-label" style="font-weight: 600;">Material Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="lecture" {{ old('type') == 'lecture' ? 'selected' : '' }}>Lecture Notes</option>
                        <option value="reading" {{ old('type') == 'reading' ? 'selected' : '' }}>Reading Material</option>
                        <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label" style="font-weight: 600;">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="file" class="form-label" style="font-weight: 600;">Upload File</label>
                    <input type="file" class="form-control" id="file" name="file" required>
                    <small class="form-text text-muted">Maximum file size: 10MB</small>
                    @error('file')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-upload me-2"></i>Upload Material
                    </button>
                    <a href="{{ route('faculty.courses.show', $course) }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
