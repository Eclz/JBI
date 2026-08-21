@extends('layouts.app')

@section('title', 'Student Leadership Cabinet')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary fw-bold">
                <i class="bi bi-award me-2"></i>Student Leadership Cabinet
            </h1>
            <p class="text-muted mb-0">Meet your elected student leaders and faculty representatives</p>
        </div>
        <a href="{{ route('student.evoting.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to E-Voting
        </a>
    </div>

    <!-- Leaders Grid -->
    <div class="row g-4">
        @forelse($leaders as $leader)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 text-center overflow-hidden">
                    <div class="bg-primary py-4 px-3 text-white position-relative" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;">
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 fw-bold">
                            <i class="bi bi-trophy-fill me-1"></i>Elected
                        </span>
                        @if($leader->photo)
                            <img src="{{ asset('storage/' . $leader->photo) }}" class="rounded-circle border border-3 border-white shadow mx-auto mb-2 object-fit-cover" width="80" height="80" alt="{{ $leader->name }}">
                        @else
                            <div class="rounded-circle border border-3 border-white bg-white text-primary fw-bold mx-auto mb-2 d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px; font-size: 24px;">
                                {{ strtoupper(substr($leader->name, 0, 2)) }}
                            </div>
                        @endif
                        <h5 class="fw-bold mb-0 text-white">{{ $leader->name }}</h5>
                        <div class="text-white-50 small">{{ $leader->slogan ?: ($leader->party_affiliation ?: 'Student Leader') }}</div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <div class="badge bg-light text-primary border mb-2 fs-6">
                                {{ $leader->position->title ?? 'Leadership Portfolio' }}
                            </div>
                            <div class="small text-muted mb-2">
                                @if($leader->position?->scope === 'university_wide')
                                    <span class="badge bg-primary text-white">University-Wide Cabinet</span>
                                @else
                                    <span class="badge bg-info text-dark">Faculty: {{ $leader->position?->faculty?->name ?? 'Faculty' }}</span>
                                @endif
                            </div>
                            <div class="small text-muted mb-3">
                                <div><strong>Tenure:</strong> {{ $leader->position?->session?->academicYear?->name ?? 'Academic Year' }}</div>
                            </div>
                            @if($leader->manifesto)
                                <p class="text-muted small mb-0 fst-italic">"{{ Str::limit($leader->manifesto, 80) }}"</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm py-5 text-center bg-white rounded-3">
                    <div class="card-body">
                        <i class="bi bi-award display-3 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold text-dark mb-1">No Elected Leaders Published Yet</h4>
                        <p class="text-muted mb-3">When elections conclude and official results are published, elected student leaders will appear here.</p>
                        <a href="{{ route('student.evoting.index') }}" class="btn btn-primary fw-bold">
                            <i class="bi bi-check2-square me-1"></i>View Open Elections
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
