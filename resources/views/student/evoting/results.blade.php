@extends('layouts.app')

@section('title', 'Election Results: ' . $session->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('student.evoting.index') }}">E-Voting</a></li>
                <li class="breadcrumb-item active" aria-current="page">Official Results</li>
            </ol>
        </nav>
        <a href="{{ route('student.evoting.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <!-- Official Header Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;">
        <div class="card-body p-4 p-md-5 text-center">
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2 text-uppercase">Official Declaration</span>
            <h2 class="display-6 fw-bold mb-1">{{ $session->title }}</h2>
            <p class="text-white-50 mb-0">Certified results published by the University Electoral Commission &bull; Academic Year: {{ $session->academicYear->name ?? 'Current' }}</p>
        </div>
    </div>

    <!-- Positions Results -->
    @foreach($session->positions as $position)
        @php
            $totalVotesPosition = $position->votes->count();
            $candidates = $position->candidates()
                ->whereIn('candidate_status', ['approved_candidate', 'elected_student_leader'])
                ->withCount('votes')
                ->orderByDesc('votes_count')
                ->get();
            $winner = $candidates->first();
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-award-fill me-2 text-primary"></i>{{ $position->title }}
                    </h5>
                    <span class="small text-muted">
                        {{ $position->scope === 'university_wide' ? 'University-Wide' : ('Faculty of ' . ($position->faculty->name ?? 'Faculty')) }}
                    </span>
                </div>
                <span class="badge bg-primary px-3 py-2 fs-6">
                    {{ number_format($totalVotesPosition) }} Votes Recorded
                </span>
            </div>

            <div class="card-body p-4 bg-light">
                <div class="row g-4">
                    @forelse($candidates as $candidate)
                        @php
                            $voteCount = $candidate->votes_count;
                            $percentage = $totalVotesPosition > 0 ? round(($voteCount / $totalVotesPosition) * 100, 1) : 0;
                            $isWinner = ($winner && $winner->id === $candidate->id && $candidate->votes_count > 0);
                        @endphp
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 {{ $isWinner ? 'bg-white border-success border-2 shadow-sm' : 'bg-white' }} position-relative">
                                @if($isWinner)
                                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 fw-bold">
                                        <i class="bi bi-trophy-fill me-1"></i>ELECTED STUDENT LEADER
                                    </span>
                                @endif

                                <div class="d-flex align-items-center gap-3 mb-3">
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" class="rounded-circle object-fit-cover shadow-sm border border-2 {{ $isWinner ? 'border-success' : 'border-light' }}" width="60" height="60" alt="{{ $candidate->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 60px; height: 60px; font-size: 20px;">
                                            {{ strtoupper(substr($candidate->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">{{ $candidate->name }}</h5>
                                        <div class="small text-muted">{{ $candidate->slogan ?: ($candidate->party_affiliation ?: 'Independent') }}</div>
                                        <div class="small text-muted">{{ $candidate->faculty->name ?? '' }} &bull; Year {{ $candidate->year_of_study }}</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-muted">Final Vote Tally:</span>
                                    <div>
                                        <span class="fw-bold fs-5 text-primary">{{ number_format($voteCount) }}</span>
                                        <span class="text-muted fw-semibold">({{ $percentage }}%)</span>
                                    </div>
                                </div>

                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar {{ $isWinner ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-3 text-muted">
                            No approved candidates recorded for this position.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
