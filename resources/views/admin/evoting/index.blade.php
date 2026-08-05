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
                            <span class="badge bg-{{ $session->is_active ? 'success' : 'danger' }}">
                                {{ $session->is_active ? 'ACTIVE ELECTION' : 'INACTIVE / CLOSED' }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
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
                                                                <span class="badge bg-light text-dark border mb-2">{{ $candidate->party_affiliation ?? 'Independent' }}</span>
                                                                <p class="small text-muted mb-0 text-truncate" style="max-height: 40px;">{{ $candidate->manifesto }}</p>
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
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
