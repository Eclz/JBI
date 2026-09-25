@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Succession Planning</h2>
            <p class="text-muted mb-0">Identify critical positions and establish succession plans.</p>
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
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createSuccessionModal">
                <i class="bi bi-plus-circle me-1"></i> Create Plan
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
                            <h6 class="text-muted mb-1">Critical Positions</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['critical_positions'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
                            <i class="bi bi-briefcase"></i>
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
                            <h6 class="text-muted mb-1">With Successors</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['with_successors'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-person-check"></i>
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
                            <h6 class="text-muted mb-1">Without Successors</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['without_successors'] }}</h3>
                        </div>
                        <div class="p-3 bg-danger bg-opacity-10 rounded text-danger fs-4">
                            <i class="bi bi-person-exclamation"></i>
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
                            <h6 class="text-muted mb-1">Total Successors</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_successors'] }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Succession Plans List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($plans->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="text-muted mb-3"><i class="bi bi-diagram-3" style="font-size: 4rem;"></i></div>
                    <h5 class="text-muted">No succession plans yet</h5>
                    <p class="text-muted text-center mb-4">Identify critical positions and establish succession plans.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSuccessionModal">
                        Create Succession Plan
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0">Critical Position</th>
                                <th class="py-3 px-4 border-0">Current Holder</th>
                                <th class="py-3 px-4 border-0">Department</th>
                                <th class="py-3 px-4 border-0">Potential Successors</th>
                                <th class="py-3 px-4 border-0">Status</th>
                                <th class="py-3 px-4 border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="px-4 fw-bold text-primary">{{ $plan->position_name }}</td>
                                    <td class="px-4">
                                        @if($plan->currentHolder)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $plan->currentHolder->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($plan->currentHolder->name).'&background=random' }}" alt="{{ $plan->currentHolder->name }}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                <span>{{ $plan->currentHolder->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-danger fst-italic">Vacant</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-muted">{{ $plan->department ?? 'General' }}</td>
                                    <td class="px-4">
                                        @if($plan->successors->count() > 0)
                                            <div class="d-flex align-items-center gap-1">
                                                @foreach($plan->successors->take(3) as $successor)
                                                    <img src="{{ $successor->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($successor->user->name).'&background=random' }}" 
                                                         alt="{{ $successor->user->name }}" 
                                                         title="{{ $successor->user->name }} - {{ $successor->readiness_level }}"
                                                         class="rounded-circle border border-2 border-white shadow-sm" 
                                                         style="width: 32px; height: 32px; object-fit: cover; margin-right: -10px;">
                                                @endforeach
                                                @if($plan->successors->count() > 3)
                                                    <span class="badge bg-secondary ms-2 rounded-pill">+{{ $plan->successors->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-light text-danger border border-danger">0 Successors</span>
                                        @endif
                                    </td>
                                    <td class="px-4">
                                        @if($plan->status === 'Active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-end">
                                        <a href="{{ route('human-resources.succession.show', $plan->id) }}" class="btn btn-sm btn-outline-primary">
                                            View Plan
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

<!-- Create Succession Modal -->
<div class="modal fade" id="createSuccessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.succession.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Succession Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Critical Position Title</label>
                        <input type="text" name="position_name" class="form-control" required placeholder="e.g. Head of Finance">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Finance Department">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Position Holder (Optional)</label>
                        <select name="current_holder_id" class="form-select">
                            <option value="">-- Select Employee (Leave blank if vacant) --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes/Requirements</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Key skills and requirements for this position..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
