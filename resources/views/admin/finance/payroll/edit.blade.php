@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Payroll Record</h2>
            <p class="text-muted mb-0">Update salary details for {{ $payroll->month_year }}</p>
        </div>
        <a href="{{ route('admin.finance.payroll.index') }}" class="btn btn-outline-secondary">Back to Payroll</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.finance.payroll.update', $payroll) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Month/Year</label>
                        <input type="text" name="month_year" class="form-control" value="{{ old('month_year', $payroll->month_year) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', optional(is_string($payroll->payment_date) ? \Carbon\Carbon::parse($payroll->payment_date) : $payroll->payment_date)->format('Y-m-d') ?? '') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Basic Salary</label>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', $payroll->basic_salary) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Total Allowances</label>
                        <input type="number" step="0.01" name="total_allowances" class="form-control" value="{{ old('total_allowances', $payroll->total_allowances) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gross Salary</label>
                        <input type="number" step="0.01" name="gross_salary" class="form-control" value="{{ old('gross_salary', $payroll->gross_salary) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tax Deductions</label>
                        <input type="number" step="0.01" name="tax_deductions" class="form-control" value="{{ old('tax_deductions', $payroll->tax_deductions) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pension Deductions</label>
                        <input type="number" step="0.01" name="pension_deductions" class="form-control" value="{{ old('pension_deductions', $payroll->pension_deductions) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Net Salary</label>
                        <input type="number" step="0.01" name="net_salary" class="form-control" value="{{ old('net_salary', $payroll->net_salary) }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Pending" {{ old('status', $payroll->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Processed" {{ old('status', $payroll->status) === 'Processed' ? 'selected' : '' }}>Processed</option>
                            <option value="Paid" {{ old('status', $payroll->status) === 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.finance.payroll.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
