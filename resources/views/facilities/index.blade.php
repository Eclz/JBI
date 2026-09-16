@extends('layouts.app')

@section('title', 'Estates & Facilities')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Estates & Facilities</h2>
            <p class="text-muted mb-0">Rooms, bookings, maintenance, and campus asset oversight.</p>
        </div>
        <a href="{{ route('facilities.rooms.index') }}" class="btn btn-primary">Manage facilities</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Rooms</span>
                        <i class="bi bi-door-open fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $rooms }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Bookings</span>
                        <i class="bi bi-calendar-event fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $bookings }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Maintenance</span>
                        <i class="bi bi-wrench fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $maintenance }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Upcoming room requests</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Seminar room booking</td>
                            <td>Science Hall</td>
                            <td>2026-09-20</td>
                            <td><span class="badge text-bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Lift maintenance check</td>
                            <td>Main admin block</td>
                            <td>2026-09-18</td>
                            <td><span class="badge text-bg-success">Scheduled</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
