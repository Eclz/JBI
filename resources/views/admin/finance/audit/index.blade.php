@extends('layouts.app')

@section('title', 'Financial Audit Trail & Compliance')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-shield-check me-2"></i>Financial Audit Trail & Compliance
            </h1>
            <p class="text-muted mb-0">Immutable audit log of all financial transactions, revenue creation, expense approvals & user actions</p>
        </div>
        <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary">Back to Finance Hub</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Timestamp</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th class="text-end pe-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-3 small text-muted fw-bold">{{ $log->created_at->format('M d, Y h:i:s A') }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $log->user->full_name ?? 'System' }}</div>
                                </td>
                                <td><span class="badge bg-primary px-2.5 py-1.5">{{ $log->module }}</span></td>
                                <td><span class="badge bg-secondary px-2.5 py-1.5">{{ $log->action }}</span></td>
                                <td class="small">{{ $log->details }}</td>
                                <td class="text-end pe-3 font-monospace small text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No audit trail records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
