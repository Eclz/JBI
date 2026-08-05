@extends('layouts.app')

@section('title', 'E-Voting Announcements')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark text-uppercase mb-0">
            <i class="bi bi-megaphone text-primary me-2"></i>E-VOTING ANNOUNCEMENTS
        </h5>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('student.evoting.index') }}" class="btn btn-outline-secondary">BALLOT / VOTE</a>
            <a href="{{ route('student.evoting.announcements') }}" class="btn btn-primary active">ANNOUNCEMENTS</a>
            <a href="{{ route('student.evoting.positions') }}" class="btn btn-outline-secondary">POSITIONS & CANDIDATES</a>
        </div>
    </div>

    <div class="row">
        @forelse($sessions as $session)
            <div class="col-lg-12 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-primary mb-0">{{ $session->title }}</h5>
                            <span class="badge bg-success">ACTIVE ELECTION</span>
                        </div>
                        <p class="text-muted mb-2">{{ $session->description }}</p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Published for Semester {{ $session->target_semester }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No active e-voting announcements found.
            </div>
        @endforelse
    </div>
</div>
@endsection
