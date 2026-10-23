@extends('layouts.app')

@section('title', 'Staff Onboarding Status')

@section('content')
<div class="container py-4">
    <!-- Top Welcome Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4 p-md-5 bg-gradient text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $user->profile_picture_url }}" alt="{{ $user->full_name }}" class="rounded-circle border border-3 border-white shadow-sm" style="width: 72px; height: 72px; object-fit: cover;">
                    <div>
                        <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1 rounded-pill mb-2">Staff Onboarding</span>
                        <h2 class="h3 fw-bold mb-1">Welcome, {{ $user->full_name }}!</h2>
                        <p class="mb-0 text-white-50 small">
                            <i class="bi bi-person-badge me-1"></i> Assigned Role: <strong class="text-white">{{ $user->role_name }}</strong>
                            @if($user->hrProfile?->department)
                                &bull; <i class="bi bi-building me-1"></i> {{ $user->hrProfile->department }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-md-end bg-white bg-opacity-10 p-3 rounded-3">
                    <div class="text-white-50 small mb-1">Onboarding Progress</div>
                    <div class="h2 fw-bold mb-0">{{ $onboarding?->progress_percentage ?? 0 }}%</div>
                    <small class="text-white-50">
                        {{ $tasks->where('status', 'Completed')->count() }} of {{ $tasks->count() }} tasks completed
                    </small>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="progress bg-white bg-opacity-25" style="height: 10px; border-radius: 6px;">
                    <div class="progress-bar bg-white progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $onboarding?->progress_percentage ?? 0 }}%;" aria-valuenow="{{ $onboarding?->progress_percentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Checklist Column (Left) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-card-checklist text-primary me-2"></i>Your Onboarding Checklist
                    </h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                        {{ $onboarding?->status ?? 'In Progress' }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($tasks as $index => $task)
                            @php
                                $isCompleted = $task->status === 'Completed';
                            @endphp
                            <div class="list-group-item p-3 p-md-4 d-flex align-items-start gap-3 {{ $isCompleted ? 'bg-light-subtle' : '' }}">
                                <div class="mt-1">
                                    @if($isCompleted)
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px;">
                                            <i class="bi bi-check-lg fw-bold"></i>
                                        </div>
                                    @else
                                        <div class="rounded-circle bg-secondary bg-opacity-25 text-secondary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                            {{ $index + 1 }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1 mb-1">
                                        <h6 class="mb-0 fw-bold {{ $isCompleted ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                            {{ $task->task_name }}
                                        </h6>
                                        <span class="badge {{ $isCompleted ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-3 py-1 small">
                                            {{ $task->status }}
                                        </span>
                                    </div>
                                    @if($task->comments)
                                        <p class="text-muted small mb-1">{{ $task->comments }}</p>
                                    @endif
                                    @if($isCompleted && $task->completed_date)
                                        <small class="text-success"><i class="bi bi-clock-history me-1"></i>Completed {{ \Carbon\Carbon::parse($task->completed_date)->format('M d, Y') }}</small>
                                    @endif

                                    {{-- Quick Action for Password task if pending --}}
                                    @if(!$isCompleted && str_contains(strtolower($task->task_name), 'password'))
                                        <div class="mt-2">
                                            <a href="{{ route('password.change.form') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-key me-1"></i> Change / Set Password
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No specific onboarding tasks recorded yet. HR is setting up your profile.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Status Notice Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-primary-subtle border-primary border-opacity-25">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 text-primary">
                            <i class="bi bi-shield-lock-fill fs-4"></i>
                            <h6 class="mb-0 fw-bold">System Access Information</h6>
                        </div>
                        <p class="small text-muted mb-3">
                            To ensure institutional compliance and accurate HR records, full system access to your dashboard and modules (such as <strong>{{ $user->role_name }}</strong> workflows) will be unlocked once HR completes your onboarding checklist.
                        </p>
                        <div class="p-3 bg-white rounded-3 shadow-sm mb-3">
                            <div class="small text-muted mb-1">Coordinating HR Officer:</div>
                            <div class="fw-bold text-dark">{{ $onboarding?->hrOfficer?->name ?? 'Human Resources Department' }}</div>
                            @if($onboarding?->due_date)
                                <div class="small text-muted mt-2">Target Completion:</div>
                                <div class="fw-medium text-dark">{{ $onboarding->due_date->format('F d, Y') }}</div>
                            @endif
                        </div>
                        <a href="{{ route('onboarding.staff-status') }}" class="btn btn-primary w-100 rounded-pill fw-semibold">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
                        </a>
                    </div>
                </div>

                <!-- Account Settings & Security Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-gear text-secondary me-2"></i>Account &amp; Profile</h6>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            <li>
                                <a href="{{ route('password.change.form') }}" class="btn btn-outline-secondary w-100 text-start rounded-3 py-2">
                                    <i class="bi bi-key me-2 text-primary"></i> Change Password
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary w-100 text-start rounded-3 py-2">
                                    <i class="bi bi-person me-2 text-primary"></i> View Profile
                                </a>
                            </li>
                            <li class="pt-2 border-top">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 text-start rounded-3 py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
