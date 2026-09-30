@extends('layouts.app')

@section('title', 'Human Resources Dashboard')

@section('content')
<style>
    .hr-gradient-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        position: relative;
        overflow: hidden;
    }
    .hr-gradient-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .hr-gradient-banner::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .hover-elevate {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-elevate:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .module-card {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.05);
        background: #ffffff;
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: inherit;
    }
    .module-card:hover {
        border-color: rgba(79, 70, 229, 0.3);
        background: linear-gradient(145deg, #ffffff, #f8faff);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.1);
        transform: translateY(-3px);
    }
    .module-card .icon-wrapper {
        transition: transform 0.3s ease;
    }
    .module-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Welcome Banner -->
    <div class="card hr-gradient-banner border-0 rounded-4 shadow-sm mb-4 text-white hover-elevate">
        <div class="card-body p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 mb-3 shadow-sm border border-white border-opacity-25" style="backdrop-filter: blur(10px);">
                    <i class="bi bi-clock me-1"></i> Dashboard Overview
                </span>
                <h2 class="fw-bold mb-2 display-6">Welcome to HR Workspace, {{ auth()->user()->first_name ?? 'Manager' }}!</h2>
                <p class="mb-0 fs-5 opacity-75 fw-light">Manage your organization's talent, operations, and lifecycle seamlessly.</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <a href="{{ route('human-resources.staff.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-person-lines-fill me-2"></i>Staff Directory
                </a>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 hover-elevate">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <p class="text-muted fw-semibold text-uppercase mb-1 tracking-wider" style="font-size: 0.8rem; letter-spacing: 1px;">Total Active Staff</p>
                            <h2 class="fw-bolder mb-0 display-5 text-dark">{{ $staffCount }}</h2>
                        </div>
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary fs-3 shadow-sm">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 border border-success-subtle">
                            <i class="bi bi-arrow-up-short"></i> Active
                        </span>
                        <span class="text-muted small ms-2">Registered employees</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 hover-elevate">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <p class="text-muted fw-semibold text-uppercase mb-1 tracking-wider" style="font-size: 0.8rem; letter-spacing: 1px;">Leave Requests</p>
                            <h2 class="fw-bolder mb-0 display-5 text-dark">{{ $leaveRequests }}</h2>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning fs-3 shadow-sm">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        @if($leaveRequests > 0)
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 border border-warning-subtle">
                                Action Required
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 border border-success-subtle">
                                All Caught Up
                            </span>
                        @endif
                        <span class="text-muted small ms-2">Pending approvals</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card border-0 rounded-4 shadow-sm h-100 hover-elevate">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <p class="text-muted fw-semibold text-uppercase mb-1 tracking-wider" style="font-size: 0.8rem; letter-spacing: 1px;">Onboarding Queue</p>
                            <h2 class="fw-bolder mb-0 display-5 text-dark">{{ $onboardingCount }}</h2>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 text-info fs-3 shadow-sm">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted small">New hires in progress</span>
                        <a href="{{ route('human-resources.sections.show', 'onboarding') }}" class="btn btn-sm btn-link ms-auto text-decoration-none">View Pipeline <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- HR Modules Grid -->
        <div class="col-xl-8">
            <h5 class="fw-bold mb-3 d-flex align-items-center">
                <i class="bi bi-grid-1x2-fill text-primary me-2"></i> HR Modules
            </h5>
            <div class="row g-3">
                <!-- Org Chart -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('human-resources.sections.show', 'org-chart') }}" class="module-card p-4 d-block h-100">
                        <div class="icon-wrapper icon-shape bg-primary text-white fs-4 shadow-sm mb-3">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Org Chart</h6>
                        <p class="text-muted small mb-0">Visualize reporting lines and structure.</p>
                    </a>
                </div>
                <!-- Performance -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('human-resources.sections.show', 'performance') }}" class="module-card p-4 d-block h-100">
                        <div class="icon-wrapper icon-shape bg-success text-white fs-4 shadow-sm mb-3">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Performance</h6>
                        <p class="text-muted small mb-0">Reviews, goals, and lecturer rankings.</p>
                    </a>
                </div>
                <!-- Shift Manager -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('human-resources.sections.show', 'shift-manager') }}" class="module-card p-4 d-block h-100">
                        <div class="icon-wrapper icon-shape bg-warning text-white fs-4 shadow-sm mb-3">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Shift Manager</h6>
                        <p class="text-muted small mb-0">Assign and manage staff schedules.</p>
                    </a>
                </div>
                <!-- Time & Attendance -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('human-resources.sections.show', 'time-tracking') }}" class="module-card p-4 d-block h-100">
                        <div class="icon-wrapper icon-shape bg-info text-white fs-4 shadow-sm mb-3">
                            <i class="bi bi-calendar2-week-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Time Tracking</h6>
                        <p class="text-muted small mb-0">Monitor ESS clock-ins and hours.</p>
                    </a>
                </div>
                <!-- Expense Claims -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('human-resources.sections.show', 'expense-claims') }}" class="module-card p-4 d-block h-100">
                        <div class="icon-wrapper icon-shape bg-danger text-white fs-4 shadow-sm mb-3">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Expense Claims</h6>
                        <p class="text-muted small mb-0">Review and approve staff expenses.</p>
                    </a>
                </div>
                <!-- Leaves -->
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('human-resources.leaves.index') }}" class="module-card p-4 d-block h-100">
                        <div class="icon-wrapper icon-shape bg-secondary text-white fs-4 shadow-sm mb-3">
                            <i class="bi bi-airplane-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Leaves</h6>
                        <p class="text-muted small mb-0">Process and track vacation/sick time.</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Staff Sidebar -->
        <div class="col-xl-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i> Recent Additions
                </h5>
                <a href="{{ route('human-resources.staff.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-4">
                        @forelse($recentStaff as $staff)
                            @php
                                $status = $staff->hrProfile?->status ?? ($staff->is_active ? 'Active' : 'Inactive');
                                $statusColor = $status === 'Active' ? 'success' : ($status === 'On Leave' ? 'info' : 'secondary');
                            @endphp
                            <div class="list-group-item p-3 border-bottom d-flex justify-content-between align-items-center bg-transparent">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="{{ $staff->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($staff->full_name).'&background=random' }}" class="avatar-circle shadow-sm">
                                        <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $statusColor }} border border-light rounded-circle" style="width: 10px; height: 10px;"></span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">{{ $staff->full_name }}</h6>
                                        <p class="text-muted small mb-0">{{ $staff->hrProfile?->job_title ?? $staff->role_name }}</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark border fw-normal">{{ $staff->hrProfile?->department ?? 'General' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center">
                                <div class="icon-shape bg-light text-muted mx-auto mb-3 fs-2 rounded-circle" style="width:60px; height:60px;">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Staff Yet</h6>
                                <p class="text-muted small mb-0">Newly added staff will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
