@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Onboarding Details</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('human-resources.section', 'onboarding') }}">Onboarding</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $onboarding->user->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('human-resources.section', 'onboarding') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @if($onboarding->status !== 'Completed')
                <button type="button" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus me-1"></i> Reassign Tasks
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Employee Info & Progress -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center pt-4">
                    <img src="{{ $onboarding->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($onboarding->user->name).'&background=random' }}" alt="{{ $onboarding->user->name }}" class="rounded-circle mb-3 border border-3 border-light shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    <h4 class="mb-1 fw-bold">{{ $onboarding->user->name }}</h4>
                    <p class="text-primary fw-semibold mb-2">{{ $onboarding->user->hrProfile->job_title ?? 'Staff' }}</p>
                    <span class="badge bg-light text-dark border mb-3">{{ $onboarding->user->hrProfile->department ?? 'General' }}</span>
                    
                    <hr>
                    
                    <div class="text-start mb-3">
                        <label class="text-muted small fw-bold text-uppercase">Onboarding Status</label>
                        <div class="d-flex align-items-center mt-1">
                            @if($onboarding->status === 'Completed')
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i> Completed</span>
                            @elseif($onboarding->status === 'In Progress')
                                <span class="badge bg-info fs-6"><i class="bi bi-arrow-repeat me-1"></i> In Progress</span>
                            @elseif($onboarding->status === 'Overdue')
                                <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle me-1"></i> Overdue</span>
                            @else
                                <span class="badge bg-secondary fs-6">Not Started</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-start mb-3">
                        <label class="text-muted small fw-bold text-uppercase d-flex justify-content-between">
                            <span>Overall Progress</span>
                            <span>{{ $onboarding->progress_percentage }}%</span>
                        </label>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar {{ $onboarding->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $onboarding->progress_percentage }}%" aria-valuenow="{{ $onboarding->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="text-start mb-3 row g-2">
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase">Start Date</label>
                            <div>{{ $onboarding->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase">Due Date</label>
                            <div class="{{ $onboarding->due_date < now() && $onboarding->status !== 'Completed' ? 'text-danger fw-bold' : '' }}">
                                {{ $onboarding->due_date->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="text-start">
                        <label class="text-muted small fw-bold text-uppercase">HR Officer</label>
                        <div>{{ $onboarding->hrOfficer->name ?? 'Unassigned' }}</div>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('human-resources.staff.show', $onboarding->user->id) }}" class="btn btn-outline-primary w-100">
                <i class="bi bi-person-vcard me-2"></i> View Full Employee Profile
            </a>
        </div>

        <!-- Right Column: Checklist -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Onboarding Checklist</h5>
                    <span class="badge bg-light text-dark border">{{ $onboarding->tasks->where('status', 'Completed')->count() }} / {{ $onboarding->tasks->count() }} Tasks</span>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($onboarding->tasks as $task)
                            <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3 border-bottom">
                                <!-- Status toggle form -->
                                <form action="{{ route('human-resources.onboarding.tasks.update', $task->id) }}" method="POST" class="mt-1">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $task->status === 'Completed' ? 'Pending' : 'Completed' }}">
                                    <button type="submit" class="btn btn-link p-0 text-decoration-none shadow-none">
                                        @if($task->status === 'Completed')
                                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                        @else
                                            <i class="bi bi-circle text-muted fs-4"></i>
                                        @endif
                                    </button>
                                </form>
                                
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $task->status === 'Completed' ? 'text-muted text-decoration-line-through' : 'fw-bold' }}">
                                        {{ $task->task_name }}
                                    </h6>
                                    <div class="d-flex gap-3 text-muted small">
                                        <span><i class="bi bi-person me-1"></i> {{ $task->assignee->name ?? 'Unassigned' }}</span>
                                        @if($task->completed_date)
                                            <span><i class="bi bi-calendar-check me-1"></i> {{ $task->completed_date->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <button class="btn btn-sm btn-light border text-muted" title="Add Comment/Attachment">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <p>No tasks found for this onboarding process.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
