@extends('layouts.app')

@section('title', 'My Candidacy Applications')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary fw-bold">
                <i class="bi bi-file-earmark-person me-2"></i>My Candidacy Applications
            </h1>
            <p class="text-muted mb-0">Track your application status through the Electoral Commission vetting process</p>
        </div>
        <a href="{{ route('student.evoting.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to E-Voting
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($applications as $app)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $app->application_status_badge }} py-1 px-3 fw-bold text-uppercase">
                            {{ ucfirst(str_replace('_', ' ', $app->application_status)) }}
                        </span>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Applied: {{ $app->created_at->format('M d, Y') }}</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($app->photo)
                                <img src="{{ asset('storage/' . $app->photo) }}" class="rounded-circle object-fit-cover shadow-sm border border-2 border-primary" width="60" height="60" alt="{{ $app->name }}">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 60px; height: 60px; font-size: 20px;">
                                    {{ strtoupper(substr($app->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $app->position->title ?? 'Position' }}</h5>
                                <div class="text-muted small">{{ $app->position->session->title ?? 'Election Season' }}</div>
                                <span class="badge bg-light text-dark border mt-1">
                                    {{ $app->position?->scope === 'university_wide' ? 'University-Wide Position' : ('Faculty of ' . ($app->position?->faculty?->name ?? 'Faculty')) }}
                                </span>
                            </div>
                        </div>

                        <!-- Stepper Progress -->
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-diagram-3 me-1 text-primary"></i>Vetting Progression</h6>
                            <div class="d-flex justify-content-between small text-center">
                                <div class="flex-fill">
                                    <i class="bi bi-check-circle-fill text-success fs-5 d-block"></i>
                                    <span class="fw-semibold">1. Submitted</span>
                                </div>
                                <div class="flex-fill">
                                    <i class="bi bi-{{ in_array($app->application_status, ['under_review', 'vetted_approved', 'rejected']) ? 'check-circle-fill text-success' : 'circle text-muted' }} fs-5 d-block"></i>
                                    <span class="fw-semibold">2. Vetting</span>
                                </div>
                                <div class="flex-fill">
                                    <i class="bi bi-{{ $app->application_status === 'vetted_approved' ? 'check-circle-fill text-success' : ($app->application_status === 'rejected' ? 'x-circle-fill text-danger' : 'circle text-muted') }} fs-5 d-block"></i>
                                    <span class="fw-semibold">3. {{ $app->application_status === 'rejected' ? 'Rejected' : 'On Ballot' }}</span>
                                </div>
                                <div class="flex-fill">
                                    <i class="bi bi-{{ $app->candidate_status === 'elected_student_leader' ? 'trophy-fill text-warning' : 'circle text-muted' }} fs-5 d-block"></i>
                                    <span class="fw-semibold">4. Result</span>
                                </div>
                            </div>
                        </div>

                        @if($app->slogan)
                            <p class="small text-muted mb-2"><strong>Campaign Slogan:</strong> <em>"{{ $app->slogan }}"</em></p>
                        @endif

                        @if($app->vetting_notes)
                            <div class="p-3 rounded-3 mb-2 {{ $app->application_status === 'vetted_approved' ? 'bg-success-subtle text-success' : ($app->application_status === 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-dark') }}">
                                <strong class="small d-block mb-1"><i class="bi bi-chat-left-quote me-1"></i>Electoral Commission Feedback:</strong>
                                <span class="small">{{ $app->vetting_notes }}</span>
                            </div>
                        @endif

                        @if($app->vetting_score !== null)
                            <div class="small text-muted mt-2">
                                <strong>Vetting Score:</strong> <span class="badge bg-success">{{ $app->vetting_score }}/100</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm py-5 text-center bg-white rounded-3">
                    <div class="card-body">
                        <i class="bi bi-folder2-open display-3 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold text-dark mb-1">No Applications Submitted</h4>
                        <p class="text-muted mb-3">You have not submitted any candidacy applications for student leadership yet.</p>
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
