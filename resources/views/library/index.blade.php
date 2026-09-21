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
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Catalogue Items</span>
                        <i class="bi bi-book fs-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['catalogue_items'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Active Loans</span>
                        <i class="bi bi-journal-arrow-up fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['active_loans'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Overdue Loans</span>
                        <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $stats['overdue_loans'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small fw-semibold text-uppercase">Fines Due</span>
                        <i class="bi bi-cash-stack fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-0">${{ number_format($stats['fines_due'], 2) }}</h3>
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
                            <th>User</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivity as $activity)
                            <tr>
                                <td>{{ $activity->libraryItem->title }}</td>
                                <td>{{ $activity->user->full_name ?? 'N/A' }}</td>
                                <td>
                                    @if($activity->status == 'borrowed')
                                        <span class="badge text-bg-primary">Borrowed</span>
                                    @elseif($activity->status == 'returned')
                                        <span class="badge text-bg-success">Returned</span>
                                    @elseif($activity->status == 'overdue')
                                        <span class="badge text-bg-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>{{ $activity->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent activity</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
