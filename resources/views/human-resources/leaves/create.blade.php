@extends('layouts.app')

@section('title', 'Apply for Leave')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Apply for Leave</h2>
            <p class="text-muted mb-0">Submit a new leave request.</p>
        </div>
        <a href="{{ route('human-resources.leaves.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" action="{{ route('human-resources.leaves.store') }}">
                @csrf
                @if(isset($isHrOrAdmin) && $isHrOrAdmin)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Staff Member</label>
                    <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror">
                        <option value="">Select staff member (or leave blank for yourself)</option>
                        @if(isset($staffMembers) && count($staffMembers) > 0)
                            @foreach($staffMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('staff_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-semibold">Leave Type</label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">Select type</option>
                        <option value="Annual">Annual Leave</option>
                        <option value="Sick">Sick Leave</option>
                        <option value="Maternity">Maternity Leave</option>
                        <option value="Paternity">Paternity Leave</option>
                        <option value="Unpaid">Unpaid Leave</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Substitute Lecturer</label>
                        <select name="substitute_id" class="form-select @error('substitute_id') is-invalid @enderror">
                            <option value="">None</option>
                            @if(isset($faculty) && count($faculty) > 0)
                                @foreach($faculty as $member)
                                    <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Optional: Select a colleague to cover your duties.</small>
                        @error('substitute_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Reason</label>
                    <textarea name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror" required></textarea>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
