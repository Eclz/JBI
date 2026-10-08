@extends('layouts.app')

@section('title', 'Draft Quality Review')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Draft New Quality Review</h5>
            <a href="{{ route('faculty.dean.quality.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.quality.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Review Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                    <select class="form-select @error('program_id') is-invalid @enderror" id="program_id" name="program_id" required>
                        <option value="">Select a Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="review_notes" class="form-label">Review Notes <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('review_notes') is-invalid @enderror" id="review_notes" name="review_notes" rows="6" required>{{ old('review_notes') }}</textarea>
                    @error('review_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Save Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
