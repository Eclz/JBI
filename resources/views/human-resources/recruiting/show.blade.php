@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="height: calc(100vh - 60px); display: flex; flex-direction: column;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-1">Applicant Tracking System</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('human-resources.section', 'recruiting') }}">Recruiting</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $vacancy->position_title }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('human-resources.section', 'recruiting') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vacancyDetailsModal">
                <i class="bi bi-info-circle me-1"></i> Vacancy Info
            </button>
            <button class="btn btn-primary fw-bold" onclick="alert('In a real app, this would open a public job link.')">
                <i class="bi bi-link-45deg me-1"></i> Job Link
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Kanban Board Container -->
    <div class="d-flex gap-3 overflow-auto flex-grow-1 pb-3 kanban-board" style="min-height: 500px;">
        
        @foreach(['Applied', 'Screening', 'Shortlisted', 'Interview', 'Offer', 'Hired', 'Rejected'] as $stage)
            <div class="kanban-column bg-light rounded shadow-sm d-flex flex-column flex-shrink-0" style="width: 280px;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-uppercase" style="font-size: 0.85rem;">{{ $stage }}</h6>
                    <span class="badge bg-secondary rounded-pill">{{ count($kanban[$stage]) }}</span>
                </div>
                
                <div class="p-2 flex-grow-1 overflow-auto kanban-lane" data-stage="{{ $stage }}">
                    @foreach($kanban[$stage] as $applicant)
                        <div class="card border shadow-sm mb-2 kanban-card cursor-pointer">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1">{{ $applicant->full_name }}</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-envelope me-1"></i> {{ $applicant->email }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $applicant->created_at->diffForHumans() }}</small>
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border p-1 py-0 shadow-none text-muted" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 text-sm">
                                            <li><h6 class="dropdown-header">Move to...</h6></li>
                                            @foreach(['Applied', 'Screening', 'Shortlisted', 'Interview', 'Offer', 'Hired', 'Rejected'] as $s)
                                                @if($s !== $stage)
                                                    <li>
                                                        <form action="{{ route('human-resources.recruiting.applicants.update', $applicant->id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="{{ $s }}">
                                                            <button type="submit" class="dropdown-item">{{ $s }}</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(count($kanban[$stage]) === 0)
                        <div class="text-center py-4 text-muted small fst-italic opacity-50">
                            No applicants in this stage.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
</div>

<!-- Vacancy Details Modal -->
<div class="modal fade" id="vacancyDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="fw-bold mb-0">Vacancy Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h4 class="text-primary fw-bold mb-1">{{ $vacancy->position_title }}</h4>
                <p class="text-muted mb-4">{{ $vacancy->department }}</p>
                
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase">Type</label>
                        <div>{{ $vacancy->employment_type }}</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase">Openings</label>
                        <div>{{ $vacancy->num_openings }} position(s)</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase">Opened</label>
                        <div>{{ $vacancy->opening_date ? $vacancy->opening_date->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase">Closing</label>
                        <div>{{ $vacancy->closing_date ? $vacancy->closing_date->format('d M Y') : 'Open until filled' }}</div>
                    </div>
                </div>
                
                <label class="text-muted small fw-bold text-uppercase mb-1">Description</label>
                <div class="bg-light p-3 rounded small mb-0 text-muted">
                    {!! nl2br(e($vacancy->job_description ?: 'No description provided.')) !!}
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom Scrollbar for Kanban Board */
    .kanban-board::-webkit-scrollbar {
        height: 8px;
    }
    .kanban-board::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .kanban-board::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 4px;
    }
    .kanban-board::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; 
    }
    
    .kanban-lane::-webkit-scrollbar {
        width: 4px;
    }
    .kanban-lane::-webkit-scrollbar-track {
        background: transparent;
    }
    .kanban-lane::-webkit-scrollbar-thumb {
        background: #e0e0e0;
        border-radius: 4px;
    }
</style>
@endpush
@endsection
