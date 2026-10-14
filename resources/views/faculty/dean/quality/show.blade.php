@extends('layouts.app')

@section('title', 'View Quality Review')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quality Review Details</h5>
            <div>
                @if($quality->status === 'Draft')
                    <a href="{{ route('faculty.dean.quality.edit', $quality) }}" class="btn btn-sm btn-outline-primary me-2">Edit Draft</a>
                @endif
                <a href="{{ route('faculty.dean.quality.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
        </div>
        <div class="card-body p-4">
            <h3 class="mb-4">{{ $quality->title }}</h3>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Program</p>
                    <p class="fw-medium">{{ $quality->program->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Status</p>
                    <span class="badge bg-{{ $quality->status === 'Approved' ? 'success' : ($quality->status === 'Rejected' ? 'danger' : ($quality->status === 'Pending Approval' ? 'warning text-dark' : 'secondary')) }}">
                        {{ $quality->status }}
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <p class="mb-2 text-muted small">Review Notes</p>
                <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap;">{{ $quality->review_notes }}</div>
            </div>

            @if($quality->registrar_feedback)
                <div class="mb-4">
                    <p class="mb-2 text-muted small">Registrar / Admin Feedback</p>
                    <div class="p-3 bg-white border border-danger border-opacity-25 rounded text-danger" style="white-space: pre-wrap;">{{ $quality->registrar_feedback }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
