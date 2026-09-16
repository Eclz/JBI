@extends('layouts.app')

@section('title', 'Bookings & Maintenance')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Bookings & Maintenance</h2>
            <p class="text-muted mb-0">Room bookings, service requests, and maintenance follow-up.</p>
        </div>
        <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Request booking</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
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
                            <td>Lecture hall booking</td>
                            <td>Science Hall</td>
                            <td>2026-09-20</td>
                            <td><span class="badge text-bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Water pump checks</td>
                            <td>Student residence</td>
                            <td>2026-09-21</td>
                            <td><span class="badge text-bg-success">Assigned</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
