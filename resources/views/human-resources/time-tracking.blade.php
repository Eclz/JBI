@extends('layouts.app')

@section('title', 'Time Tracking & Attendance')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Time & Attendance</h2>
            <p class="text-muted mb-0">Track staff working hours, shifts, and timesheets.</p>
        </div>
        <button class="btn btn-primary"><i class="bi bi-clock me-2"></i>Log Hours</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}</td>
                            <td>
                                <div class="fw-bold">{{ $log->user->name }}</div>
                                <small class="text-muted">{{ $log->user->hrProfile->department ?? 'General' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($log->clock_in)->format('h:i A') }}</td>
                            <td>{{ $log->clock_out ? \Carbon\Carbon::parse($log->clock_out)->format('h:i A') : '—' }}</td>
                            <td>{{ $log->total_hours ? $log->total_hours . ' hrs' : '—' }}</td>
                            <td>
                                @if($log->clock_out)
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Active Shift</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No time logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
