@extends('layouts.app')

@section('title', 'Edit Leave Request')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Leave Request</h2>
            <p class="text-muted mb-0">Modify your pending leave request.</p>
        </div>
        <a href="{{ route('human-resources.leaves.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" action="{{ route('human-resources.leaves.update', $leave) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Leave Type</label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">Select type</option>
                        <option value="Annual" {{ $leave->type === 'Annual' ? 'selected' : '' }}>Annual Leave</option>
                        <option value="Sick" {{ $leave->type === 'Sick' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="Maternity" {{ $leave->type === 'Maternity' ? 'selected' : '' }}>Maternity Leave</option>
                        <option value="Paternity" {{ $leave->type === 'Paternity' ? 'selected' : '' }}>Paternity Leave</option>
                        <option value="Unpaid" {{ $leave->type === 'Unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                        <option value="Other" {{ $leave->type === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ $leave->start_date->format('Y-m-d') }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ $leave->end_date->format('Y-m-d') }}" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Reason</label>
                    <textarea name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror" required>{{ $leave->reason }}</textarea>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">Update Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
