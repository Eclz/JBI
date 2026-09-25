@extends('layouts.app')

@section('title', 'Staff Member Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Staff Profile: {{ $employee->full_name }}</h2>
            <p class="text-muted mb-0">View employee details and HR records.</p>
        </div>
        <div>
            <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary">Back to Staff List</a>
            @if(auth()->user()->hasPermission('hr_core', 'edit'))
                <a href="{{ route('human-resources.staff.edit', $employee) }}" class="btn btn-primary">Edit Profile</a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-circle text-secondary" style="font-size: 5rem;"></i>
                    </div>
                    <h4>{{ $employee->full_name }}</h4>
                    <p class="text-muted mb-1">{{ $employee->email }}</p>
                    <p class="text-muted mb-3">{{ $employee->phone ?? 'No phone provided' }}</p>
                    
                    @php
                        $status = $employee->hrProfile?->status ?? ($employee->is_active ? 'Active' : 'Inactive');
                        $badgeClass = $status === 'Active' ? 'success' : ($status === 'On Leave' ? 'info' : 'warning');
                    @endphp
                    <span class="badge text-bg-{{ $badgeClass }} px-3 py-2 fs-6">{{ $status }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0 fw-bold">Employment Information</h5>
                </div>
                <div class="card-body">
                    @if($employee->hrProfile)
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Employee Number</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->employee_number }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Job Title</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->job_title }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Department</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->department ?? '—' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Employment Type</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->employment_type }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Salary Band</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->salary_band ?? '—' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Emergency Contact</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->emergency_contact ?? '—' }}</div>
                        </div>
                        @if($employee->hrProfile->notes)
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-semibold">Notes</div>
                            <div class="col-sm-8">{{ $employee->hrProfile->notes }}</div>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> No HR profile found for this employee.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
