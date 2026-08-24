@extends('layouts.app')

@section('title', 'E-Voting & Student Leadership Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary fw-bold">
                <i class="bi bi-check2-square me-2"></i>E-Voting & Student Leadership Management
            </h1>
            <p class="text-muted mb-0">Manage election seasons, electoral commission, faculty & university-wide positions, candidate vetting, and voting outcomes</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.evoting.leaders') }}" class="btn btn-outline-success fw-bold shadow-sm">
                <i class="bi bi-award me-1"></i>Elected Student Leaders
            </a>
            <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                <i class="bi bi-plus-lg me-1"></i>Create Election Season
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Total Seasons</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $stats['total_elections'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Active Voting</div>
                    <div class="h3 fw-bold text-success mb-0">{{ $stats['active_voting'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Total Applicants</div>
                    <div class="h3 fw-bold text-info mb-0">{{ $stats['total_candidates'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Approved Candidates</div>
                    <div class="h3 fw-bold text-primary mb-0">{{ $stats['approved_candidates'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Total Votes Cast</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ number_format($stats['total_votes_cast'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Elected Leaders</div>
                    <div class="h3 fw-bold text-warning mb-0">{{ $stats['elected_leaders'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.evoting.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search election title or description..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Election Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="applications_open" {{ request('status') == 'applications_open' ? 'selected' : '' }}>Applications Open</option>
                        <option value="vetting" {{ request('status') == 'vetting' ? 'selected' : '' }}>Candidate Vetting</option>
                        <option value="voting_scheduled" {{ request('status') == 'voting_scheduled' ? 'selected' : '' }}>Voting Scheduled</option>
                        <option value="voting_open" {{ request('status') == 'voting_open' ? 'selected' : '' }}>Voting in Progress</option>
                        <option value="voting_closed" {{ request('status') == 'voting_closed' ? 'selected' : '' }}>Voting Closed</option>
                        <option value="results_under_review" {{ request('status') == 'results_under_review' ? 'selected' : '' }}>Results Under Review</option>
                        <option value="results_published" {{ request('status') == 'results_published' ? 'selected' : '' }}>Results Published</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold"><i class="bi bi-filter me-1"></i>Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.evoting.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Election Seasons Cards -->
    <div class="row g-4">
        @forelse($sessions as $session)
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden d-flex flex-column">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $session->status_badge_color }} py-1 px-2 text-uppercase fw-bold small">
                            {{ $session->status_label }}
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.evoting.show', $session) }}">
                                        <i class="bi bi-gear me-2 text-primary"></i>Manage Election
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.evoting.results', $session) }}">
                                        <i class="bi bi-bar-chart me-2 text-info"></i>View Results
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.evoting.sessions.destroy', $session) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this election season and all its data?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i>Delete Election
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <a href="{{ route('admin.evoting.show', $session) }}" class="text-decoration-none text-dark">
                                    {{ $session->title }}
                                </a>
                            </h5>
                            <div class="small text-muted mb-3">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $session->academicYear->name ?? 'Academic Year' }}</span>
                                <span class="mx-1">&bull;</span>
                                <span>Semester {{ $session->target_semester }}</span>
                            </div>

                            @if($session->description)
                                <p class="text-muted small mb-3 text-truncate-2">{{ Str::limit($session->description, 110) }}</p>
                            @endif

                            <!-- Key Metrics -->
                            <div class="row g-2 mb-3 text-center">
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="fw-bold text-primary">{{ $session->positions->count() }}</div>
                                        <div class="text-muted" style="font-size: 11px;">Positions</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="fw-bold text-success">{{ $session->candidates->count() }}</div>
                                        <div class="text-muted" style="font-size: 11px;">Candidates</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="fw-bold text-dark">{{ $session->votes->count() }}</div>
                                        <div class="text-muted" style="font-size: 11px;">Votes Cast</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Preview -->
                            <div class="small bg-light rounded p-2 mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Applications:</span>
                                    <span class="fw-semibold">
                                        {{ $session->application_start_at ? $session->application_start_at->format('M d') : 'TBD' }} - 
                                        {{ $session->application_end_at ? $session->application_end_at->format('M d, Y') : 'TBD' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Voting Session:</span>
                                    <span class="fw-semibold {{ $session->is_voting_open ? 'text-success' : '' }}">
                                        {{ $session->start_time ? $session->start_time->format('M d, H:i') : 'TBD' }} - 
                                        {{ $session->end_time ? $session->end_time->format('M d, H:i') : 'TBD' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="d-flex gap-2 pt-2 border-top">
                            <a href="{{ route('admin.evoting.show', $session) }}" class="btn btn-sm btn-primary w-100 fw-bold">
                                <i class="bi bi-sliders me-1"></i>Manage & Vet
                            </a>
                            <a href="{{ route('admin.evoting.results', $session) }}" class="btn btn-sm btn-outline-info" title="View Results">
                                <i class="bi bi-bar-chart"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm py-5 text-center bg-white rounded-3">
                    <div class="card-body">
                        <i class="bi bi-box-seam display-3 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold text-dark mb-1">No Election Seasons Found</h4>
                        <p class="text-muted mb-4">Create your first election season to start managing electoral positions, candidate applications, vetting, and voting.</p>
                        <button class="btn btn-primary fw-bold px-4" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                            <i class="bi bi-plus-lg me-1"></i>Create Election Season
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($sessions->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

<!-- Modal: Create Election Season -->
<div class="modal fade" id="createSessionModal" tabindex="-1" aria-labelledby="createSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.evoting.sessions.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="createSessionModalLabel">
                        <i class="bi bi-calendar-plus me-2"></i>Create New Election Season
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Election Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Guild Presidential & Cabinet Elections 2026/2027" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">Select Academic Year...</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                        {{ $year->name }} ({{ $year->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Semester *</label>
                            <select name="target_semester" class="form-select" required>
                                <option value="1">Semester 1</option>
                                <option value="2" selected>Semester 2</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description & Guidelines</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief notes or guidelines regarding this election season..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-primary mb-2"><i class="bi bi-clock-history me-1"></i>Key Election Milestones</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Candidacy Applications Open</label>
                            <input type="datetime-local" name="application_start_at" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Candidacy Applications Deadline</label>
                            <input type="datetime-local" name="application_end_at" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Vetting Window Starts</label>
                            <input type="datetime-local" name="vetting_start_at" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Vetting Window Closes</label>
                            <input type="datetime-local" name="vetting_end_at" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Voting Session Starts</label>
                            <input type="datetime-local" name="start_time" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Voting Session Ends</label>
                            <input type="datetime-local" name="end_time" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Initial Election Status *</label>
                            <select name="status" class="form-select" required>
                                <option value="draft" selected>Draft / Setup</option>
                                <option value="applications_open">Applications Open</option>
                                <option value="vetting">Candidate Vetting</option>
                                <option value="voting_scheduled">Voting Scheduled</option>
                                <option value="voting_open">Voting in Progress</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-check2-circle me-1"></i>Create Season & Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
