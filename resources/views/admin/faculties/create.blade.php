@extends('layouts.app')

@section('title', 'Create New Faculty')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-primary fw-bold"><i class="bi bi-buildings me-2"></i>Create New Faculty</h1>
                    <p class="text-muted mb-0">Add a new academic faculty to group departments</p>
                </div>
                <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Faculties
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('admin.faculties.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Faculty Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Faculty of Technology" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="code" class="form-label fw-semibold">Faculty Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. FOT" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="dean_id" class="form-label fw-semibold">Dean of Faculty</label>
                                <select name="dean_id" id="dean_id" class="form-select">
                                    <option value="">-- Select Dean --</option>
                                    @foreach($deans as $dean)
                                        <option value="{{ $dean->id }}" {{ old('dean_id') == $dean->id ? 'selected' : '' }}>
                                            {{ $dean->full_name }} ({{ $dean->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">Building / Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" placeholder="e.g. Technology Building, Main Campus">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Faculty Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="technology@jbiuniversity.ac.ug">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Faculty Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="+256 414 100 105">
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Faculty Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Overview of programs and departments..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">Faculty Active Status</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.faculties.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="bi bi-save me-1"></i>Save Faculty
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
