@extends('layouts.app')

@section('title', 'Department Expenses & Budget Alignment - ' . $department->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                    <i class="bi bi-wallet2 me-1"></i> Head of Department Finance
                </span>
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-building me-1"></i> {{ $department->name }} ({{ $department->code }})
                </span>
                @if($budget)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                        <i class="bi bi-tag-fill me-1"></i> {{ $budget->budget_code }} &bull; {{ $budget->academic_year }}
                    </span>
                @endif
            </div>
            <h1 class="h3 mb-1 fw-bold text-dark">Department Expenses & Budget Alignment</h1>
            <p class="text-muted mb-0">Record and track departmental expenditures against the authorized department budget.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('faculty.hod.department.index') }}" class="btn btn-outline-secondary fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Department Overview
            </a>
            @if($canCreateExpense)
                <button class="btn btn-success fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#recordExpenseModal" id="btn-record-expense-modal">
                    <i class="bi bi-plus-circle me-1"></i> Record Expense
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Department Budget Alignment KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Allocated Budget -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden position-relative" style="background: linear-gradient(135deg, #f8fbfd 0%, #eef5fb 100%); border-left: 4px solid #0d6efd !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-xs fw-bold text-uppercase text-primary letter-spacing-1">Allocated Budget</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $currencyCode }} {{ number_format($allocatedAmount, 2) }}</h3>
                        </div>
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i class="bi bi-piggy-bank fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block">
                        @if($budget)
                            Active budget for {{ $budget->academic_year }}
                        @else
                            <span class="text-warning"><i class="bi bi-info-circle me-1"></i>No formal budget assigned yet</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- 2. Total Spent -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden position-relative" style="background: linear-gradient(135deg, #fdf8f8 0%, #faeeee 100%); border-left: 4px solid #dc3545 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-xs fw-bold text-uppercase text-danger letter-spacing-1">Total Spent</span>
                            <h3 class="fw-bold text-danger mb-0 mt-1">{{ $currencyCode }} {{ number_format($spentAmount, 2) }}</h3>
                        </div>
                        <div class="p-2 bg-danger-subtle text-danger rounded-3">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Approved expenditures</small>
                        @if($allocatedAmount > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ $utilizationPercentage }}% used</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Remaining Available Budget -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden position-relative" style="background: linear-gradient(135deg, #f7fdf9 0%, #eefbf1 100%); border-left: 4px solid #198754 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-xs fw-bold text-uppercase text-success letter-spacing-1">Remaining Balance</span>
                            <h3 class="fw-bold {{ $remainingBudget <= 0 && $allocatedAmount > 0 ? 'text-danger' : 'text-success' }} mb-0 mt-1">
                                {{ $currencyCode }} {{ number_format($remainingBudget, 2) }}
                            </h3>
                        </div>
                        <div class="p-2 bg-success-subtle text-success rounded-3">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                    </div>
                    @if($allocatedAmount > 0)
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar {{ $utilizationPercentage > 90 ? 'bg-danger' : ($utilizationPercentage > 70 ? 'bg-warning' : 'bg-success') }}"
                                 role="progressbar"
                                 style="width: {{ $utilizationPercentage }}%"
                                 aria-valuenow="{{ $utilizationPercentage }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    @else
                        <small class="text-muted d-block">Expenditures tracked on rolling basis</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Pending / Committed -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden position-relative" style="background: linear-gradient(135deg, #fdfbf7 0%, #fbf5eb 100%); border-left: 4px solid #ffc107 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-xs fw-bold text-uppercase text-warning-emphasis letter-spacing-1">Pending Approval</span>
                            <h3 class="fw-bold text-warning-emphasis mb-0 mt-1">{{ $currencyCode }} {{ number_format($pendingAmount, 2) }}</h3>
                        </div>
                        <div class="p-2 bg-warning-subtle text-warning-emphasis rounded-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block">Submitted requests awaiting Bursar approval</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('faculty.hod.expenses.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search title, description, EXP#..." value="{{ request('search') }}" id="search-input">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select name="category" class="form-select bg-light" id="filter-category" onchange="this.form.submit()">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select name="status" class="form-select bg-light" id="filter-status" onchange="this.form.submit()">
                        <option value="all">All Statuses</option>
                        <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending Approval</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <input type="date" name="date_from" class="form-control bg-light" value="{{ request('date_from') }}" title="Date From" placeholder="From Date">
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="d-flex gap-1">
                        <input type="date" name="date_to" class="form-control bg-light" value="{{ request('date_to') }}" title="Date To" placeholder="To Date">
                        <button type="submit" class="btn btn-primary" title="Apply Filter">
                            <i class="bi bi-funnel-fill"></i>
                        </button>
                        @if(request()->anyFilled(['search', 'category', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('faculty.hod.expenses.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-list-check text-primary me-2"></i>Recorded Department Expenses ({{ $expenses->total() }})
            </h5>
            <div class="text-muted small">
                Showing {{ $expenses->firstItem() ?? 0 }} - {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="hod-expenses-table">
                    <thead class="bg-light text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4">Expense Ref</th>
                            <th>Title & Description</th>
                            <th>Category</th>
                            <th>Expense Date</th>
                            <th>Payment</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                            @if($canViewExpense || $canEditExpense || $canDeleteExpense)
                                <th class="text-end pe-4">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-4">
                                    <span class="font-monospace fw-bold text-primary small">{{ $exp->expense_number }}</span>
                                    <div class="text-xs text-muted">Created {{ $exp->created_at->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                    @if($exp->description)
                                        <div class="small text-muted text-truncate" style="max-width: 280px;" title="{{ $exp->description }}">
                                            {{ $exp->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $exp->category }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $exp->expense_date->format('M d, Y') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary small">
                                        {{ $exp->payment_method ?? 'Petty Cash' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold fs-6 {{ $exp->status === 'approved' ? 'text-dark' : 'text-muted' }}">
                                        {{ $currencyCode }} {{ number_format($exp->amount, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($exp->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Approved
                                        </span>
                                    @elseif($exp->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i> Pending Approval
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                            <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                                @if($canViewExpense || $canEditExpense || $canDeleteExpense)
                                    <td class="text-end pe-4">
                                      <div class="btn-group btn-group-sm">
                                        @if($canViewExpense)
                                        <button type="button"
                                                class="btn btn-outline-secondary"
                                                title="View Details"
                                                data-bs-toggle="modal"
                                                data-bs-target="#showExpenseModal{{ $exp->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @endif
                                        @if($canEditExpense)
                                        <button type="button"
                                                class="btn btn-outline-primary"
                                                title="Edit Expense"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editExpenseModal{{ $exp->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @endif
                                        @if($canDeleteExpense)
                                        <form action="{{ route('faculty.hod.expenses.destroy', $exp->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this expense record ({{ $exp->expense_number }})? The department budget will be updated accordingly.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Expense">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 7 + (($canViewExpense || $canEditExpense || $canDeleteExpense) ? 1 : 0) }}" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="bi bi-wallet2 text-muted mb-2 d-block" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold text-dark">No Department Expenses Found</h6>
                                        <p class="text-muted small mb-3">No expenses recorded matching the current filter criteria.</p>
                                        @if($canCreateExpense)
                                            <button class="btn btn-success fw-bold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#recordExpenseModal">
                                                <i class="bi bi-plus-circle me-1"></i> Record First Expense
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">Page {{ $expenses->currentPage() }} of {{ $expenses->lastPage() }}</small>
                <div>{{ $expenses->links() }}</div>
            </div>
        @endif
    </div>
</div>

{{-- ============================================================
     ALL MODALS MUST BE OUTSIDE THE TABLE
     ============================================================ --}}

@foreach($expenses as $exp)
    @if($canViewExpense)
    <!-- View Details Modal -->
    <div class="modal fade" id="showExpenseModal{{ $exp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0">Expense Details</h6>
                            <span class="font-monospace small text-primary">{{ $exp->expense_number }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 text-center mb-3">
                        <small class="text-muted d-block text-uppercase fw-semibold">Expense Amount</small>
                        <h2 class="fw-bold text-dark mb-0 mt-1">{{ $currencyCode }} {{ number_format($exp->amount, 2) }}</h2>
                        <div class="mt-2">
                            @if($exp->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle-fill me-1"></i> Charged to Department Budget
                                </span>
                            @elseif($exp->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    <i class="bi bi-hourglass-split me-1"></i> Awaiting Finance Approval
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-4 text-muted small fw-semibold">Department:</div>
                        <div class="col-sm-8 small fw-bold text-dark">{{ $department->name }} ({{ $department->code }})</div>

                        <div class="col-sm-4 text-muted small fw-semibold">Title:</div>
                        <div class="col-sm-8 small fw-bold text-dark">{{ $exp->title }}</div>

                        <div class="col-sm-4 text-muted small fw-semibold">Category:</div>
                        <div class="col-sm-8 small text-dark"><span class="badge bg-light text-dark border">{{ $exp->category }}</span></div>

                        <div class="col-sm-4 text-muted small fw-semibold">Expense Date:</div>
                        <div class="col-sm-8 small text-dark">{{ $exp->expense_date->format('F d, Y') }}</div>

                        <div class="col-sm-4 text-muted small fw-semibold">Payment Method:</div>
                        <div class="col-sm-8 small text-dark">{{ $exp->payment_method ?? 'Petty Cash' }}</div>

                        <div class="col-sm-4 text-muted small fw-semibold">Recorded By:</div>
                        <div class="col-sm-8 small text-dark">{{ $exp->requester->name ?? 'Head of Department' }}</div>

                        @if($exp->approved_by)
                            <div class="col-sm-4 text-muted small fw-semibold">Approved By:</div>
                            <div class="col-sm-8 small text-dark">{{ $exp->approver->name ?? 'Authorized Officer' }}</div>
                        @endif
                    </div>

                    @if($exp->description)
                        <div class="border-top pt-3">
                            <small class="text-muted d-block fw-semibold mb-1">Description / Notes:</small>
                            <div class="p-2 bg-light rounded small text-dark">{{ $exp->description }}</div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($canEditExpense)
    <!-- Edit Expense Modal -->
    <div class="modal fade" id="editExpenseModal{{ $exp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('faculty.hod.expenses.update', $exp->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-light border-0 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-primary-subtle text-primary rounded-3">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0">Edit Department Expense</h6>
                                <span class="font-monospace small text-primary">{{ $exp->expense_number }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Expense Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $exp->title) }}" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" @selected(old('category', $exp->category) === $cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount', $exp->amount) }}" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" max="{{ date('Y-m-d') }}" class="form-control" value="{{ old('expense_date', $exp->expense_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm }}" @selected(old('payment_method', $exp->payment_method) === $pm)>{{ $pm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="approved" @selected(old('status', $exp->status) === 'approved')>Approved (Deduct from Budget)</option>
                                <option value="pending" @selected(old('status', $exp->status) === 'pending')>Pending (Submit to Finance)</option>
                                <option value="rejected" @selected(old('status', $exp->status) === 'rejected')>Rejected</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">Description / Justification</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Additional details regarding this expenditure">{{ old('description', $exp->description) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<!-- Record Expense Modal -->
@if($canCreateExpense)
<div class="modal fade" id="recordExpenseModal" tabindex="-1" aria-labelledby="recordExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form action="{{ route('faculty.hod.expenses.store') }}" method="POST" id="record-expense-form">
                @csrf
                <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #198754 0%, #0f5132 100%); border-top-left-radius: 14px; border-top-right-radius: 14px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-white bg-opacity-25 rounded-3">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="recordExpenseModalLabel">Record Department Expense</h5>
                            <small class="text-white-50">Department: {{ $department->name }} ({{ $department->code }})</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Live Department Budget Alignment Banner -->
                    <div class="p-3 mb-4 rounded-3 border" style="background: #f8fafc;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                            <div>
                                <small class="text-muted text-uppercase fw-bold letter-spacing-1 d-block">Department Budget Allocation</small>
                                <span class="fw-bold fs-5 text-dark">{{ $currencyCode }} {{ number_format($allocatedAmount, 2) }}</span>
                                @if($budget)
                                    <span class="badge bg-light text-muted border ms-1 font-monospace">{{ $budget->budget_code }}</span>
                                @endif
                            </div>
                            <div class="text-sm-end">
                                <small class="text-muted text-uppercase fw-bold letter-spacing-1 d-block">Available Remaining Balance</small>
                                <span class="fw-bold fs-5 {{ $remainingBudget <= 0 && $allocatedAmount > 0 ? 'text-danger' : 'text-success' }}" id="modal-remaining-balance" data-raw-balance="{{ $remainingBudget }}">
                                    {{ $currencyCode }} {{ number_format($remainingBudget, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Live Budget Comparison Alert (JS-Driven) -->
                        <div class="alert alert-warning border-0 mt-3 mb-0 small d-none" id="budget-exceeded-alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Budget Notice:</strong> The expense amount entered exceeds the remaining allocated budget for this department (<span id="overspent-diff-text"></span>). Recording this will be flagged as an over-budget expenditure.
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark" for="expense_title">
                                Expense Title / Purpose <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="expense_title"
                                   class="form-control form-control-lg fs-6"
                                   placeholder="e.g. Science Lab Reagents & Consumables"
                                   value="{{ old('title') }}"
                                   required>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" for="expense_category">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="expense_category" class="form-select form-select-lg fs-6" required>
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select Expense Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" for="expense_amount">
                                Amount <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light fw-bold text-muted fs-6">{{ $currencyCode }}</span>
                                <input type="number"
                                       step="0.01"
                                       min="0.01"
                                       name="amount"
                                       id="expense_amount"
                                       class="form-control fs-6"
                                       placeholder="0.00"
                                       value="{{ old('amount') }}"
                                       required>
                            </div>
                        </div>

                        <!-- Expense Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" for="expense_date">
                                Expense Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="expense_date"
                                   id="expense_date"
                                   class="form-control"
                                   max="{{ date('Y-m-d') }}"
                                   value="{{ old('expense_date', date('Y-m-d')) }}"
                                   required>
                            <small class="text-muted">Must be on or before today.</small>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark" for="expense_payment_method">
                                Payment Method
                            </label>
                            <select name="payment_method" id="expense_payment_method" class="form-select">
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm }}" @selected(old('payment_method', 'Petty Cash') === $pm)>{{ $pm }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status / Recording Mode -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Expenditure Classification & Accounting</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="status" id="status_approved" value="approved" checked>
                                        <label class="form-check-label fw-bold text-dark d-block" for="status_approved">
                                            <i class="bi bi-check-circle-fill text-success me-1"></i> Direct Approved Expense
                                            <small class="text-muted d-block fw-normal mt-1">Directly deducted and reconciled with department budget.</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="status" id="status_pending" value="pending">
                                        <label class="form-check-label fw-bold text-dark d-block" for="status_pending">
                                            <i class="bi bi-hourglass-split text-warning me-1"></i> Submit for Finance Approval
                                            <small class="text-muted d-block fw-normal mt-1">Logged as pending request awaiting disbursement by Bursar.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark" for="expense_description">
                                Description & Justification
                            </label>
                            <textarea name="description"
                                      id="expense_description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Provide context, vendor/invoice references, or justification against department budget line items...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3 px-4" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm" id="btn-submit-expense">
                        <i class="bi bi-check-lg me-1"></i> Save & Reconcile Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Open modal automatically if #record hash is in URL
        if (window.location.hash === '#record') {
            const modalEl = document.getElementById('recordExpenseModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        // Live Budget Comparison Feedback
        const amountInput = document.getElementById('expense_amount');
        const balanceEl = document.getElementById('modal-remaining-balance');
        const alertEl = document.getElementById('budget-exceeded-alert');
        const diffText = document.getElementById('overspent-diff-text');

        if (amountInput && balanceEl && alertEl) {
            const availableBalance = parseFloat(balanceEl.dataset.rawBalance || 0);

            amountInput.addEventListener('input', function () {
                const val = parseFloat(this.value || 0);
                if (availableBalance > 0 && val > availableBalance) {
                    const diff = (val - availableBalance).toFixed(2);
                    diffText.textContent = '{{ $currencyCode }} ' + diff + ' over budget';
                    alertEl.classList.remove('d-none');
                } else {
                    alertEl.classList.add('d-none');
                }
            });
        }
    });
</script>
@endpush
@endsection
