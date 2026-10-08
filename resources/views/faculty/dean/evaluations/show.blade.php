@extends('layouts.app')

@section('title', 'View Faculty Evaluation')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Faculty Evaluation Details</h5>
            <div>
                @if($evaluation->status === 'Draft')
                    <a href="{{ route('faculty.dean.evaluations.edit', $evaluation) }}" class="btn btn-sm btn-outline-primary me-2">Edit Draft</a>
                @endif
                <a href="{{ route('faculty.dean.evaluations.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
        </div>
        <div class="card-body p-4">
            
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-1 text-muted small">Faculty Member</p>
                    <p class="fw-medium mb-0">{{ $evaluation->faculty->name ?? 'N/A' }}</p>
                    <p class="text-muted small">{{ $evaluation->faculty->email ?? '' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Academic Year</p>
                    <p class="fw-medium mb-0">{{ $evaluation->academic_year }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-1 text-muted small">Performance Score</p>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-3" style="height: 10px;">
                            <div class="progress-bar bg-{{ $evaluation->performance_score >= 80 ? 'success' : ($evaluation->performance_score >= 60 ? 'primary' : ($evaluation->performance_score >= 40 ? 'warning' : 'danger')) }}" role="progressbar" style="width: {{ $evaluation->performance_score }}%" aria-valuenow="{{ $evaluation->performance_score }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="fw-bold">{{ $evaluation->performance_score }}/100</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Status</p>
                    <span class="badge bg-{{ $evaluation->status === 'Approved' ? 'success' : ($evaluation->status === 'Rejected' ? 'danger' : ($evaluation->status === 'Pending Approval' ? 'warning text-dark' : 'secondary')) }}">
                        {{ $evaluation->status }}
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <p class="mb-2 text-muted small">Evaluation Comments</p>
                <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $evaluation->comments }}</div>
            </div>
            
            @if($evaluation->registrar_feedback)
                <div class="mb-4">
                    <p class="mb-2 text-muted small">Registrar / Admin Feedback</p>
                    <div class="p-3 bg-white border border-danger border-opacity-25 rounded text-danger" style="white-space: pre-wrap;">{{ $evaluation->registrar_feedback }}</div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
