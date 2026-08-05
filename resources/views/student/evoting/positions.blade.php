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

    @foreach($sessions as $session)
        @foreach($session->positions as $position)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-award me-2"></i>{{ strtoupper($position->title) }}</h6>
                    <small class="text-muted">{{ $position->description }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($position->candidates as $candidate)
                            <div class="col-md-4">
                                <div class="card h-100 border text-center p-3">
                                    <div class="mb-2">
                                        @if($candidate->photo)
                                            <img src="{{ Storage::url($candidate->photo) }}" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                <i class="bi bi-person fs-2"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $candidate->name }}</h6>
                                    <span class="badge bg-light text-dark border mb-2">{{ $candidate->party_affiliation ?? 'Independent' }}</span>
                                    <p class="small text-muted mb-0">{{ $candidate->manifesto }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
</div>
@endsection
