@extends('layouts.app')

@section('title', 'Edit Expense Requisition')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Expense: {{ $expense->expense_number }}</h5>
            <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">Back to List</a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.finance.expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $expense->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Supplies & Stationery" {{ $expense->category == 'Supplies & Stationery' ? 'selected' : '' }}>Supplies & Stationery</option>
                            <option value="Equipment & Repairs" {{ $expense->category == 'Equipment & Repairs' ? 'selected' : '' }}>Equipment & Repairs</option>
                            <option value="Utilities & Internet" {{ $expense->category == 'Utilities & Internet' ? 'selected' : '' }}>Utilities & Internet</option>
                            <option value="Travel & Fuel" {{ $expense->category == 'Travel & Fuel' ? 'selected' : '' }}>Travel & Fuel</option>
                            <option value="Miscellaneous" {{ $expense->category == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Title / Item Description <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $expense->title }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->amount }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '' }}" required>
                    </div>
                    
                    @if(auth()->user()->can('expenses,approve') || auth()->user()->hasRole('super_administrator'))
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary"><i class="bi bi-shield-lock me-1"></i>Approval Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select border-primary" required>
                                <option value="pending" {{ $expense->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $expense->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $expense->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <div class="small text-muted mt-1">Changing status to Approved will deduct the amount from the department's budget.</div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($expense->status === 'approved')
                                    <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                @elseif($expense->status === 'rejected')
                                    <span class="badge bg-danger px-3 py-2 fs-6"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="bi bi-hourglass me-1"></i>Pending</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description / Notes</label>
                        <textarea name="description" class="form-control" rows="3">{{ $expense->description }}</textarea>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-save me-1"></i> Update Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
