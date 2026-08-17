@extends('layouts.app')

@section('title', 'Election Tally Results')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary"><i class="bi bi-bar-chart-line me-2"></i>Election Tally Results</h1>
            <p class="text-muted mb-0">{{ $session->title }}</p>
        </div>
        <a href="{{ route('admin.evoting.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to E-Voting Management
        </a>
    </div>

    @foreach($session->positions as $position)
        @php
            $totalVotesPosition = $position->candidates->sum(fn($c) => $c->votes->count());
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-award me-2 text-primary"></i>POSITION: {{ strtoupper($position->title) }}</h5>
                <span class="badge bg-primary px-3 py-2 fs-6">TOTAL VOTES CAST: {{ $totalVotesPosition }}</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($position->candidates as $candidate)
                        @php
                            $voteCount = $candidate->votes->count();
                            $percentage = $totalVotesPosition > 0 ? round(($voteCount / $totalVotesPosition) * 100, 1) : 0;
                        @endphp
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($candidate->photo)
                                            <img src="{{ Storage::url($candidate->photo) }}" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px;">
                                                {{ substr($candidate->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ $candidate->name }}</h6>
                                            <small class="text-muted">{{ $candidate->party_affiliation ?? 'Independent' }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold fs-4 text-primary">{{ $voteCount }}</span>
                                        <small class="text-muted d-block">{{ $percentage }}%</small>
                                    </div>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
