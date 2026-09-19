@extends('layouts.app')

@section('title', 'Employee Self Service')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Employee Self Service (ESS)</h2>
            <p class="text-muted mb-0">Manage your employment profile, leaves, and time tracking.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Links -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i>My Profile</h5>
                    <p class="text-muted small mb-4">View and update your personal information and contact details.</p>
                    <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-primary w-100 mb-2">View Profile Directory</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-calendar-event text-success me-2"></i>Leave Requests</h5>
                    <p class="text-muted small mb-4">Apply for annual, sick, or personal leave, and view balances.</p>
                    <a href="{{ route('human-resources.leaves.index') }}" class="btn btn-outline-success w-100 mb-2">My Leaves</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i>Time & Attendance</h5>
                    <p class="text-muted small mb-4">Log your working hours and view timesheets.</p>
                    <a href="{{ route('human-resources.sections.show', 'time-tracking') }}" class="btn btn-outline-info w-100 mb-2">Timesheets</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
