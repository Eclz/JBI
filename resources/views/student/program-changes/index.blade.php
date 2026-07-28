@extends('layouts.app')

@section('title', 'Program Change Requests')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-arrow-repeat me-2"></i>Program Change Requests
            </h1>
            <p class="text-muted mb-0">Track your program change requests</p>
        </div>
        <a href="{{ route('student.program-changes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Current Program</th>
                                <th>Requested Program</th>
                                <th>Status</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->currentProgram->name ?? 'N/A' }}</td>
                                    <td>{{ $request->requestedProgram->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-arrow-repeat display-1 text-muted"></i>
                    <h5 class="mt-3">No requests yet</h5>
                    <p class="text-muted">Submit a program change request to get started.</p>
                </div>
            @endif
        </div>
        @if($requests->hasPages())
            <div class="card-footer">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
