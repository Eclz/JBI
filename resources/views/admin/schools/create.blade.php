@extends('layouts.app')

@section('title', 'Add New Academic School')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-building-plus me-2"></i>Add New Academic School
            </h1>
            <p class="text-muted mb-0">Create a new university school entity</p>
        </div>
        <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Schools List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>School Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.schools.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">School Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. School of Technology" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="code" class="form-label fw-semibold">School Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. SOT" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Brief mission or overview of this academic school">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="dean_id" class="form-label fw-semibold">Assigned Dean / Head</label>
                                <select name="dean_id" id="dean_id" class="form-select">
                                    <option value="">-- Select Dean (Optional) --</option>
                                    @foreach($deans as $dean)
                                        <option value="{{ $dean->id }}" {{ old('dean_id') == $dean->id ? 'selected' : '' }}>
                                            {{ $dean->first_name }} {{ $dean->last_name }} ({{ $dean->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">Building / Campus Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" placeholder="e.g. Tech Hub, Main Campus">
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Contact Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="+256 414 000000">
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Official Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="school@jbiuniversity.ac.ug">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">Set Active Status immediately</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.schools.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="bi bi-save me-1"></i>Create School
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
