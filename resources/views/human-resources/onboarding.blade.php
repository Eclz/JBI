@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Onboarding</h2>
            <p class="text-muted mb-0">Manage new employee onboarding and onboarding tasks.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#startOnboardingModal">
                <i class="bi bi-plus-circle me-1"></i> Start Onboarding
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Onboarding</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-white fs-4">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">In Progress</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['in_progress'] }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Completed</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['completed'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Overdue</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['overdue'] }}</h3>
                        </div>
                        <div class="p-3 bg-danger bg-opacity-10 rounded text-white fs-4">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Onboarding List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($onboardings->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="text-muted mb-3"><i class="bi bi-person-lines-fill" style="font-size: 4rem;"></i></div>
                    <h5 class="text-muted">No onboarding records yet</h5>
                    <p class="text-muted text-center mb-4">Start an onboarding process for a newly hired employee.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#startOnboardingModal">
                        Start Onboarding
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0">Employee</th>
                                <th class="py-3 px-4 border-0">Department</th>
                                <th class="py-3 px-4 border-0">Status</th>
                                <th class="py-3 px-4 border-0">Start Date</th>
                                <th class="py-3 px-4 border-0">Progress</th>
                                <th class="py-3 px-4 border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($onboardings as $onboard)
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $onboard->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($onboard->user->name).'&background=random' }}" alt="{{ $onboard->user->name }}" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $onboard->user->name }}</h6>
                                                <small class="text-muted">{{ $onboard->user->hrProfile->job_title ?? 'Staff' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 text-muted">{{ $onboard->user->hrProfile->department ?? 'General' }}</td>
                                    <td class="px-4">
                                        @if($onboard->status === 'Completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($onboard->status === 'In Progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @elseif($onboard->status === 'Overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-secondary">Not Started</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-muted">
                                        {{ $onboard->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar {{ $onboard->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $onboard->progress_percentage }}%" aria-valuenow="{{ $onboard->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted fw-bold" style="width: 35px;">{{ $onboard->progress_percentage }}%</small>
                                        </div>
                                    </td>
                                    <td class="px-4 text-end">
                                        <a href="{{ route('human-resources.onboarding.show', $onboard->id) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Start Onboarding Modal -->
<div class="modal fade" id="startOnboardingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.onboarding.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Start Onboarding Process</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select a newly hired employee to begin their onboarding.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template</label>
                        <select name="template_name" class="form-select" required>
                            <option value="Standard">Standard Onboarding</option>
                            <option value="Management">Management Onboarding</option>
                            <option value="Faculty">Faculty Onboarding</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Process</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
