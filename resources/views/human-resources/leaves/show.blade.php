@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Leave Request Details</h2>
            <p class="text-muted mb-0">View leave request and approval status.</p>
        </div>
        <a href="{{ route('human-resources.leaves.index') }}" class="btn btn-outline-secondary">Back to Requests</a>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-bold">Request Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Employee</div>
                        <div class="col-sm-8">{{ $leave->user->full_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Leave Type</div>
                        <div class="col-sm-8">{{ $leave->type }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Duration</div>
                        <div class="col-sm-8">{{ $leave->start_date->format('M d, Y') }} to {{ $leave->end_date->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Total Days</div>
                        <div class="col-sm-8">{{ $leave->start_date->diffInDays($leave->end_date) + 1 }} Days</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Reason</div>
                        <div class="col-sm-8">{{ $leave->reason }}</div>
                    </div>
                    @if($leave->substitute_id)
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Substitute Lecturer</div>
                        <div class="col-sm-8">
                            <span class="badge bg-info text-dark">{{ $leave->substitute->full_name }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-semibold">Submitted On</div>
                        <div class="col-sm-8">{{ $leave->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-bold">Approval Status</h5>
                </div>
                <div class="card-body">
                    @php
                        $badgeClass = match($leave->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'cancelled' => 'secondary',
                            default => 'warning'
                        };
                    @endphp
                    <div class="mb-4">
                        <h6 class="text-muted fw-semibold mb-2">Current Status</h6>
                        <span class="badge text-bg-{{ $badgeClass }} px-3 py-2 fs-6">{{ ucfirst($leave->status) }}</span>
                    </div>

                    @if($leave->status !== 'pending' && $leave->status !== 'cancelled')
                        <div class="mb-3">
                            <h6 class="text-muted fw-semibold mb-1">Reviewed By</h6>
                            <p class="mb-0">{{ $leave->manager ? $leave->manager->full_name : 'System/Admin' }}</p>
                        </div>
                        @if($leave->manager_comment)
                        <div class="mb-0">
                            <h6 class="text-muted fw-semibold mb-1">Manager Comment</h6>
                            <p class="mb-0 bg-light p-2 rounded">{{ $leave->manager_comment }}</p>
                        </div>
                        @endif
                    @endif

                    @if($leave->status === 'pending' && auth()->user()->hasPermission('human_resources', 'view'))
                        <hr>
                        <h6 class="fw-semibold mb-3">Take Action</h6>
                        <form method="POST" action="{{ route('human-resources.leaves.status', $leave) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label text-muted small">Manager Comment (Optional)</label>
                                <textarea name="manager_comment" rows="2" class="form-control"></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="status" value="approved" class="btn btn-success" onclick="return confirm('Approve this leave request?')">Approve Request</button>
                                <button type="submit" name="status" value="rejected" class="btn btn-outline-danger" onclick="return confirm('Reject this leave request?')">Reject Request</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
