@extends('layouts.app')

@section('title', 'Edit Department Budget')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Budget Allocation: {{ $budget->budget_code ?? 'BGT-'.str_pad($budget->id, 5, '0', STR_PAD_LEFT) }}</h5>
            <a href="{{ route('admin.finance.budgets.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">Back to List</a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.finance.budgets.update', $budget->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control" value="{{ $budget->academic_year }}" placeholder="e.g. 2026/2027" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $budget->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Allocated Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="allocated_amount" class="form-control" value="{{ $budget->allocated_amount }}" required>
                        <div class="small text-muted mt-1">Total approved funds for this department for the academic year.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Utilization Stats</label>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Amount Spent:</span>
                                <span class="fw-bold text-danger">{{ $currencyCode }} {{ number_format($budget->spent_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Amount Committed:</span>
                                <span class="fw-bold text-warning">{{ $currencyCode }} {{ number_format($budget->committed_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-save me-1"></i> Update Budget</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
