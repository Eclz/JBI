@extends('layouts.app')

@section('title', 'System User Logs')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">System User Logs</h2>
            <p class="text-muted mb-0">System report for user logins and logouts.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Authentication Logs</h5>
            <span class="badge bg-primary text-white">{{ $logs->total() }} Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Timestamp (Local)</th>
                            <th>IP Address</th>
                            <th>Browser / Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $log->user->name ?? 'Unknown User' }}</div>
                                    <small class="text-muted">{{ $log->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($log->action == 'login')
                                        <span class="badge text-bg-success">Login</span>
                                    @else
                                        <span class="badge text-bg-warning">Logout</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $log->created_at->format('h:i:s A') }}</small>
                                </td>
                                <td>{{ $log->ip_address ?? '—' }}</td>
                                <td>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent ?? '—' }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-lock display-4 d-block mb-3"></i>
                                    No user logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-transparent py-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
