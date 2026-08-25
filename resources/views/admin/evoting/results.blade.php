@extends('layouts.app')

@section('title', 'Official Election Tally & Results: ' . $session->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header & Print Toolbar -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 d-print-none">
        <div>
            <h1 class="h3 mb-1 text-primary fw-bold">
                <i class="bi bi-bar-chart-line me-2"></i>Official Election Tally & Results
            </h1>
            <p class="text-muted mb-0">{{ $session->title }} &mdash; Academic Year: {{ $session->academicYear->name ?? 'Current' }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark fw-bold">
                <i class="bi bi-printer me-1"></i>Print Official Report
            </button>
            <a href="{{ route('admin.evoting.show', $session) }}" class="btn btn-primary fw-bold">
                <i class="bi bi-arrow-left me-1"></i>Manage Election
            </a>
        </div>
    </div>

    <!-- Official Header Banner (Prints beautifully) -->
    <div class="card border-0 shadow-sm mb-4 bg-white">
        <div class="card-body p-4 text-center border-bottom border-primary border-3">
            <h3 class="fw-bold text-dark mb-1">JBI UNIVERSITY</h3>
            <h5 class="text-primary fw-bold mb-2">OFFICIAL ELECTORAL COMMISSION RESULTS DECLARATION</h5>
            <h6 class="text-dark fw-bold mb-3">{{ strtoupper($session->title) }}</h6>
            <div class="d-flex justify-content-center gap-3 flex-wrap small text-muted">
                <span><strong>Status:</strong> <span class="badge bg-{{ $session->status_badge_color }} text-uppercase">{{ $session->status_label }}</span></span>
                <span><strong>Published Date:</strong> {{ $session->results_published_at ? $session->results_published_at->format('F d, Y H:i') : now()->format('F d, Y') }}</span>
                <span><strong>Total Votes Cast:</strong> {{ number_format($session->votes->count()) }}</span>
                <span><strong>Turnout:</strong> {{ $totalEligibleStudents > 0 ? round(($totalVotersCount / $totalEligibleStudents) * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    <!-- Positions Results Loop -->
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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-award me-2 text-primary"></i>{{ strtoupper($position->title) }}
                    </h5>
                    <span class="small text-muted">
                        {{ $position->scope === 'university_wide' ? 'University-Wide' : ('Faculty: ' . ($position->faculty->name ?? 'Faculty')) }}
                    </span>
                </div>
                <span class="badge bg-primary px-3 py-2 fs-6">
                    Total Valid Votes: {{ number_format($totalVotesPosition) }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @forelse($candidates as $candidate)
                        @php
                            $voteCount = $candidate->votes_count;
                            $percentage = $totalVotesPosition > 0 ? round(($voteCount / $totalVotesPosition) * 100, 1) : 0;
                            $isWinner = ($winner && $winner->id === $candidate->id && $candidate->votes_count > 0);
                        @endphp
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 {{ $isWinner ? 'bg-light border-success shadow-sm' : 'bg-white' }} position-relative">
                                @if($isWinner)
                                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 fw-bold">
                                        <i class="bi bi-trophy-fill me-1"></i>WINNER & ELECTED LEADER
                                    </span>
                                @endif

                                <div class="d-flex align-items-center gap-3 mb-3">
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" class="rounded-circle object-fit-cover shadow-sm border border-2 {{ $isWinner ? 'border-success' : 'border-light' }}" style="width: 60px; height: 60px;" alt="{{ $candidate->name }}">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 60px; height: 60px; font-size: 20px;">
                                            {{ strtoupper(substr($candidate->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="fw-bold mb-0 text-dark">{{ $candidate->name }}</h5>
                                        <div class="small text-muted">{{ $candidate->slogan ?: ($candidate->party_affiliation ?: 'Independent') }}</div>
                                        <div class="small text-muted">{{ $candidate->faculty->name ?? '' }} &bull; Year {{ $candidate->year_of_study }}</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-muted">Vote Count:</span>
                                    <div>
                                        <span class="fw-bold fs-5 text-primary">{{ number_format($voteCount) }}</span>
                                        <span class="text-muted fw-semibold">({{ $percentage }}%)</span>
                                    </div>
                                </div>

                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar {{ $isWinner ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
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

    <!-- Returning Officer Sign-Off -->
    <div class="card border-0 shadow-sm mt-4 bg-white">
        <div class="card-body p-4">
            <div class="row pt-3 text-center">
                <div class="col-md-4 mb-3">
                    <div class="border-bottom pb-4 mb-2"></div>
                    <strong class="d-block text-dark">Electoral Commission Chairperson</strong>
                    <small class="text-muted">Signature & Official Stamp</small>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border-bottom pb-4 mb-2"></div>
                    <strong class="d-block text-dark">Chief Returning Officer</strong>
                    <small class="text-muted">Signature & Verification</small>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border-bottom pb-4 mb-2"></div>
                    <strong class="d-block text-dark">Dean of Student Affairs / Registrar</strong>
                    <small class="text-muted">Institutional Endorsement</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
