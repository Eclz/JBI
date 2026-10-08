@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('faculty.dean.partnerships.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> Back to Partnerships
            </a>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">{{ $partnership->organization_name }}</h1>
            <p class="text-muted">Partnership Type: {{ $partnership->partnership_type }}</p>
        </div>
        <div>
            <a href="{{ route('faculty.dean.partnerships.edit', $partnership) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit Details
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">Strategic Objectives & Shared Outcomes</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $partnership->objectives }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold text-dark">Partnership Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Status</span>
                        @if($partnership->status === 'Pending')
                            <span class="badge bg-warning text-dark fs-6">Pending</span>
                        @elseif($partnership->status === 'Active')
                            <span class="badge bg-success fs-6">Active</span>
                        @elseif($partnership->status === 'Concluded')
                            <span class="badge bg-secondary fs-6">Concluded</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Financial Commitment</span>
                        @if($partnership->funding_amount > 0)
                            <h3 class="mb-0 font-monospace text-primary">₵{{ number_format($partnership->funding_amount, 2) }}</h3>
                        @else
                            <h5 class="mb-0 text-muted fst-italic">Non-Financial</h5>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Managed By</span>
                        <span class="fw-medium">{{ $partnership->manager->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Date Logged</span>
                        <span class="fw-medium">{{ $partnership->created_at->format('F d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-muted d-block small mb-1">Last Updated</span>
                        <span class="fw-medium">{{ $partnership->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
