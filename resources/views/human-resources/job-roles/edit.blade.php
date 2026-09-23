@extends('layouts.app')

@section('title', 'Edit Job Role')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Job Role</h2>
            <p class="text-muted mb-0">Update position details and salary bands.</p>
        </div>
        <a href="{{ route('human-resources.job-roles.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('human-resources.job-roles.update', $jobRole) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $jobRole->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $jobRole->department) }}">
                        @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Minimum Salary Band</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="salary_band_min" class="form-control @error('salary_band_min') is-invalid @enderror" value="{{ old('salary_band_min', optional($jobRole->salary_band_min)) }}">
                        </div>
                        @error('salary_band_min') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Maximum Salary Band</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="salary_band_max" class="form-control @error('salary_band_max') is-invalid @enderror" value="{{ old('salary_band_max', optional($jobRole->salary_band_max)) }}">
                        </div>
                        @error('salary_band_max') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $jobRole->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $jobRole->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Role is Active</label>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
