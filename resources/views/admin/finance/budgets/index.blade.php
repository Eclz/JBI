@extends('layouts.app')

@section('title', 'Departmental Budget Allocations')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-pie-chart me-2"></i>Departmental Budget Allocations
            </h1>
            <p class="text-muted mb-0">Allocate, monitor, and manage annual operational budgets for academic and administrative departments</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                <i class="bi bi-plus-lg me-1"></i>Allocate New Budget
            </button>
            <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary">Back to Finance Hub</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Academic Year</th>
                            <th>Department</th>
                            <th>Allocated Amount</th>
                            <th>Spent Amount</th>
                            <th>Remaining Balance</th>
                            <th>Utilization</th>
                            <th class="text-end pe-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $bdg)
                            @php
                                $spent = $bdg->spent_amount;
                                $allocated = $bdg->allocated_amount;
                                $balance = max(0, $allocated - $spent);
                                $perc = $allocated > 0 ? min(100, round(($spent / $allocated) * 100)) : 0;
                            @endphp
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-primary">{{ $bdg->budget_code }}</td>
                                <td class="fw-bold">{{ $bdg->academic_year }}</td>
                                <td class="fw-bold text-dark">{{ $bdg->department->name ?? 'General Department' }}</td>
                                <td class="fw-bold text-dark">{{ $currencyCode }} {{ number_format($allocated, 2) }}</td>
                                <td class="fw-bold text-danger">{{ $currencyCode }} {{ number_format($spent, 2) }}</td>
                                <td class="fw-bold text-success">{{ $currencyCode }} {{ number_format($balance, 2) }}</td>
                                <td style="width: 180px;">
                                    <div class="d-flex justify-content-between align-items-center mb-1 small fw-bold">
                                        <span>{{ $perc }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $perc > 85 ? 'bg-danger' : ($perc > 60 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $perc }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-success px-2.5 py-1.5 text-uppercase">{{ $bdg->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No budget allocations recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($budgets->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $budgets->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addBudgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.finance.budgets.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Allocate Department Budget</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control" value="2026/2027" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Choose Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Allocated Budget Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="allocated_amount" class="form-control" placeholder="250000000" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Budget Allocation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
