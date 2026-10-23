@extends('layouts.app')

@section('title', 'Edit Expense Requisition & Allocation')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title fw-bold text-primary mb-0">
                    <i class="bi bi-pencil-square me-2"></i>Edit Expense Requisition: {{ $expense->expense_number }}
                </h5>
                <small class="text-muted">Modify expenditure particulars, department attribution, and institutional functional classification</small>
            </div>
            <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                        <strong class="text-danger">Unable to update expense:</strong>
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.finance.expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <!-- Institutional Function -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Institutional Function <span class="text-danger">*</span></label>
                        <select name="classification" id="expenseClassificationSelect" class="form-select" required>
                            <option value="academic" {{ old('classification', $expense->classification) === 'academic' ? 'selected' : '' }}>Academic (Instruction, Labs & Research)</option>
                            <option value="administrative" {{ old('classification', $expense->classification) === 'administrative' ? 'selected' : '' }}>Administrative (Governance, HR, ICT & Audit)</option>
                            <option value="operational" {{ old('classification', $expense->classification) === 'operational' ? 'selected' : '' }}>Operational (Estates, Security, Utilities & Fleet)</option>
                        </select>
                        <div class="form-text small text-muted">Functional account category for institutional budgeting.</div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Department / Cost Center <span class="text-danger">*</span></label>
                        <select name="department_id" id="departmentSelect" class="form-select" required>
                            <option value="">-- Select Department --</option>
                            <optgroup label="Academic Departments" data-type="academic">
                                @foreach($academicDepartments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $expense->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->code ?? 'ACA' }})
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Administrative Offices" data-type="administrative">
                                @foreach($adminDepartments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $expense->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->code ?? 'ADM' }})
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Operational & Estates Divisions" data-type="operational">
                                @foreach($operationalDepartments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $expense->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->code ?? 'OPS' }})
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    
                    <!-- Expense Category -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Expense Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            @foreach($categoriesByFunction as $func => $cats)
                                <optgroup label="{{ ucfirst($func) }} Categories">
                                    @foreach($cats as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $expense->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                    </div>

                    <!-- Title -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-dark">Title / Item Description <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $expense->title) }}" required>
                    </div>

                    <!-- Expense Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}" required>
                        @error('expense_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Status & Approval -->
                    @if(auth()->user()->can('expenses,approve') || auth()->user()->hasRole('super_administrator'))
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary"><i class="bi bi-shield-lock me-1"></i>Approval & Reconciliation Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select border-primary" required>
                                <option value="pending" {{ old('status', $expense->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $expense->status) === 'approved' ? 'selected' : '' }}>Approved (Reconciles Department Budget)</option>
                                <option value="rejected" {{ old('status', $expense->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Approval Status</label>
                            <div>
                                @if($expense->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i>Approved
                                    </span>
                                @elseif($expense->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-dark px-3 py-2 fs-6 rounded-pill">
                                        <i class="bi bi-hourglass me-1"></i>Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Description / Justification Notes</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description) }}</textarea>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Save Changes & Reconcile Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>
@endpush
@endsection
