@extends('layouts.app')

@section('title', 'E-Voting Portal')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark text-uppercase mb-0">
            <i class="bi bi-check2-square text-primary me-2"></i>STUDENT E-VOTING PORTAL
        </h5>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('student.evoting.index') }}" class="btn btn-primary active">BALLOT / VOTE</a>
            <a href="{{ route('student.evoting.announcements') }}" class="btn btn-outline-secondary">ANNOUNCEMENTS</a>
            <a href="{{ route('student.evoting.positions') }}" class="btn btn-outline-secondary">POSITIONS & CANDIDATES</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$activeSession)
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-box-seam fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold text-dark">NO ACTIVE ELECTION SESSION</h5>
                <p class="text-muted small">Student leader elections take place in Semester 2. Please check back when voting is activated by the election committee.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-light border-start border-primary border-4 rounded-3 p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">{{ $activeSession->title }}</h5>
                        <p class="mb-0 small text-muted">{{ $activeSession->description }}</p>
                    </div>
                    <div>
                        <span class="badge bg-success px-3 py-2 text-uppercase">
                            <i class="bi bi-broadcast me-1"></i>VOTING IS OPEN
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @foreach($activeSession->positions as $position)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-badge text-primary me-2"></i>POSITION: {{ strtoupper($position->title) }}
                    </h6>
                    @if(isset($userVotes[$position->id]))
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                            <i class="bi bi-check-circle me-1"></i>VOTE CAST
                        </span>
                    @else
                        <span class="badge bg-warning text-dark px-2 py-1">BALLOT PENDING</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">{{ $position->description }}</p>

                    <div class="row g-4">
                        @foreach($position->candidates as $candidate)
                            @php
                                $hasVotedForThis = isset($userVotes[$position->id]) && $userVotes[$position->id] == $candidate->id;
                                $alreadyVotedPosition = isset($userVotes[$position->id]);
                            @endphp
                            <div class="col-md-4">
                                <div class="card h-100 border {{ $hasVotedForThis ? 'border-success bg-success bg-opacity-10 shadow-sm' : '' }}">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3">
                                            @if($candidate->photo)
                                                <img src="{{ Storage::url($candidate->photo) }}" class="rounded-circle shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 90px; height: 90px;">
                                                    <i class="bi bi-person fs-1"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <h5 class="fw-bold mb-1">{{ $candidate->name }}</h5>
                                        <span class="badge bg-light text-dark border mb-3">{{ $candidate->party_affiliation ?? 'Independent Candidate' }}</span>

                                        <div class="bg-light p-3 rounded text-start mb-3" style="max-height: 100px; overflow-y: auto;">
                                            <small class="fw-bold d-block mb-1 text-muted">Manifesto:</small>
                                            <small class="text-secondary">{{ $candidate->manifesto ?? 'No manifesto submitted.' }}</small>
                                        </div>

                                        <form action="{{ route('student.evoting.vote') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="voting_session_id" value="{{ $activeSession->id }}">
                                            <input type="hidden" name="voting_position_id" value="{{ $position->id }}">
                                            <input type="hidden" name="voting_candidate_id" value="{{ $candidate->id }}">

                                            @if($hasVotedForThis)
                                                <button type="button" class="btn btn-success w-100 fw-bold" disabled>
                                                    <i class="bi bi-check-lg me-1"></i>VOTED FOR THIS CANDIDATE
                                                </button>
                                            @elseif($alreadyVotedPosition)
                                                <button type="button" class="btn btn-secondary w-100 disabled" disabled>
                                                    VOTE ALREADY CAST
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-outline-primary w-100 fw-bold" onclick="return confirm('Cast your vote for {{ $candidate->name }} as {{ $position->title }}? This cannot be changed.');">
                                                    <i class="bi bi-box-arrow-in-down me-1"></i>CAST VOTE FOR CANDIDATE
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
