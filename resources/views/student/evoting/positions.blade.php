@extends('layouts.app')

@section('title', 'E-Voting Positions & Candidates')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark text-uppercase mb-0">
            <i class="bi bi-person-badge text-primary me-2"></i>E-VOTING POSITIONS & CANDIDATES
        </h5>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('student.evoting.index') }}" class="btn btn-outline-secondary">BALLOT / VOTE</a>
            <a href="{{ route('student.evoting.announcements') }}" class="btn btn-outline-secondary">ANNOUNCEMENTS</a>
            <a href="{{ route('student.evoting.positions') }}" class="btn btn-primary active">POSITIONS & CANDIDATES</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @foreach($sessions as $session)
        <!-- Election & Vetting Banner -->
        <div class="card border-0 shadow-sm mb-4 bg-light">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-start border-4 border-primary rounded-3">
                <div>
                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-box-seam me-2"></i>{{ $session->title }}</h6>
                    <span class="small text-muted">
                        <i class="bi bi-calendar-event me-1"></i>Vetting Period: 
                        <strong>
                            {{ $session->vetting_start_at ? $session->vetting_start_at->format('M d, Y h:i A') : 'Open' }} 
                            - 
                            {{ $session->vetting_end_at ? $session->vetting_end_at->format('M d, Y h:i A') : 'Open' }}
                        </strong>
                    </span>
                </div>
                <div>
                    @if($session->is_vetting_open)
                        <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-unlock-fill me-1"></i>VETTING WINDOW OPEN FOR CANDIDACY</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2"><i class="bi bi-lock-fill me-1"></i>VETTING WINDOW CLOSED</span>
                    @endif
                </div>
            </div>
        </div>

        @foreach($session->positions as $position)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-award me-2"></i>{{ strtoupper($position->title) }}</h6>
                        <small class="text-muted">{{ $position->description }}</small>
                    </div>
                    @if($session->is_vetting_open && Auth::user()->isStudent())
                        <button type="button" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#applyModal{{ $position->id }}" style="border-radius: 6px;">
                            <i class="bi bi-pencil-square me-1"></i>STAND FOR CANDIDACY / ENROLL FOR THIS POST
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($position->candidates as $candidate)
                            <div class="col-md-4">
                                <div class="card h-100 border text-center p-3">
                                    <div class="mb-2 position-relative">
                                        @if($candidate->photo)
                                            <img src="{{ Storage::url($candidate->photo) }}" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                <i class="bi bi-person fs-2"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $candidate->name }}</h6>
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark border me-1">{{ $candidate->party_affiliation ?? 'Independent' }}</span>
                                        @if($candidate->status === 'approved')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>VETTED & APPROVED</span>
                                        @elseif($candidate->status === 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>REJECTED</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>PENDING VETTING</span>
                                        @endif
                                    </div>
                                    <p class="small text-muted mb-0">{{ $candidate->manifesto }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-3">
                                <i class="bi bi-person-x me-2"></i>No approved candidates or applicants registered for this post yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Apply for Candidacy Modal -->
            @if($session->is_vetting_open && Auth::user()->isStudent())
                <div class="modal fade" id="applyModal{{ $position->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('student.evoting.candidacy.apply') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="voting_position_id" value="{{ $position->id }}">

                                <div class="modal-header bg-primary text-white py-3">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-pen me-2"></i>Apply for Candidacy: {{ $position->title }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="alert alert-info py-2 px-3 small mb-3">
                                        <i class="bi bi-info-circle-fill me-1"></i>
                                        Vetting is currently OPEN. Submitting this form registers your candidacy application for official vetting by the election committee.
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase">Applicant Name</label>
                                        <input type="text" class="form-control" value="{{ Auth::user()->full_name }}" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase">Party / Team Affiliation</label>
                                        <input type="text" name="party_affiliation" class="form-control" placeholder="e.g. Independent or Alliance Party">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase">Campaign Photo</label>
                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-uppercase">Manifesto / Campaign Promises <span class="text-danger">*</span></label>
                                        <textarea name="manifesto" class="form-control" rows="4" placeholder="Briefly describe your key campaign promises and goals for this position..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light py-2">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary fw-bold">
                                        <i class="bi bi-send-check me-1"></i>SUBMIT CANDIDACY APPLICATION
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
</div>
@endsection
