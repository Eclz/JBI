@extends('layouts.app')

@section('title', 'Human Resources Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Human Resources Dashboard</h2>
            <p class="text-muted mb-0">Overview of staff, leave requests, and onboarding.</p>
        </div>
        @if(auth()->user()->hasPermission('hr_core', 'create'))
            <a href="{{ route('human-resources.staff.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Add Staff
            </a>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('human-resources.staff.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Active Staff</div>
                        <div class="fs-2 fw-bold text-dark">{{ $staffCount }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('human-resources.leaves.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Pending Leave Requests</div>
                        <div class="fs-2 fw-bold text-dark">{{ $leaveRequests }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('human-resources.sections.show', 'onboarding') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">Onboarding Records</div>
                        <div class="fs-2 fw-bold text-dark">{{ $onboardingCount }}</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Staff</h5>
            <a href="{{ route('human-resources.staff.index') }}" class="btn btn-sm btn-outline-primary">View staff directory</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentStaff as $employee)
                        <tr>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->role_name }}</td>
                            <td>{{ $employee->hrProfile?->department ?? '—' }}</td>
                            <td>
                                <span class="badge text-bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('human-resources.staff.show', $employee) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No staff records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
