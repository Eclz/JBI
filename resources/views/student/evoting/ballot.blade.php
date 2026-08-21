@extends('layouts.app')

@section('title', 'Official Electronic Ballot: ' . $session->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('student.evoting.index') }}">E-Voting</a></li>
                <li class="breadcrumb-item active" aria-current="page">Official Ballot</li>
            </ol>
        </nav>
        <a href="{{ route('student.evoting.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <!-- Ballot Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #065f46, #10b981) !important;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2 text-uppercase">
                        <i class="bi bi-shield-lock-fill me-1"></i>Secure Digital Ballot
                    </span>
                    <h2 class="h3 fw-bold mb-1">{{ $session->title }}</h2>
                    <p class="text-white-50 mb-0 small">
                        Voter: <strong>{{ Auth::user()->name }}</strong> &bull; 
                        Student ID: <strong>{{ Auth::user()->student_id ?? Auth::user()->studentProfile?->admission_number }}</strong> &bull; 
                        Faculty: <strong>{{ $studentProfile->department?->faculty?->name ?? 'General' }}</strong>
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-success fs-6 py-2 px-3 fw-bold shadow-sm">
                        <i class="bi bi-broadcast me-1"></i>Voting Session Active
                    </span>
                    <div class="text-white-50 small mt-1">Closes: {{ $session->end_time ? $session->end_time->format('M d, H:i') : 'TBD' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Alert -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div class="small">
            <strong>Voting Guidelines:</strong> You may cast <strong>one vote per electoral position</strong>. Select your preferred vetted candidate and click <strong>"Confirm & Cast Vote"</strong>. Once submitted, your vote is cryptographically recorded and cannot be altered or cast again.
        </div>
    </div>

    <!-- Positions & Ballot Sections -->
    @forelse($positions as $position)
        @php
            $hasVoted = isset($myVotes[$position->id]);
            $votedCandidateId = $myVotes[$position->id] ?? null;
            $approvedCandidates = $position->approvedCandidates;
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-card" id="positionCard{{ $position->id }}">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-award-fill me-2 text-primary"></i>{{ $position->title }}
                    </h5>
                    <span class="small text-muted">
                        @if($position->scope === 'university_wide')
                            <span class="badge bg-primary text-white">University-Wide Position</span>
                        @else
                            <span class="badge bg-info text-dark">Faculty of {{ $position->faculty->name ?? 'Your Faculty' }}</span>
                        @endif
                    </span>
                </div>

                <div>
                    @if($hasVoted)
                        <span class="badge bg-success py-2 px-3 fs-6 fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i>Vote Cast & Confirmed
                        </span>
                    @else
                        <span class="badge bg-warning text-dark py-2 px-3 fw-bold">
                            <i class="bi bi-hourglass-split me-1"></i>Awaiting Your Vote
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4 bg-light">
                <div class="row g-4">
                    @forelse($approvedCandidates as $candidate)
                        @php
                            $isSelected = ($hasVoted && $votedCandidateId == $candidate->id);
                        @endphp
                        <div class="col-md-6 col-xl-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100 candidate-card p-3 {{ $isSelected ? 'border border-2 border-success bg-success-subtle' : 'bg-white' }}" id="candCard{{ $candidate->id }}">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" class="rounded-circle object-fit-cover shadow-sm border border-2 border-primary" width="65" height="65" alt="{{ $candidate->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 65px; height: 65px; font-size: 22px;">
                                            {{ strtoupper(substr($candidate->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">{{ $candidate->name }}</h5>
                                        <div class="text-muted small">{{ $candidate->slogan ?: ($candidate->party_affiliation ?: 'Independent') }}</div>
                                        <div class="text-muted small">Year {{ $candidate->year_of_study }} &bull; {{ $candidate->faculty->name ?? 'General' }}</div>
                                    </div>
                                </div>

                                @if($candidate->manifesto)
                                    <p class="text-muted small mb-3 fst-italic">"{{ Str::limit($candidate->manifesto, 90) }}"</p>
                                @endif

                                <div class="d-flex gap-2 mt-auto">
                                    <button class="btn btn-sm btn-outline-secondary w-50" data-bs-toggle="modal" data-bs-target="#manifestoModal{{ $candidate->id }}">
                                        <i class="bi bi-file-earmark-text me-1"></i>Manifesto
                                    </button>

                                    @if(!$hasVoted)
                                        <button type="button" class="btn btn-sm btn-success w-50 fw-bold vote-btn" 
                                                data-session-id="{{ $session->id }}" 
                                                data-position-id="{{ $position->id }}" 
                                                data-candidate-id="{{ $candidate->id }}"
                                                data-candidate-name="{{ $candidate->name }}"
                                                data-position-title="{{ $position->title }}">
                                            <i class="bi bi-check2-circle me-1"></i>Vote
                                        </button>
                                    @elseif($isSelected)
                                        <button class="btn btn-sm btn-success w-50 fw-bold" disabled>
                                            <i class="bi bi-check2-all me-1"></i>Your Choice
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-light w-50 text-muted" disabled>
                                            Not Chosen
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Manifesto Modal -->
                        <div class="modal fade" id="manifestoModal{{ $candidate->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-primary text-white py-3">
                                        <h5 class="modal-title fw-bold">{{ $candidate->name }} &mdash; Manifesto</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            @if($candidate->photo)
                                                <img src="{{ asset('storage/' . $candidate->photo) }}" class="rounded-circle" width="55" height="55">
                                            @endif
                                            <div>
                                                <h5 class="fw-bold mb-0">{{ $candidate->name }}</h5>
                                                <div class="text-primary small fw-semibold">{{ $candidate->position->title }}</div>
                                            </div>
                                        </div>
                                        @if($candidate->slogan)
                                            <div class="p-2 bg-light rounded mb-3 small fw-bold text-center text-dark">
                                                "{{ $candidate->slogan }}"
                                            </div>
                                        @endif
                                        <h6 class="fw-bold text-dark small mb-1">Vision & Commitments:</h6>
                                        <p class="small text-muted mb-0 whitespace-pre-line">{{ $candidate->manifesto }}</p>
                                    </div>
                                    <div class="modal-footer bg-light py-2">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted small mb-0 text-center py-3">No approved candidates available for this position on the ballot.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm py-5 text-center bg-white rounded-3">
            <div class="card-body">
                <i class="bi bi-inbox display-3 text-muted mb-3 d-block"></i>
                <h4 class="fw-bold text-dark mb-1">No Positions on Ballot</h4>
                <p class="text-muted mb-0">No eligible electoral positions were found for your faculty or university-wide ballot.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Vote Confirmation Modal -->
<div class="modal fade" id="confirmVoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-check me-2"></i>Confirm Your Vote
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted small mb-2">You are about to cast your vote for:</p>
                <h3 class="fw-bold text-dark mb-1" id="modalCandidateName">Candidate Name</h3>
                <div class="badge bg-primary fs-6 px-3 py-2 mb-3" id="modalPositionTitle">Position Title</div>

                <div class="alert alert-warning border-0 small text-start mb-0">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>
                    Once confirmed, your choice is final and cannot be modified.
                </div>
            </div>
            <div class="modal-footer bg-light py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold px-4" id="submitVoteBtn">
                    <i class="bi bi-check-circle-fill me-1"></i>Confirm & Cast Vote
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let pendingVote = null;
    const confirmModalEl = document.getElementById('confirmVoteModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl);

    document.querySelectorAll('.vote-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            pendingVote = {
                sessionId: this.dataset.sessionId,
                positionId: this.dataset.positionId,
                candidateId: this.dataset.candidateId,
                candidateName: this.dataset.candidateName,
                positionTitle: this.dataset.positionTitle,
                buttonEl: this
            };

            document.getElementById('modalCandidateName').innerText = pendingVote.candidateName;
            document.getElementById('modalPositionTitle').innerText = pendingVote.positionTitle;
            confirmModal.show();
        });
    });

    document.getElementById('submitVoteBtn').addEventListener('click', function () {
        if (!pendingVote) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Submitting...';

        fetch('{{ route("student.evoting.vote", $session) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                voting_position_id: pendingVote.positionId,
                voting_candidate_id: pendingVote.candidateId
            })
        })
        .then(response => response.json())
        .then(data => {
            confirmModal.hide();
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Confirm & Cast Vote';

            if (data.success) {
                // Success alert
                alert(data.message + '\nVerification Token: ' + data.token);
                window.location.reload();
            } else {
                alert(data.message || 'Failed to record vote. Please try again.');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Confirm & Cast Vote';
            alert('An error occurred while submitting your vote. Please refresh and try again.');
        });
    });
});
</script>
@endsection
