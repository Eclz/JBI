@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body p-4 text-white">
                    <h3 class="mb-2 fw-bold"><i class="bi bi-building me-2"></i>Hostel & Accommodation</h3>
                    <p class="mb-0" style="color: rgba(255, 255, 255, 0.9);">
                        Manage your campus living arrangements and request accommodation.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error/Success Messages -->
    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Current Allocation -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold" style="color: #212529;">Current Allocation</h5>
                </div>
                <div class="card-body">
                    @if($allocation)
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block">
                                    <i class="bi bi-door-closed fs-1 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Hostel Name</p>
                                        <p class="fw-bold mb-0 fs-5">{{ $allocation->room->hostel->name }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Room Number</p>
                                        <p class="fw-bold mb-0 fs-5">{{ $allocation->room->room_number }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Status</p>
                                        @if($allocation->status === 'approved')
                                            <span class="badge bg-success rounded-pill px-3 py-2">Approved</span>
                                        @elseif($allocation->status === 'pending')
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending Approval</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($allocation->status) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Semester</p>
                                        <p class="fw-bold mb-0">{{ $allocation->semester->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-house-x fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold text-dark">No Current Allocation</h5>
                            <p class="text-muted mb-0">You have not been allocated a room for the current semester.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Hostels -->
        @if(!$allocation || in_array($allocation->status, ['rejected', 'vacated']))
        <div class="col-lg-12">
            <h5 class="fw-bold mb-3" style="color: #212529;">Request Accommodation</h5>
            <div class="row g-4">
                @forelse($hostels as $hostel)
                    @if($hostel->rooms->count() > 0)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="fw-bold mb-0">{{ $hostel->name }}</h5>
                                        <span class="badge bg-primary rounded-pill">{{ ucfirst($hostel->type) }}</span>
                                    </div>
                                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i>{{ $hostel->location ?? 'Campus' }}</p>
                                    <p class="small">{{ $hostel->description }}</p>

                                    <hr>

                                    <form action="{{ route('student.hostel.request') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Select Available Room</label>
                                            <select class="form-select border-0 bg-light" name="hostel_room_id" required>
                                                <option value="">-- Choose Room --</option>
                                                @foreach($hostel->rooms as $room)
                                                    <option value="{{ $room->id }}">Room {{ $room->room_number }} (Fee: ${{ number_format($room->fee_per_semester, 2) }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                                            Request Room Allocation
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="bi bi-info-circle-fill me-2"></i> No hostels with available rooms found at this time.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
