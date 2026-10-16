@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Edit Hall of Residence</h3>
            <p class="text-muted mb-0">Halls are stored with campus facilities using the Hall type.</p>
        </div>
        <a href="{{ route('admin.hostel.index') }}" class="btn btn-outline-secondary">Back to halls</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.hostel.update', $hostel) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hall Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" maxlength="255" required value="{{ old('name', $hostel->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hall Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            @foreach(['male' => 'Male Only', 'female' => 'Female Only', 'mixed' => 'Mixed'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $hostel->hall_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Total Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacity" min="1" required value="{{ old('capacity', $hostel->capacity) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Location</label>
                        <input type="text" class="form-control" name="location" maxlength="255" value="{{ old('location', $hostel->location) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $hostel->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="is_active" required>
                            <option value="1" @selected((string) old('is_active', (int) $hostel->is_active) === '1')>Active</option>
                            <option value="0" @selected((string) old('is_active', (int) $hostel->is_active) === '0')>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="{{ route('admin.hostel.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
