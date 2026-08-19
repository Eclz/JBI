@extends('layouts.app')

@section('title', 'Expenditures & Requisitions')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-cart-check me-2"></i>Expenditures & Requisitions
            </h1>
            <p class="text-muted mb-0">Record and approve operational expenditures, stationery requisitions, equipment maintenance & repairs</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-lg me-1"></i>Record New Expense
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
                            <th class="ps-3">Expense #</th>
                            <th>Category</th>
                            <th>Title / Description</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-primary">{{ $exp->expense_number }}</td>
                                <td><span class="badge bg-warning text-dark px-2 py-1">{{ $exp->category }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                    <small class="text-muted">{{ $exp->description }}</small>
                                </td>
                                <td>{{ $exp->department->name ?? 'General Department' }}</td>
                                <td class="small">{{ $exp->expense_date ? $exp->expense_date->format('M d, Y') : '-' }}</td>
                                <td><span class="badge bg-success px-2.5 py-1.5 text-uppercase">{{ $exp->status }}</span></td>
                                <td class="text-end pe-3 fw-bold text-danger">{{ $currencyCode }} {{ number_format($exp->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No expense records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $expenses->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.finance.expenses.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Record & Approve Expense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <select name="department_id" class="form-select" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="Supplies & Stationery">Supplies & Stationery</option>
                                <option value="Equipment & Repairs">Equipment & Repairs</option>
                                <option value="Utilities & Internet">Utilities & Internet</option>
                                <option value="Travel & Fuel">Travel & Fuel</option>
                                <option value="Miscellaneous">Miscellaneous</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Title / Item Description <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Printing Toner & Paper" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description / Notes</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
