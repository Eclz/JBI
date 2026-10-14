@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('faculty.dean.student-issues.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> Back to Student Issues
            </a>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Issue Details</h1>
            <p class="text-muted">Reported on {{ $studentIssue->created_at->format('M d, Y') }}</p>
        </div>
        <div>
            <a href="{{ route('faculty.dean.student-issues.edit', $studentIssue) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Update Issue
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
                    <h5 class="mb-0 fw-semibold text-dark">Issue Description</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $studentIssue->description }}</p>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">Action Taken & Resolution Log</h5>
                </div>
                <div class="card-body p-4">
                    @if($studentIssue->action_taken)
                        <p class="mb-0" style="white-space: pre-line; line-height: 1.6;">{{ $studentIssue->action_taken }}</p>
                    @else
                        <div class="text-muted fst-italic">No action log provided yet.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold text-dark">Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $studentIssue->student->profile_picture_url ?? 'https://ui-avatars.com/api/?name=User&color=1e3a8a&background=e0e7ff' }}" alt="" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <div class="fw-medium text-dark">{{ $studentIssue->student->full_name ?? 'N/A' }}</div>
                            <div class="text-muted small">{{ $studentIssue->student->email ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold text-dark">Report Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Status</span>
                        @if($studentIssue->status === 'Open')
                            <span class="badge bg-danger fs-6">Open</span>
                        @elseif($studentIssue->status === 'In Progress')
                            <span class="badge bg-warning text-dark fs-6">In Progress</span>
                        @elseif($studentIssue->status === 'Resolved')
                            <span class="badge bg-success fs-6">Resolved</span>
                        @elseif($studentIssue->status === 'Escalated')
                            <span class="badge bg-dark fs-6">Escalated</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Issue Type</span>
                        <span class="fw-medium">{{ $studentIssue->issue_type }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Reported By</span>
                        <span class="fw-medium">{{ $studentIssue->reporter->full_name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-muted d-block small mb-1">Last Updated</span>
                        <span class="fw-medium">{{ $studentIssue->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
