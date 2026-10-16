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
                    <div class="card-body" role="button" data-bs-toggle="modal" data-bs-target="#profileModal{{ $emp->id }}" style="cursor: pointer;">
                        @if($emp->profile_picture)
                            <img src="{{ asset('storage/' . $emp->profile_picture) }}" alt="{{ $emp->full_name }}" class="rounded-circle mx-auto mb-3 object-fit-cover shadow-sm" style="width: 80px; height: 80px;">
                        @else
                            <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2.5rem;">
                                <i class="bi bi-person text-white"></i>
                            </div>
                        @endif
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

                <!-- Profile Modal -->
                <div class="modal fade" id="profileModal{{ $emp->id }}" tabindex="-1" aria-labelledby="profileModalLabel{{ $emp->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 pb-0 justify-content-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center pt-0 px-4 pb-4">
                                @if($emp->profile_picture)
                                    <img src="{{ asset('storage/' . $emp->profile_picture) }}" alt="{{ $emp->full_name }}" class="rounded-circle mx-auto mb-3 object-fit-cover shadow-sm" style="width: 100px; height: 100px;">
                                @else
                                    <div class="avatar-xl bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm text-white" style="width: 100px; height: 100px; font-size: 3rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                @endif
                                <h4 class="fw-bold text-dark mb-1">{{ $emp->full_name }}</h4>
                                <p class="text-primary mb-2 fw-medium">{{ $emp->hrProfile?->job_title ?? $emp->role_name }}</p>
                                @if($emp->hrProfile?->department)
                                    <span class="badge bg-secondary mb-3">{{ $emp->hrProfile->department }}</span>
                                @endif
                                
                                <hr class="my-3 opacity-25">
                                
                                <div class="row text-start mb-3 g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-envelope me-1"></i>Email</small>
                                        <div class="text-dark small text-break">{{ $emp->email }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-telephone me-1"></i>Phone</small>
                                        <div class="text-dark small">{{ $emp->phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-person-badge me-1"></i>Gender</small>
                                        <div class="text-dark small text-capitalize">{{ $emp->gender ?? 'Not specified' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-briefcase me-1"></i>Status</small>
                                        <div class="text-dark small">
                                            @if($emp->hrProfile)
                                                <span class="badge bg-{{ $emp->hrProfile->status == 'Active' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $emp->hrProfile->status == 'Active' ? 'success' : 'warning' }} border-0 px-2">{{ $emp->hrProfile->status }}</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success border-0 px-2">Active</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1"><i class="bi bi-geo-alt me-1"></i>Address</small>
                                        <div class="text-dark small">{{ $emp->address ?? 'Not specified' }}</div>
                                    </div>
                                </div>
                                
                                <div class="text-start bg-light p-3 rounded text-muted small">
                                    <h6 class="text-dark fw-bold mb-2">Bio / Notes</h6>
                                    {{ $emp->hrProfile?->notes ?? 'No additional information available for this employee.' }}
                                </div>
                                
                                <div class="mt-4 d-flex justify-content-center gap-2">
                                    <a href="mailto:{{ $emp->email }}" class="btn btn-primary px-4"><i class="bi bi-envelope me-2"></i>Send Email</a>
                                    @if($emp->phone)
                                        <a href="tel:{{ $emp->phone }}" class="btn btn-outline-success px-4"><i class="bi bi-telephone"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
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
