@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Leave Requests</h2>
            <p class="text-muted mb-0">Track staff leave, approvals, and balances.</p>
        </div>
        <a href="{{ route('human-resources.leaves.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New request</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Substitute</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            <tr>
                                <td>{{ $leave->user->full_name }}</td>
                                <td>{{ $leave->type }}</td>
                                <td>{{ $leave->start_date->format('M d, Y') }}</td>
                                <td>{{ $leave->end_date->format('M d, Y') }}</td>
                                <td>{{ $leave->substitute_id ? $leave->substitute->full_name : '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($leave->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'cancelled' => 'secondary',
                                            default => 'warning'
                                        };
                                    @endphp
                                    <span class="badge text-bg-{{ $badgeClass }}">{{ ucfirst($leave->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('human-resources.leaves.show', $leave) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i> View</a>
                                    @if(auth()->id() === $leave->user_id && $leave->status === 'pending')
                                        <a href="{{ route('human-resources.leaves.edit', $leave) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('human-resources.leaves.destroy', $leave) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this leave request?')">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No leave requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
