@extends('layouts.app')

@section('title', 'Expenditures & Functional Allocation')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 fw-semibold">
                    <i class="bi bi-diagram-3-fill me-1"></i> Institutional Financial Governance
                </span>
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-normal">
                    {{ $totalCount }} Records Recorded
                </span>
            </div>
            <h1 class="h3 mb-1 text-dark fw-bold mt-2">
                <i class="bi bi-wallet2 text-primary me-2"></i>Expenditure Allocation & Classification Hub
            </h1>
            <p class="text-muted mb-0">
                Institutional cost accounting and budget reconciliation categorized across <strong>Academic</strong>, <strong>Administrative</strong>, and <strong>Operational</strong> functions.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-circle me-1"></i> Record New Expense
            </button>
            <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-speedometer2 me-1"></i> Finance Hub
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <strong class="text-danger">Unable to process expenditure request:</strong>
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Functional Allocation KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Academic Allocation Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #2563eb, #3b82f6);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Academic Functions</span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-bold px-2.5 py-1">
                            {{ $academicPercent }}%
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($academicTotal, 2) }}</h3>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                        <span><i class="bi bi-journal-bookmark me-1 text-primary"></i>Teaching, Labs & Faculty</span>
                        <span class="badge bg-light text-secondary border">{{ $academicCount }} items</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Administrative Allocation Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #7c3aed, #a855f7);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Administrative Functions</span>
                        <span class="badge rounded-pill bg-purple bg-opacity-10 text-purple fw-bold px-2.5 py-1" style="background-color: #f3e8ff; color: #7c3aed;">
                            {{ $adminPercent }}%
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($adminTotal, 2) }}</h3>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                        <span><i class="bi bi-building-gear me-1 text-purple" style="color: #7c3aed;"></i>Governance, HR & Systems</span>
                        <span class="badge bg-light text-secondary border">{{ $adminCount }} items</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Operational Allocation Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #0d9488, #14b8a6);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Operational Functions</span>
                        <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background-color: #ccfbf1; color: #0f766e;">
                            {{ $operationalPercent }}%
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($operationalTotal, 2) }}</h3>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                        <span><i class="bi bi-tools me-1 text-teal" style="color: #0d9488;"></i>Estates, Utilities & Fleet</span>
                        <span class="badge bg-light text-secondary border">{{ $operationalCount }} items</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Total Approved Allocation Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #10b981, #059669);"></div>
                <div class="card-body p-3 pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">Total Approved Spend</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2.5 py-1">Reconciled</span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($grandTotal, 2) }}</h3>
                    <!-- Proportional Bar -->
                    <div class="progress mt-2" style="height: 6px; border-radius: 999px; background-color: #e2e8f0;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $academicPercent }}%; background-color: #2563eb;" title="Academic {{ $academicPercent }}%"></div>
                        <div class="progress-bar" role="progressbar" style="width: {{ $adminPercent }}%; background-color: #7c3aed;" title="Administrative {{ $adminPercent }}%"></div>
                        <div class="progress-bar" role="progressbar" style="width: {{ $operationalPercent }}%; background-color: #0d9488;" title="Operational {{ $operationalPercent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.finance.expenses.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Function Filter Pills -->
                <div class="col-12 col-lg-4">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('admin.finance.expenses.index', array_merge(request()->except('classification', 'page'), ['classification' => 'all'])) }}" 
                           class="btn btn-sm {{ !request('classification') || request('classification') === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' }}">
                            All Functions
                        </a>
                        <a href="{{ route('admin.finance.expenses.index', array_merge(request()->except('classification', 'page'), ['classification' => 'academic'])) }}" 
                           class="btn btn-sm {{ request('classification') === 'academic' ? 'btn-primary fw-bold text-white' : 'btn-outline-primary' }}">
                            <i class="bi bi-mortarboard me-1"></i>Academic
                        </a>
                        <a href="{{ route('admin.finance.expenses.index', array_merge(request()->except('classification', 'page'), ['classification' => 'administrative'])) }}" 
                           class="btn btn-sm {{ request('classification') === 'administrative' ? 'btn-secondary fw-bold text-white' : 'btn-outline-secondary' }}"
                           style="{{ request('classification') === 'administrative' ? 'background-color: #7c3aed; border-color: #7c3aed;' : 'color: #7c3aed; border-color: #7c3aed;' }}">
                            <i class="bi bi-briefcase me-1"></i>Admin
                        </a>
                        <a href="{{ route('admin.finance.expenses.index', array_merge(request()->except('classification', 'page'), ['classification' => 'operational'])) }}" 
                           class="btn btn-sm {{ request('classification') === 'operational' ? 'btn-success fw-bold text-white' : 'btn-outline-success' }}"
                           style="{{ request('classification') === 'operational' ? 'background-color: #0d9488; border-color: #0d9488;' : 'color: #0d9488; border-color: #0d9488;' }}">
                            <i class="bi bi-gear-wide-connected me-1"></i>Ops
                        </a>
                    </div>
                </div>

                <!-- Department Selector -->
                <div class="col-sm-6 col-lg-3">
                    <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Departments & Cost Centers</option>
                        <optgroup label="Academic Departments">
                            @foreach($academicDepartments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Administrative Offices">
                            @foreach($adminDepartments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Operational & Estates Divisions">
                            @foreach($operationalDepartments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- Status Selector -->
                <div class="col-sm-6 col-lg-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Search Input & Reset -->
                <div class="col-12 col-lg-3">
                    <div class="input-group input-group-sm">
                        @if(request('classification'))
                            <input type="hidden" name="classification" value="{{ request('classification') }}">
                        @endif
                        <input type="text" name="search" class="form-control" placeholder="Search title, expense #..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request()->hasAny(['search', 'department_id', 'status', 'classification']))
                            <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenditures Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-3 py-3">Expense #</th>
                            <th class="py-3">Function Area</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Particulars / Description</th>
                            <th class="py-3">Department / Cost Center</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Status</th>
                            <th class="text-end pe-3 py-3">Amount</th>
                            <th class="text-end pe-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-dark">
                                    <a href="{{ route('admin.finance.expenses.show', $exp->id) }}" class="text-decoration-none text-primary">
                                        {{ $exp->expense_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($exp->classification === 'operational')
                                        <span class="badge rounded-pill fw-semibold px-2.5 py-1" style="background-color: #ccfbf1; color: #0f766e;">
                                            <i class="bi bi-tools me-1"></i>Operational
                                        </span>
                                    @elseif($exp->classification === 'administrative')
                                        <span class="badge rounded-pill fw-semibold px-2.5 py-1" style="background-color: #f3e8ff; color: #7c3aed;">
                                            <i class="bi bi-briefcase me-1"></i>Administrative
                                        </span>
                                    @else
                                        <span class="badge rounded-pill fw-semibold px-2.5 py-1 bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-mortarboard me-1"></i>Academic
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 small fw-normal">{{ $exp->category }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                    @if($exp->description)
                                        <small class="text-muted d-inline-block text-truncate" style="max-width: 260px;">{{ $exp->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $exp->department->name ?? 'General Department' }}</div>
                                    @if($exp->department)
                                        <small class="text-muted">
                                            Code: {{ $exp->department->code ?? 'N/A' }} 
                                            &bull; <span class="badge bg-secondary-subtle text-secondary py-0 px-1">{{ ucfirst($exp->department->type ?? 'academic') }}</span>
                                        </small>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $exp->expense_date ? $exp->expense_date->format('M d, Y') : '-' }}</td>
                                <td>
                                    @if($exp->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                            <i class="bi bi-check-circle me-1"></i>Approved
                                        </span>
                                    @elseif($exp->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                            <i class="bi bi-x-circle me-1"></i>Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3 fw-bold text-danger">
                                    {{ $currencyCode }} {{ number_format($exp->amount, 2) }}
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.finance.expenses.show', $exp->id) }}" class="btn btn-outline-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.finance.expenses.edit', $exp->id) }}" class="btn btn-outline-primary" title="Edit Expense">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($exp->status === 'pending')
                                            <form action="{{ route('admin.finance.expenses.approve', $exp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this expense and deduct from department budget?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Approve & Reconcile Budget"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.finance.expenses.destroy', $exp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        <h6 class="fw-bold">No Expenditure Records Found</h6>
                                        <p class="small mb-0">No expenses match your active filter criteria. Try resetting the filters or record a new expense.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Record New Expense with Functional Classification -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.finance.expenses.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Record Institutional Expenditure</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Functional Area -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Institutional Function <span class="text-danger">*</span></label>
                            <select name="classification" id="expenseClassificationSelect" class="form-select" required>
                                <option value="academic" {{ old('classification') === 'academic' ? 'selected' : '' }}>Academic (Instruction, Labs & Research)</option>
                                <option value="administrative" {{ old('classification') === 'administrative' ? 'selected' : '' }}>Administrative (Governance, HR, ICT & Audit)</option>
                                <option value="operational" {{ old('classification') === 'operational' ? 'selected' : '' }}>Operational (Estates, Security, Utilities & Fleet)</option>
                            </select>
                            <div class="form-text small text-muted">Ensures costs are correctly classified across institutional functional accounts.</div>
                        </div>

                        <!-- Department Selection with Grouping -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Department / Cost Center <span class="text-danger">*</span></label>
                            <select name="department_id" id="departmentSelect" class="form-select" required>
                                <option value="">-- Select Responsible Department --</option>
                                <optgroup label="Academic Departments" data-type="academic">
                                    @foreach($academicDepartments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }} ({{ $dept->code ?? 'ACA' }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Administrative Offices & Directorates" data-type="administrative">
                                    @foreach($adminDepartments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }} ({{ $dept->code ?? 'ADM' }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Operational & Estates Divisions" data-type="operational">
                                    @foreach($operationalDepartments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }} ({{ $dept->code ?? 'OPS' }})</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <!-- Expense Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Expenditure Category <span class="text-danger">*</span></label>
                            <select name="category" id="expenseCategorySelect" class="form-select" required>
                                @foreach($categoriesByFunction as $func => $cats)
                                    <optgroup label="{{ ucfirst($func) }} Categories" data-function="{{ $func }}">
                                        @foreach($cats as $cat)
                                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" value="{{ old('amount') }}" required>
                        </div>

                        <!-- Title / Particulars -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark">Particulars / Expenditure Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Campus Power Grid Utility Payment - October 2026" value="{{ old('title') }}" required>
                        </div>

                        <!-- Expense Date -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Approval & Status -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-primary"><i class="bi bi-shield-check me-1"></i>Approval & Budget Deduction Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select border-primary" required>
                                <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Approved (Immediately Deducts from Department Budget)</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Bursar Approval</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Detailed Description / Justification</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Provide background justification, vendor details, or requisition notes...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Confirm & Record Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-synchronize classification dropdown with department selection if applicable
        const classSelect = document.getElementById('expenseClassificationSelect');
        const deptSelect = document.getElementById('departmentSelect');

        if (classSelect && deptSelect) {
            deptSelect.addEventListener('change', function () {
                const selectedOption = deptSelect.options[deptSelect.selectedIndex];
                const optgroup = selectedOption.parentElement;
                if (optgroup && optgroup.dataset.type) {
                    classSelect.value = optgroup.dataset.type;
                }
            });
        }

        @if($errors->any())
            var modalEl = document.getElementById('addExpenseModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        @endif
    });
</script>
@endpush
@endsection
