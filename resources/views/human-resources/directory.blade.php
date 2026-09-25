@extends('layouts.app')

@section('title', 'Employee Directory')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-1 fw-bold text-dark">Employee Directory</h2>
        <p class="text-muted mb-0">Find and connect with colleagues across the university.</p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($employees as $emp)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm text-center pt-4 pb-3">
                    <div class="card-body">
                        <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($emp->full_name, 0, 1) }}
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $emp->full_name }}</h5>
                        <p class="text-muted small mb-2">{{ $emp->hrProfile?->job_title ?? $emp->role_name }}</p>
                        @if($emp->hrProfile?->department)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill mb-3">{{ $emp->hrProfile->department }}</span>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0 d-flex justify-content-center gap-2">
                        <a href="mailto:{{ $emp->email }}" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 35px; height: 35px; padding: 0.35rem;" title="Email">
                            <i class="bi bi-envelope"></i>
                        </a>
                        @if($emp->phone)
                            <a href="tel:{{ $emp->phone }}" class="btn btn-sm btn-outline-success rounded-circle" style="width: 35px; height: 35px; padding: 0.35rem;" title="Call">
                                <i class="bi bi-telephone"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted mb-0">No employees found in the directory.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
