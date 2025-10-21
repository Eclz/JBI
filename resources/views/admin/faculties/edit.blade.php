@extends('layouts.app')

@section('title', 'Edit Faculty')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Faculty</h1>
                    <p class="text-muted">Update faculty information for {{ $faculty->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.faculties.show', $faculty) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> View Faculty
                    </a>
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Faculties
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Faculty Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.faculties.update', $faculty) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Faculty Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name', $faculty->name) }}"
                                                   placeholder="e.g., Faculty of Social Sciences" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Faculty Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                   id="code" name="code" value="{{ old('code', $faculty->code) }}"
                                                   placeholder="e.g., FSS" maxlength="10" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Short code for the faculty (max 10 characters)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Brief description of the faculty">{{ old('description', $faculty->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Dean Assignment -->
                                <div class="mb-3">
                                    <label for="dean_id" class="form-label">Dean</label>
                                    <select class="form-select @error('dean_id') is-invalid @enderror" id="dean_id" name="dean_id">
                                        <option value="">Select Dean (Optional)</option>
                                        @foreach($potentialDeans as $dean)
                                            <option value="{{ $dean->id }}"
                                                {{ old('dean_id', $faculty->dean_id) == $dean->id ? 'selected' : '' }}>
                                                {{ $dean->name }} - {{ $dean->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dean_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Select a faculty member to serve as dean</div>
                                </div>

                                <!-- Contact Information -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $faculty->email) }}"
                                                   placeholder="faculty@university.edu">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" value="{{ old('phone', $faculty->phone) }}"
                                                   placeholder="+1 (555) 123-4567">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                                   id="location" name="location" value="{{ old('location', $faculty->location) }}"
                                                   placeholder="Building name or address">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                   id="website" name="website" value="{{ old('website', $faculty->website) }}"
                                                   placeholder="https://faculty.university.edu">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                               {{ old('is_active', $faculty->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Faculty
                                        </label>
                                    </div>
                                    <div class="form-text">Inactive faculties will not be visible to students</div>
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Update Faculty
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase the code field
    const codeField = document.getElementById('code');
    codeField.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endpush
