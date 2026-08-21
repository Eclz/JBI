@extends('layouts.app')

@section('title', 'Manage Election: ' . $session->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Back -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.evoting.index') }}">E-Voting</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($session->title, 40) }}</li>
            </ol>
        </nav>
        <a href="{{ route('admin.evoting.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Elections
        </a>
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

    <!-- Election Header Card -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-4 bg-white">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-{{ $session->status_badge_color }} py-1 px-3 fs-6 fw-bold text-uppercase">
                            {{ $session->status_label }}
                        </span>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-calendar3 me-1"></i>{{ $session->academicYear->name ?? 'Academic Year' }} (Sem {{ $session->target_semester }})
                        </span>
                    </div>
                    <h2 class="h4 fw-bold text-dark mb-1">{{ $session->title }}</h2>
                    <p class="text-muted small mb-0">{{ $session->description ?: 'No detailed description provided.' }}</p>
                </div>

                <!-- Quick Status Switcher & Actions -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editSessionModal">
                        <i class="bi bi-pencil me-1"></i>Edit Details
                    </button>
                    <a href="{{ route('admin.evoting.results', $session) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-bar-chart me-1"></i>Results View
                    </a>

                    <!-- Status Transition Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-arrow-repeat me-1"></i>Change Election Status
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><h6 class="dropdown-header text-uppercase small">Transition Lifecycle</h6></li>
                            @foreach([
                                'draft' => 'Draft / Setup',
                                'applications_open' => 'Open Applications',
                                'vetting' => 'Start Candidate Vetting',
                                'voting_scheduled' => 'Schedule Voting',
                                'voting_open' => 'Open Voting Session Now',
                                'voting_closed' => 'Close Voting Session',
                                'results_under_review' => 'Results Under Review',
                                'results_published' => 'Publish Official Results',
                                'completed' => 'Mark as Completed'
                            ] as $stKey => $stLabel)
                                <li>
                                    <form action="{{ route('admin.evoting.sessions.status', $session) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $stKey }}">
                                        <button type="submit" class="dropdown-item {{ $session->status === $stKey ? 'active fw-bold' : '' }}" {{ $session->status === $stKey ? 'disabled' : '' }}>
                                            <i class="bi bi-chevron-right me-1 small"></i>{{ $stLabel }}
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Summary Bar -->
        <div class="card-footer bg-light px-4 py-3 border-top">
            <div class="row g-3 text-center text-md-start">
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Electoral Commission:</span>
                    <strong class="text-dark">{{ $session->commissionMembers->count() }} Members</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Electoral Positions:</span>
                    <strong class="text-dark">{{ $session->positions->count() }} Positions</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Candidates Vetted:</span>
                    <strong class="text-success">{{ $session->candidates->where('candidate_status', 'approved_candidate')->count() }} / {{ $session->candidates->count() }} Approved</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted small d-block">Voter Turnout:</span>
                    <strong class="text-primary">{{ $turnoutPercentage }}% ({{ $totalVotersCount }} Voted)</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-3 shadow-sm border" id="electionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                <i class="bi bi-speedometer2 me-1"></i>Overview & Metrics
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="commission-tab" data-bs-toggle="tab" data-bs-target="#commission" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i>Electoral Commission ({{ $session->commissionMembers->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="positions-tab" data-bs-toggle="tab" data-bs-target="#positions" type="button" role="tab">
                <i class="bi bi-award me-1"></i>Electoral Positions ({{ $session->positions->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="vetting-tab" data-bs-toggle="tab" data-bs-target="#vetting" type="button" role="tab">
                <i class="bi bi-person-check me-1"></i>Candidate Vetting Hub ({{ $session->candidates->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button" role="tab">
                <i class="bi bi-bar-chart-line me-1"></i>Live Turnout & Results
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="electionTabsContent">
        <!-- Tab 1: Overview & Metrics -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Timeline Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-event me-2 text-primary"></i>Election Lifecycle Schedule</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 h-100 border">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="badge bg-info text-dark me-2">Stage 1</div>
                                            <h6 class="fw-bold mb-0">Applications</h6>
                                        </div>
                                        <div class="small text-muted mb-1">Start: <strong>{{ $session->application_start_at ? $session->application_start_at->format('M d, Y H:i') : 'Not Configured' }}</strong></div>
                                        <div class="small text-muted">End: <strong>{{ $session->application_end_at ? $session->application_end_at->format('M d, Y H:i') : 'Not Configured' }}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 h-100 border">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="badge bg-warning text-dark me-2">Stage 2</div>
                                            <h6 class="fw-bold mb-0">Candidate Vetting</h6>
                                        </div>
                                        <div class="small text-muted mb-1">Start: <strong>{{ $session->vetting_start_at ? $session->vetting_start_at->format('M d, Y H:i') : 'Not Configured' }}</strong></div>
                                        <div class="small text-muted">End: <strong>{{ $session->vetting_end_at ? $session->vetting_end_at->format('M d, Y H:i') : 'Not Configured' }}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3 h-100 border border-success">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="badge bg-success me-2">Stage 3</div>
                                            <h6 class="fw-bold mb-0 text-success">Voting Session</h6>
                                        </div>
                                        <div class="small text-muted mb-1">Opens: <strong>{{ $session->start_time ? $session->start_time->format('M d, Y H:i') : 'Not Configured' }}</strong></div>
                                        <div class="small text-muted">Closes: <strong>{{ $session->end_time ? $session->end_time->format('M d, Y H:i') : 'Not Configured' }}</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Positions Overview Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-award me-2 text-primary"></i>Positions Contested</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                                <i class="bi bi-plus-lg me-1"></i>Add Position
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Position Title</th>
                                            <th>Scope</th>
                                            <th>Faculty Restriction</th>
                                            <th>Approved Candidates</th>
                                            <th>Votes Cast</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($session->positions as $pos)
                                            <tr>
                                                <td>{{ $pos->display_order }}</td>
                                                <td class="fw-bold text-dark">{{ $pos->title }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $pos->scope === 'university_wide' ? 'primary' : 'info' }}">
                                                        {{ $pos->scope === 'university_wide' ? 'University-Wide' : 'Faculty-Specific' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($pos->scope === 'faculty_specific' && $pos->faculty)
                                                        <span class="text-dark fw-semibold"><i class="bi bi-building me-1"></i>{{ $pos->faculty->name }}</span>
                                                    @else
                                                        <span class="text-muted small">All Faculties Eligible</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $pos->approvedCandidates->count() }} Candidates
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-primary">{{ $pos->votes->count() }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No positions configured yet. Click "Add Position" above.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Turnout Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Participation Summary</h5>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="display-4 fw-bold text-primary mb-2">{{ $turnoutPercentage }}%</div>
                            <p class="text-muted small mb-3">Total Registered Student Voters who have cast their ballot</p>

                            <div class="progress mb-3" style="height: 12px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ min(100, $turnoutPercentage) }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between small text-muted border-top pt-2">
                                <span>Voted: <strong>{{ $totalVotersCount }}</strong></span>
                                <span>Eligible: <strong>{{ $totalEligibleStudents }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Commission Summary -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check me-2 text-primary"></i>Commission</h5>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCommissionModal">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group list-group-flush">
                                @forelse($session->commissionMembers as $comm)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $comm->user->name ?? 'User' }}</div>
                                            <span class="badge bg-secondary" style="font-size: 10px;">{{ $comm->role_title }}</span>
                                        </div>
                                        <span class="badge bg-light text-muted border">{{ ucfirst($comm->user->role ?? 'User') }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item px-0 text-muted small text-center">No commissioners appointed yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Electoral Commission -->
        <div class="tab-pane fade" id="commission" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check me-2 text-primary"></i>Electoral Commission Team</h5>
                        <p class="text-muted small mb-0">Authorized users (students, faculty staff, administrators) entrusted to manage and supervise the election process</p>
                    </div>
                    <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addCommissionModal">
                        <i class="bi bi-person-plus me-1"></i>Appoint Commission Member
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Member Name</th>
                                    <th>Email</th>
                                    <th>System Role</th>
                                    <th>Commission Role</th>
                                    <th>Appointed At</th>
                                    <th>Notes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($session->commissionMembers as $comm)
                                    <tr>
                                        <td class="fw-bold text-dark">
                                            <i class="bi bi-person-badge text-primary me-2"></i>{{ $comm->user->name ?? 'N/A' }}
                                        </td>
                                        <td>{{ $comm->user->email ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ ucfirst($comm->user->role ?? 'User') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ $comm->role_title }}</span>
                                        </td>
                                        <td class="small text-muted">{{ $comm->appointed_at ? $comm->appointed_at->format('M d, Y') : '-' }}</td>
                                        <td class="small text-muted">{{ $comm->notes ?? '-' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.evoting.commission.destroy', [$session, $comm]) }}" method="POST" onsubmit="return confirm('Remove this member from the Electoral Commission?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Commissioner">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No commission members appointed yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Electoral Positions -->
        <div class="tab-pane fade" id="positions" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-award me-2 text-primary"></i>Electoral Positions & Offices</h5>
                        <p class="text-muted small mb-0">Define positions, voting limits, eligibility requirements, and scope restrictions (university-wide vs faculty-specific)</p>
                    </div>
                    <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                        <i class="bi bi-plus-lg me-1"></i>Add Electoral Position
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Position Title</th>
                                    <th>Scope & Faculty Restriction</th>
                                    <th>Max Votes / Voter</th>
                                    <th>Requirements</th>
                                    <th>Candidates</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($session->positions as $pos)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $pos->display_order }}</span></td>
                                        <td class="fw-bold text-dark">{{ $pos->title }}</td>
                                        <td>
                                            @if($pos->scope === 'university_wide')
                                                <span class="badge bg-primary"><i class="bi bi-globe me-1"></i>University-Wide Position</span>
                                            @else
                                                <span class="badge bg-info text-dark">
                                                    <i class="bi bi-building me-1"></i>Faculty: {{ $pos->faculty->name ?? 'Faculty Specific' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $pos->max_votes_per_voter }} vote(s)</td>
                                        <td class="small text-muted">{{ Str::limit($pos->requirements ?: 'Standard university eligibility', 50) }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $pos->candidates->count() }} Total</span>
                                            <span class="badge bg-success">{{ $pos->approvedCandidates->count() }} Approved</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPositionModal{{ $pos->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('admin.evoting.positions.destroy', $pos) }}" method="POST" onsubmit="return confirm('Delete this electoral position and all its candidates?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Position Modal -->
                                    <div class="modal fade" id="editPositionModal{{ $pos->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('admin.evoting.positions.update', $pos) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-primary text-white py-3">
                                                        <h5 class="modal-title fw-bold">Edit Position: {{ $pos->title }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Position Title *</label>
                                                            <input type="text" name="title" class="form-control" value="{{ $pos->title }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Position Scope *</label>
                                                            <select name="scope" class="form-select scope-select" data-target="#editFacultySelect{{ $pos->id }}" required>
                                                                <option value="university_wide" {{ $pos->scope === 'university_wide' ? 'selected' : '' }}>University-Wide Position (All Students Can Vote)</option>
                                                                <option value="faculty_specific" {{ $pos->scope === 'faculty_specific' ? 'selected' : '' }}>Faculty-Specific Position (Only Faculty Students Can Vote)</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3" id="editFacultySelect{{ $pos->id }}" style="{{ $pos->scope === 'faculty_specific' ? '' : 'display: none;' }}">
                                                            <label class="form-label fw-semibold">Select Faculty *</label>
                                                            <select name="faculty_id" class="form-select">
                                                                <option value="">Select Faculty...</option>
                                                                @foreach($faculties as $fac)
                                                                    <option value="{{ $fac->id }}" {{ $pos->faculty_id == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label fw-semibold">Max Votes / Voter</label>
                                                                <input type="number" name="max_votes_per_voter" class="form-control" value="{{ $pos->max_votes_per_voter }}" min="1" max="5" required>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label fw-semibold">Display Order</label>
                                                                <input type="number" name="display_order" class="form-control" value="{{ $pos->display_order }}" min="1" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Requirements / Criteria</label>
                                                            <textarea name="requirements" class="form-control" rows="2">{{ $pos->requirements }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light py-3">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary fw-bold">Update Position</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No positions created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Candidate Applications & Vetting Hub -->
        <div class="tab-pane fade" id="vetting" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-check me-2 text-primary"></i>Candidate Vetting & Approval Hub</h5>
                        <p class="text-muted small mb-0">Review student applications, inspect manifestos & academic standing, and vet applicants to place approved candidates on the ballot</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Applicant Candidate</th>
                                    <th>Position Applied</th>
                                    <th>Faculty & Year</th>
                                    <th>CGPA</th>
                                    <th>Application Status</th>
                                    <th>Ballot Status</th>
                                    <th>Vetting Score</th>
                                    <th class="text-end">Vetting Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($session->candidates as $cand)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($cand->photo)
                                                    <img src="{{ asset('storage/' . $cand->photo) }}" class="rounded-circle object-fit-cover shadow-sm" width="38" height="38" alt="{{ $cand->name }}">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                                        {{ strtoupper(substr($cand->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $cand->name }}</div>
                                                    <small class="text-muted">{{ $cand->user->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-primary">{{ $cand->position->title ?? 'N/A' }}</td>
                                        <td>
                                            <div class="small fw-semibold">{{ $cand->faculty->name ?? ($cand->user->studentProfile?->department?->faculty?->name ?? 'General') }}</div>
                                            <small class="text-muted">Year {{ $cand->year_of_study ?: ($cand->user->studentProfile?->year_of_study ?? 1) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $cand->cgpa ? number_format($cand->cgpa, 2) : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $cand->application_status_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $cand->application_status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $cand->candidate_status_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $cand->candidate_status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($cand->vetting_score !== null)
                                                <span class="fw-bold text-success">{{ $cand->vetting_score }}/100</span>
                                            @else
                                                <span class="text-muted small">Not Scored</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#vetModal{{ $cand->id }}">
                                                <i class="bi bi-clipboard-check me-1"></i>Review & Vet
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Candidate Vetting Modal -->
                                    <div class="modal fade" id="vetModal{{ $cand->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('admin.evoting.candidates.vet', $cand) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-primary text-white py-3">
                                                        <h5 class="modal-title fw-bold">
                                                            <i class="bi bi-person-lines-fill me-2"></i>Candidate Vetting Review: {{ $cand->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="row g-3 mb-4">
                                                            <div class="col-md-3 text-center">
                                                                @if($cand->photo)
                                                                    <img src="{{ asset('storage/' . $cand->photo) }}" class="rounded-3 img-fluid shadow mb-2" style="max-height: 140px;" alt="{{ $cand->name }}">
                                                                @else
                                                                    <div class="rounded-3 bg-light text-muted p-4 border text-center mb-2">No Photo</div>
                                                                @endif
                                                                <span class="badge bg-{{ $cand->candidate_status_badge }} w-100">
                                                                    {{ ucfirst(str_replace('_', ' ', $cand->candidate_status)) }}
                                                                </span>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <h4 class="fw-bold mb-1">{{ $cand->name }}</h4>
                                                                <p class="text-primary fw-semibold mb-2">Contesting for: {{ $cand->position->title }}</p>
                                                                <div class="row g-2 small text-muted mb-2">
                                                                    <div class="col-6"><strong>Faculty:</strong> {{ $cand->faculty->name ?? 'General' }}</div>
                                                                    <div class="col-6"><strong>Year of Study:</strong> Year {{ $cand->year_of_study }}</div>
                                                                    <div class="col-6"><strong>Cumulative GPA:</strong> {{ $cand->cgpa ? number_format($cand->cgpa, 2) : 'N/A' }}</div>
                                                                    <div class="col-6"><strong>Party/Slogan:</strong> {{ $cand->slogan ?: ($cand->party_affiliation ?: 'Independent') }}</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Manifesto -->
                                                        <div class="card bg-light border mb-3">
                                                            <div class="card-header bg-white py-2 fw-bold small text-dark">
                                                                <i class="bi bi-file-earmark-text me-1 text-primary"></i>Candidate Manifesto & Campaign Statement
                                                            </div>
                                                            <div class="card-body py-2">
                                                                <p class="small text-dark mb-0 whitespace-pre-line">{{ $cand->manifesto }}</p>
                                                            </div>
                                                        </div>

                                                        <!-- Supporting Documents -->
                                                        @if($cand->supporting_documents && is_array($cand->supporting_documents) && count($cand->supporting_documents) > 0)
                                                            <div class="card bg-light border mb-3">
                                                                <div class="card-header bg-white py-2 fw-bold small text-dark">
                                                                    <i class="bi bi-paperclip me-1 text-primary"></i>Submitted Supporting Documents
                                                                </div>
                                                                <div class="card-body py-2">
                                                                    <div class="d-flex gap-2 flex-wrap">
                                                                        @foreach($cand->supporting_documents as $doc)
                                                                            <a href="{{ asset('storage/' . ($doc['path'] ?? '')) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                                <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ $doc['name'] ?? 'View Document' }}
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Vetting Decision Section -->
                                                        <div class="border-top pt-3">
                                                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-check me-1"></i>Electoral Commission Vetting Decision</h6>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold">Vetting Decision *</label>
                                                                    <select name="application_status" class="form-select" required>
                                                                        <option value="vetted_approved" {{ $cand->application_status === 'vetted_approved' ? 'selected' : '' }}>
                                                                            Vetted & Approved (Place on Official Ballot)
                                                                        </option>
                                                                        <option value="under_review" {{ $cand->application_status === 'under_review' ? 'selected' : '' }}>
                                                                            Under Review / Revision Requested
                                                                        </option>
                                                                        <option value="rejected" {{ $cand->application_status === 'rejected' ? 'selected' : '' }}>
                                                                            Rejected (Disqualified from Ballot)
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold">Vetting Score (0 - 100)</label>
                                                                    <input type="number" name="vetting_score" class="form-control" value="{{ $cand->vetting_score }}" min="0" max="100" placeholder="e.g. 85">
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label class="form-label fw-semibold">Vetting Comments & Feedback Notes</label>
                                                                    <textarea name="vetting_notes" class="form-control" rows="2" placeholder="Commission feedback or reasons for approval/rejection...">{{ $cand->vetting_notes }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light py-3">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary fw-bold px-4">
                                                            <i class="bi bi-check2-circle me-1"></i>Submit Vetting Verdict
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No candidate applications received for this election yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 5: Live Turnout & Results -->
        <div class="tab-pane fade" id="results" role="tabpanel">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Live Tally & Official Outcome</h5>
                        <p class="text-muted small mb-0">Real-time vote aggregation, candidate percentages, and student leader winners</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if($session->status !== 'results_published' && $session->status !== 'completed')
                            <form action="{{ route('admin.evoting.sessions.publish', $session) }}" method="POST" onsubmit="return confirm('Publish official election results? This will declare the winners and officially designate them as Elected Student Leaders.');">
                                @csrf
                                <button type="submit" class="btn btn-success fw-bold">
                                    <i class="bi bi-award me-1"></i>Publish Official Results & Elect Leaders
                                </button>
                            </form>
                        @else
                            <span class="badge bg-success fs-6 py-2 px-3"><i class="bi bi-check-all me-1"></i>Results Officially Published</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    @forelse($session->positions as $position)
                        <div class="card border mb-4 shadow-sm">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">{{ $position->title }}</span>
                                <span class="badge bg-primary">{{ $position->votes->count() }} Total Votes Cast</span>
                            </div>
                            <div class="card-body p-3">
                                @php
                                    $posTotalVotes = $position->votes->count();
                                    $posCandidates = $position->candidates()
                                        ->whereIn('candidate_status', ['approved_candidate', 'elected_student_leader'])
                                        ->withCount('votes')
                                        ->orderByDesc('votes_count')
                                        ->get();
                                    $winner = $posCandidates->first();
                                @endphp

                                @forelse($posCandidates as $cand)
                                    @php
                                        $candPct = $posTotalVotes > 0 ? round(($cand->votes_count / $posTotalVotes) * 100, 1) : 0;
                                        $isWinner = ($winner && $winner->id === $cand->id && $cand->votes_count > 0);
                                    @endphp
                                    <div class="mb-3 p-2 rounded {{ $isWinner ? 'bg-light border border-success' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($isWinner)
                                                    <i class="bi bi-trophy-fill text-warning fs-5" title="Leading Candidate / Winner"></i>
                                                @endif
                                                <span class="fw-bold text-dark">{{ $cand->name }}</span>
                                                <small class="text-muted">({{ $cand->slogan ?: ($cand->party_affiliation ?: 'Independent') }})</small>
                                                @if($cand->candidate_status === 'elected_student_leader')
                                                    <span class="badge bg-success ms-1"><i class="bi bi-award me-1"></i>Elected Student Leader</span>
                                                @endif
                                            </div>
                                            <div>
                                                <strong class="text-primary">{{ $cand->votes_count }} votes</strong>
                                                <span class="text-muted small">({{ $candPct }}%)</span>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar {{ $isWinner ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $candPct }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted small py-2">No approved candidates contesting for this position.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No positions or votes recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Session Details -->
<div class="modal fade" id="editSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.evoting.sessions.update', $session) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">Edit Election Season Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Election Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ $session->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $session->academic_year_id == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} ({{ $year->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Semester</label>
                            <select name="target_semester" class="form-select" required>
                                <option value="1" {{ $session->target_semester == 1 ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ $session->target_semester == 2 ? 'selected' : '' }}>Semester 2</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ $session->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Application Opens</label>
                            <input type="datetime-local" name="application_start_at" class="form-control form-control-sm" value="{{ $session->application_start_at ? $session->application_start_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Application Closes</label>
                            <input type="datetime-local" name="application_end_at" class="form-control form-control-sm" value="{{ $session->application_end_at ? $session->application_end_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Vetting Starts</label>
                            <input type="datetime-local" name="vetting_start_at" class="form-control form-control-sm" value="{{ $session->vetting_start_at ? $session->vetting_start_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Vetting Ends</label>
                            <input type="datetime-local" name="vetting_end_at" class="form-control form-control-sm" value="{{ $session->vetting_end_at ? $session->vetting_end_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Voting Opens</label>
                            <input type="datetime-local" name="start_time" class="form-control form-control-sm" value="{{ $session->start_time ? $session->start_time->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Voting Closes</label>
                            <input type="datetime-local" name="end_time" class="form-control form-control-sm" value="{{ $session->end_time ? $session->end_time->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach([
                                    'draft' => 'Draft',
                                    'applications_open' => 'Applications Open',
                                    'vetting' => 'Candidate Vetting',
                                    'voting_scheduled' => 'Voting Scheduled',
                                    'voting_open' => 'Voting in Progress',
                                    'voting_closed' => 'Voting Closed',
                                    'results_under_review' => 'Results Under Review',
                                    'results_published' => 'Results Published',
                                    'completed' => 'Completed'
                                ] as $stKey => $stVal)
                                    <option value="{{ $stKey }}" {{ $session->status === $stKey ? 'selected' : '' }}>{{ $stVal }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Commission Member -->
<div class="modal fade" id="addCommissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.evoting.commission.store', $session) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-shield-plus me-2"></i>Appoint Electoral Commission Member
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select System User *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select User (Student, Faculty, Staff)...</option>
                            @foreach($eligibleCommissionUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }}) &mdash; {{ ucfirst($user->role) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Any system user (student, lecturer, or administrator) can be appointed.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Commission Role Title *</label>
                        <select name="role_title" class="form-select" required>
                            <option value="Electoral Chairperson">Electoral Chairperson</option>
                            <option value="Vice Chairperson">Vice Chairperson</option>
                            <option value="Returning Officer">Returning Officer</option>
                            <option value="Electoral Secretary">Electoral Secretary</option>
                            <option value="Chief Vetting Officer">Chief Vetting Officer</option>
                            <option value="Electoral Commissioner" selected>Electoral Commissioner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Appointment Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Responsibilities or assigned polling station/faculty..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Confirm Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Position -->
<div class="modal fade" id="addPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.evoting.positions.store', $session) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-award me-2"></i>Create Electoral Position
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Position Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Guild President, Faculty Representative" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Position Scope *</label>
                        <select name="scope" id="addPositionScope" class="form-select" required>
                            <option value="university_wide" selected>University-Wide Position (All Students Can Vote)</option>
                            <option value="faculty_specific">Faculty-Specific Position (Only Faculty Students Can Vote)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="addFacultySelectDiv" style="display: none;">
                        <label class="form-label fw-semibold">Restricted to Faculty *</label>
                        <select name="faculty_id" class="form-select">
                            <option value="">Select Faculty...</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Candidates and voters must strictly belong to this faculty.</div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Max Votes / Voter</label>
                            <input type="number" name="max_votes_per_voter" class="form-control" value="1" min="1" max="5" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="{{ $session->positions->count() + 1 }}" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Requirements / Eligibility Notes</label>
                        <textarea name="requirements" class="form-control" rows="2" placeholder="e.g. Minimum CGPA of 3.0, Year 2 or 3 student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Add Position</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scopeSelect = document.getElementById('addPositionScope');
    const facultyDiv = document.getElementById('addFacultySelectDiv');

    if (scopeSelect && facultyDiv) {
        scopeSelect.addEventListener('change', function () {
            facultyDiv.style.display = this.value === 'faculty_specific' ? 'block' : 'none';
        });
    }

    document.querySelectorAll('.scope-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const target = document.querySelector(this.dataset.target);
            if (target) {
                target.style.display = this.value === 'faculty_specific' ? 'block' : 'none';
            }
        });
    });
});
</script>
@endsection
