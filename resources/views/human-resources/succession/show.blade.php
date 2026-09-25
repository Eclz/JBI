@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Succession Plan Details</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('human-resources.section', 'succession') }}">Succession Planning</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $succession->position_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('human-resources.section', 'succession') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @if($succession->status === 'Active')
                <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addSuccessorModal">
                    <i class="bi bi-person-plus me-1"></i> Add Successor
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
        <!-- Left Column: Position Info -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white pt-4 border-bottom-0">
                    <h5 class="fw-bold mb-0">Target Position</h5>
                </div>
                <div class="card-body">
                    <h4 class="text-primary fw-bold mb-1">{{ $succession->position_name }}</h4>
                    <p class="text-muted mb-4">{{ $succession->department ?? 'General Department' }}</p>

                    <div class="mb-4">
                        <label class="text-muted small fw-bold text-uppercase mb-2">Current Holder</label>
                        @if($succession->currentHolder)
                            <div class="d-flex align-items-center p-3 bg-light rounded border">
                                <img src="{{ $succession->currentHolder->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($succession->currentHolder->name).'&background=random' }}" alt="{{ $succession->currentHolder->name }}" class="rounded-circle me-3 border shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $succession->currentHolder->name }}</h6>
                                    <small class="text-muted">{{ $succession->currentHolder->email }}</small>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i> This position is currently vacant.
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small fw-bold text-uppercase mb-2">Status</label>
                        <div>
                            @if($succession->status === 'Active')
                                <span class="badge bg-success fs-6">Active Plan</span>
                            @else
                                <span class="badge bg-secondary fs-6">Closed</span>
                            @endif
                        </div>
                    </div>

                    @if($succession->notes)
                    <div class="mb-2">
                        <label class="text-muted small fw-bold text-uppercase mb-1">Notes / Requirements</label>
                        <p class="small text-muted">{{ $succession->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Successors -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Identified Successors</h5>
                    <span class="badge bg-primary rounded-pill">{{ $succession->successors->count() }}</span>
                </div>
                <div class="card-body">
                    @if($succession->successors->isEmpty())
                        <div class="text-center py-5">
                            <div class="text-muted mb-3"><i class="bi bi-people" style="font-size: 3rem;"></i></div>
                            <h6 class="text-muted">No successors have been identified for this position yet.</h6>
                            <p class="text-muted small">Add employees to the succession plan to document their readiness.</p>
                            @if($succession->status === 'Active')
                                <button type="button" class="btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#addSuccessorModal">
                                    Add First Successor
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($succession->successors as $successor)
                                <div class="col-12">
                                    <div class="card border shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $successor->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($successor->user->name).'&background=random' }}" alt="{{ $successor->user->name }}" class="rounded-circle me-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $successor->user->name }}</h6>
                                                        <small class="text-primary">{{ $successor->user->hrProfile->job_title ?? 'Staff' }} • {{ $successor->user->hrProfile->department ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                                <div>
                                                    @if($successor->readiness_level === 'Ready Now')
                                                        <span class="badge bg-success">Ready Now</span>
                                                    @elseif($successor->readiness_level === '1-2 Years')
                                                        <span class="badge bg-info">Ready in 1-2 Years</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Ready in 3-5 Years</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="bg-light p-3 rounded text-muted small">
                                                <h6 class="text-uppercase fw-bold text-dark" style="font-size: 0.75rem;">Development Needs / Action Plan</h6>
                                                {{ $successor->development_needs ?: 'No specific development needs documented.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Successor Modal -->
<div class="modal fade" id="addSuccessorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.succession.add-successor', $succession->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Potential Successor</h5>
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
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Readiness Level</label>
                        <select name="readiness_level" class="form-select" required>
                            <option value="">-- Select Readiness --</option>
                            <option value="Ready Now">Ready Now</option>
                            <option value="1-2 Years">Ready with Development (1-2 Years)</option>
                            <option value="3-5 Years">Longer-term Development (3-5 Years)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Development Needs & Training Plan</label>
                        <textarea name="development_needs" class="form-control" rows="4" placeholder="Identify skills gaps and required training/mentorship..."></textarea>
                        <div class="form-text">This will form the basis of their development plan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Successor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
