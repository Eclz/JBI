@extends('layouts.app')

@section('title', 'E-Voting & Student Leadership')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Hero Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 mb-2 text-uppercase">
                        <i class="bi bi-shield-check me-1"></i>Official Democratic Process
                    </span>
                    <h1 class="display-6 fw-bold mb-2">Student Leadership & E-Voting Portal</h1>
                    <p class="lead mb-3 text-white-50">Exercise your democratic right: vote for your student representatives, apply for leadership portfolios, and follow official election results.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('student.evoting.my-applications') }}" class="btn btn-light fw-bold text-primary shadow-sm">
                            <i class="bi bi-file-earmark-person me-1"></i>My Candidacy Applications ({{ $myApplications->count() }})
                        </a>
                        <a href="{{ route('student.evoting.leaders') }}" class="btn btn-outline-light fw-bold">
                            <i class="bi bi-award me-1"></i>Current Student Leaders
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end d-none d-lg-block">
                    <i class="bi bi-box-seam-fill display-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Election Seasons Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <h4 class="fw-bold text-dark mb-3"><i class="bi bi-check2-square me-2 text-primary"></i>Elections & Voting Sessions</h4>

            @forelse($sessions as $session)
                <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="badge bg-{{ $session->status_badge_color }} py-1 px-3 fw-bold text-uppercase me-2">
                                {{ $session->status_label }}
                            </span>
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>{{ $session->academicYear->name ?? 'Academic Year' }} (Semester {{ $session->target_semester }})
                            </span>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $session->positions->count() }} Contested Positions</span>
                    </div>

                    <div class="card-body p-4">
                        <h4 class="fw-bold text-dark mb-2">{{ $session->title }}</h4>
                        <p class="text-muted small mb-3">{{ $session->description ?: 'Participate in this election by applying for candidacy or casting your ballot during the active voting window.' }}</p>

                        <!-- Key Timelines -->
                        <div class="row g-2 mb-3 small">
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border">
                                    <span class="text-muted d-block">Application Window:</span>
                                    <strong class="text-dark">
                                        {{ $session->application_start_at ? $session->application_start_at->format('M d') : 'TBD' }} - 
                                        {{ $session->application_end_at ? $session->application_end_at->format('M d, Y') : 'TBD' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded bg-light border">
                                    <span class="text-muted d-block">Voting Session Window:</span>
                                    <strong class="{{ $session->is_voting_open ? 'text-success' : 'text-dark' }}">
                                        {{ $session->start_time ? $session->start_time->format('M d, H:i') : 'TBD' }} - 
                                        {{ $session->end_time ? $session->end_time->format('M d, H:i') : 'TBD' }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons based on status -->
                        <div class="d-flex gap-2 flex-wrap pt-2">
                            @if($session->is_voting_open)
                                <a href="{{ route('student.evoting.ballot', $session) }}" class="btn btn-success fw-bold px-4 shadow-sm animate-pulse">
                                    <i class="bi bi-check-circle me-1"></i>Cast Your Vote Now (Ballot Open)
                                </a>
                            @endif

                            @if($session->is_application_open || $session->status === 'applications_open')
                                <a href="{{ route('student.evoting.apply', $session) }}" class="btn btn-primary fw-bold px-3">
                                    <i class="bi bi-send me-1"></i>Apply for Candidacy
                                </a>
                            @endif

                            @if($session->is_results_published)
                                <a href="{{ route('student.evoting.results', $session) }}" class="btn btn-outline-info fw-bold">
                                    <i class="bi bi-bar-chart-line me-1"></i>View Official Results
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm py-5 text-center bg-white rounded-3">
                    <div class="card-body">
                        <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark mb-1">No Active Election Seasons</h5>
                        <p class="text-muted mb-0">There are currently no active or upcoming student elections scheduled by the Electoral Commission.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="col-lg-4">
            <!-- My Applications Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-person me-2 text-primary"></i>My Applications</h5>
                    <a href="{{ route('student.evoting.my-applications') }}" class="small fw-semibold">View All</a>
                </div>
                <div class="card-body p-3">
                    @forelse($myApplications->take(3) as $app)
                        <div class="p-2 border rounded mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="small text-dark">{{ $app->position->title ?? 'Position' }}</strong>
                                <span class="badge bg-{{ $app->application_status_badge }}" style="font-size: 10px;">
                                    {{ ucfirst(str_replace('_', ' ', $app->application_status)) }}
                                </span>
                            </div>
                            <div class="small text-muted">{{ $app->position->session->title ?? 'Election' }}</div>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-3">You have not submitted any candidacy applications yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Leadership Cabinet Showcase -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-award me-2 text-primary"></i>Elected Student Leaders</h5>
                    <a href="{{ route('student.evoting.leaders') }}" class="small fw-semibold">Directory</a>
                </div>
                <div class="card-body p-3">
                    @forelse($electedLeaders->take(4) as $leader)
                        <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded bg-light">
                            @if($leader->photo)
                                <img src="{{ asset('storage/' . $leader->photo) }}" class="rounded-circle object-fit-cover shadow-sm" width="45" height="45" alt="{{ $leader->name }}">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px;">
                                    {{ strtoupper(substr($leader->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $leader->name }}</h6>
                                <span class="badge bg-light text-primary border" style="font-size: 11px;">{{ $leader->position->title ?? 'Leader' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-3">No student leaders declared yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
