@extends('layouts.app')

@section('title', 'Library Services')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Library Services</h2>
            <p class="text-muted mb-0">Catalogue, loans, holds, and borrowing activity.</p>
        </div>
        @if(auth()->user()->isStudent())
            <a href="{{ route('library.my-loans') }}" class="btn btn-primary">
                <i class="bi bi-person-badge me-2"></i>My Borrowing
            </a>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Loans Due</span>
                        <i class="bi bi-journal-bookmark fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['catalogue_items'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Reservations</span>
                        <i class="bi bi-calendar-check fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['available_copies'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Fines Due</span>
                        <i class="bi bi-cash-stack fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['fines_due'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">Recent library activity</h5>
                <a href="{{ route('library.catalogue.index') }}" class="btn btn-sm btn-outline-primary">Browse catalogue</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Last updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Introduction to Software Engineering</td>
                            <td>Textbook</td>
                            <td><span class="badge text-bg-success">Available</span></td>
                            <td>Today</td>
                        </tr>
                        <tr>
                            <td>Research Methods in Computing</td>
                            <td>Reference</td>
                            <td><span class="badge text-bg-warning">On reserve</span></td>
                            <td>Yesterday</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
