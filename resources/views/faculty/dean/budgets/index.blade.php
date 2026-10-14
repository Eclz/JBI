@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Budget Requests</h1>
            <p class="text-muted">Manage department budget allocations, event funding, and financial requests.</p>
        </div>
        <div>
            <a href="{{ route('faculty.dean.budgets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Request
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Title</th>
                            <th>Department</th>
                            <th>Amount (GHS)</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $budget)
                        <tr>
                            <td class="ps-4 fw-medium text-dark">{{ $budget->title }}</td>
                            <td>{{ $budget->department->name ?? 'N/A' }}</td>
                            <td class="font-monospace">
                                ₵{{ number_format($budget->amount, 2) }}
                            </td>
                            <td>
                                @if($budget->status === 'Draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($budget->status === 'Pending Finance Approval')
                                    <span class="badge bg-warning text-dark">Pending Finance</span>
                                @elseif($budget->status === 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($budget->status === 'Rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $budget->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('faculty.dean.budgets.show', $budget) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($budget->status === 'Draft' || $budget->status === 'Rejected')
                                    <a href="{{ route('faculty.dean.budgets.edit', $budget) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('faculty.dean.budgets.destroy', $budget) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this budget request?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-1 d-block mb-3"></i>
                                <p class="mb-0">No budget requests found.</p>
                                <a href="{{ route('faculty.dean.budgets.create') }}" class="btn btn-sm btn-primary mt-3">Create First Request</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($budgets->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $budgets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
