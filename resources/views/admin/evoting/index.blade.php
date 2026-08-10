@extends('layouts.app')

@section('title', 'E-Voting Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary"><i class="bi bi-check2-square me-2"></i>E-Voting Management</h1>
            <p class="text-muted mb-0">Manage student leader elections, voting positions, candidates, and live results</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSessionModal">
            <i class="bi bi-plus-lg me-2"></i>Create Voting Session
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($sessions as $session)
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1 text-primary">{{ $session->title }}</h5>
                            <span class="badge bg-secondary me-2">Target: Semester {{ $session->target_semester }}</span>
                            <span class="badge bg-{{ $session->is_active ? 'success' : 'danger' }} me-2">
                                {{ $session->is_active ? 'ACTIVE ELECTION' : 'INACTIVE / CLOSED' }}
                            </span>
                            @if($session->vetting_start_at && $session->vetting_end_at)
                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-clock me-1"></i>Vetting: {{ $session->vetting_start_at->format('M d') }} - {{ $session->vetting_end_at->format('M d') }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#vettingModal{{ $session->id }}">
                                <i class="bi bi-calendar-range me-1"></i>Vetting Window
                            </button>
                            <form action="{{ route('admin.evoting.sessions.toggle', $session) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-{{ $session->is_active ? 'warning' : 'success' }}">
                                    {{ $session->is_active ? 'Close Election' : 'Activate Election' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.evoting.results', $session) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-bar-chart-line me-1"></i>View Tally Results
                            </a>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal{{ $session->id }}">
                                <i class="bi bi-plus-circle me-1"></i>Add Position
                            </button>
                        </div>
                    </div>

                    <!-- Vetting Modal -->
                    <div class="modal fade" id="vettingModal{{ $session->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.evoting.sessions.vetting', $session) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Set Candidacy Vetting Window</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted small mb-3">Define the begin and end period for active students to stand up for candidacy and apply for posts in this election.</p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Vetting Window Start Date & Time</label>
                                            <input type="datetime-local" name="vetting_start_at" class="form-control" value="{{ $session->vetting_start_at ? $session->vetting_start_at->format('Y-m-d\TH:i') : '' }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Vetting Window End Date & Time</label>
                                            <input type="datetime-local" name="vetting_end_at" class="form-control" value="{{ $session->vetting_end_at ? $session->vetting_end_at->format('Y-m-d\TH:i') : '' }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Vetting Window</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">{{ $session->description }}</p>

                        <div class="accordion" id="accordionSession{{ $session->id }}">
                            @forelse($session->positions as $position)
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header" id="headingPos{{ $position->id }}">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePos{{ $position->id }}">
                                            <i class="bi bi-person-badge me-2 text-primary"></i>
                                            {{ $position->title }} ({{ $position->candidates->count() }} Candidates)
                                        </button>
                                    </h2>
                                    <div id="collapsePos{{ $position->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionSession{{ $session->id }}">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="small text-muted">{{ $position->description }}</span>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addCandidateModal{{ $position->id }}">
                                                    <i class="bi bi-person-plus me-1"></i>Add Candidate
                                                </button>
                                            </div>

                                            <div class="row g-3">
                                                @forelse($position->candidates as $candidate)
                                                    <div class="col-md-4">
                                                        <div class="card h-100 border">
                                                            <div class="card-body text-center">
                                                                <div class="avatar-lg mb-2">
                                                                    @if($candidate->photo)
                                                                        <img src="{{ Storage::url($candidate->photo) }}" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;">
                                                                    @else
                                                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                                            <i class="bi bi-person fs-3"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <h6 class="fw-bold mb-1">{{ $candidate->name }}</h6>
                                                                <div class="mb-2">
                                                                    <span class="badge bg-light text-dark border me-1">{{ $candidate->party_affiliation ?? 'Independent' }}</span>
                                                                    @if($candidate->status === 'approved')
                                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                                    @elseif($candidate->status === 'rejected')
                                                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending Vetting</span>
                                                                    @endif
                                                                </div>
                                                                <p class="small text-muted mb-2 text-truncate" style="max-height: 40px;">{{ $candidate->manifesto }}</p>
                                                                
                                                                <div class="d-flex justify-content-center gap-1 mt-2 border-top pt-2">
                                                                    @if($candidate->status !== 'approved')
                                                                        <form action="{{ route('admin.evoting.candidates.vet', $candidate) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="approved">
                                                                            <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2" title="Approve Vetting">
                                                                                <i class="bi bi-check-lg"></i> Approve
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    @if($candidate->status !== 'rejected')
                                                                        <form action="{{ route('admin.evoting.candidates.vet', $candidate) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="rejected">
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Reject Vetting">
                                                                                <i class="bi bi-x-lg"></i> Reject
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-muted small py-2">No candidates added yet for this position.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Candidate Modal -->
                                <div class="modal fade" id="addCandidateModal{{ $position->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.evoting.candidates.store', $position) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add Candidate for {{ $position->title }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Party / Team Affiliation</label>
                                                        <input type="text" name="party_affiliation" class="form-control" placeholder="e.g. Progressive Student Union">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Candidate Photo</label>
                                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Manifesto Summary</label>
                                                        <textarea name="manifesto" class="form-control" rows="3" placeholder="Key promises and manifesto summary..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Save Candidate</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted small py-3">No positions defined for this election session.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Position Modal -->
            <div class="modal fade" id="addPositionModal{{ $session->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.evoting.positions.store', $session) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Add Position to {{ $session->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Position Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Guild President" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Position</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>No voting sessions created yet. Click "Create Voting Session" above.
            </div>
        @endforelse
    </div>
</div>

<!-- Create Session Modal -->
<div class="modal fade" id="createSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.evoting.sessions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create E-Voting Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Election Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Guild Student Leaders Elections 2026/2027" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Semester <span class="text-danger">*</span></label>
                        <select name="target_semester" class="form-select" required>
                            <option value="2" selected>Semester 2 (Student Leader Voting)</option>
                            <option value="1">Semester 1</option>
                        </select>
                        <small class="text-muted">Student leader elections take place in Semester 2 by default.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="card bg-light p-3 border mb-3">
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-clock-history me-1"></i>Candidacy Vetting Period Window</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Vetting Start Date & Time</label>
                                <input type="datetime-local" name="vetting_start_at" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Vetting End Date & Time</label>
                                <input type="datetime-local" name="vetting_end_at" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck" checked>
                        <label class="form-check-label" for="isActiveCheck">Activate election session immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create Session</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
