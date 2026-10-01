@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('faculty.dean.budgets.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> Back to Budget Requests
            </a>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">{{ $budget->title }}</h1>
            <p class="text-muted">Budget Request for {{ $budget->department->name ?? 'Unknown Department' }}</p>
        </div>
        <div>
            @if($budget->status === 'Draft' || $budget->status === 'Rejected')
                <a href="{{ route('faculty.dean.budgets.edit', $budget) }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-pencil me-1"></i> Edit Request
                </a>
                <form action="{{ route('faculty.dean.budgets.submit', $budget) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Submit this request to the Finance Department? It will be locked from further edits.')">
                        <i class="bi bi-send me-1"></i> Submit to Finance
                    </button>
                </form>
            @endif
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
                    <h5 class="mb-0 fw-semibold text-dark">Justification & Details</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $budget->description }}</p>
                </div>
            </div>
            
            @if($budget->finance_notes)
            <div class="card border-0 shadow-sm border-start border-4 border-{{ $budget->status === 'Rejected' ? 'danger' : 'success' }}">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">Finance Office Notes</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-line;">{{ $budget->finance_notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold text-dark">Financial Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Status</span>
                        @if($budget->status === 'Draft')
                            <span class="badge bg-secondary fs-6">Draft</span>
                        @elseif($budget->status === 'Pending Finance Approval')
                            <span class="badge bg-warning text-dark fs-6">Pending Approval</span>
                        @elseif($budget->status === 'Approved')
                            <span class="badge bg-success fs-6">Approved</span>
                        @elseif($budget->status === 'Rejected')
                            <span class="badge bg-danger fs-6">Rejected</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Requested Amount</span>
                        <h3 class="mb-0 font-monospace text-primary">₵{{ number_format($budget->amount, 2) }}</h3>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Created By</span>
                        <span class="fw-medium">{{ $budget->requester->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Date Created</span>
                        <span class="fw-medium">{{ $budget->created_at->format('F d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-muted d-block small mb-1">Last Modified</span>
                        <span class="fw-medium">{{ $budget->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
