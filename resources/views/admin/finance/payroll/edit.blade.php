@extends('layouts.app')

@section('title', 'Edit Payroll - ' . ($payroll->user->full_name ?? 'Staff'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Payroll Record</h2>
            <p class="text-muted mb-0">Update staff salary details and deductions for <strong>{{ $payroll->month_year }}</strong></p>
        </div>
        <a href="{{ route('admin.finance.payroll.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Payroll
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="max-width: 900px;">
        <div class="card-body p-4">
            <!-- Staff & Job Role Info Card -->
            <div class="card bg-light border-0 p-3 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="small text-muted text-uppercase fw-semibold">Employee Details</div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-person-badge text-primary me-2"></i>{{ $payroll->user->full_name ?? $payroll->user->name ?? 'University Staff' }}
                        </h5>
                        <div class="text-muted small">
                            <span>Email: {{ $payroll->user->email ?? 'N/A' }}</span>
                            @if($payroll->user->hrProfile?->department)
                                <span class="mx-2">|</span>
                                <span>Dept: <strong>{{ $payroll->user->hrProfile->department }}</strong></span>
                            @endif
                        </div>
                    </div>

                    <!-- Job Role & Salary Band Reflection -->
                    <div class="text-md-end">
                        <div class="small text-muted text-uppercase fw-semibold">HR Job Role & Salary Band</div>
                        @if($jobRole)
                            <div class="fw-bold text-primary fs-6 mb-1">
                                <i class="bi bi-award me-1"></i>{{ $jobRole->title }}
                            </div>
                            @if($jobRole->salary_band_min || $jobRole->salary_band_max)
                                <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fs-6">
                                    Band: {{ $currencyCode }} {{ number_format($jobRole->salary_band_min, 0) }} - {{ number_format($jobRole->salary_band_max, 0) }}
                                </div>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Salary Band Not Configured</span>
                            @endif
                        @else
                            <div class="text-muted small">No specific HR job role associated</div>
                        @endif
                    </div>
                </div>

                @if($jobRole && ($jobRole->salary_band_min || $jobRole->salary_band_max))
                    <div class="border-top pt-2 mt-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <span class="small text-muted">
                            <i class="bi bi-info-circle me-1"></i>Quickly apply the HR salary band bounds to this payroll record:
                        </span>
                        <div class="btn-group btn-group-sm">
                            @if($jobRole->salary_band_min)
                                <button type="button" class="btn btn-outline-primary bg-white" 
                                        onclick="applySalary('{{ $jobRole->salary_band_min }}')">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Apply Min Band ({{ $currencyCode }} {{ number_format($jobRole->salary_band_min, 0) }})
                                </button>
                            @endif
                            @if($jobRole->salary_band_max)
                                <button type="button" class="btn btn-outline-primary bg-white" 
                                        onclick="applySalary('{{ $jobRole->salary_band_max }}')">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Apply Max Band ({{ $currencyCode }} {{ number_format($jobRole->salary_band_max, 0) }})
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <form action="{{ route('admin.finance.payroll.update', $payroll->id) }}" method="POST" id="payrollEditForm">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Month & Year Period</label>
                        <input type="text" name="month_year" class="form-control" value="{{ old('month_year', $payroll->month_year) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" 
                               value="{{ old('payment_date', optional(is_string($payroll->payment_date) ? \Carbon\Carbon::parse($payroll->payment_date) : $payroll->payment_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Basic Salary ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="basic_salary" id="basic_salary" 
                                   class="form-control form-control-lg fw-bold text-dark @error('basic_salary') is-invalid @enderror" 
                                   value="{{ old('basic_salary', $payroll->basic_salary) }}" required>
                        </div>
                        @error('basic_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Total Allowances ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="total_allowances" id="total_allowances" 
                                   class="form-control form-control-lg @error('total_allowances') is-invalid @enderror" 
                                   value="{{ old('total_allowances', $payroll->total_allowances) }}" required>
                        </div>
                        @error('total_allowances') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gross Salary ({{ $currencyCode }})</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">$</span>
                            <input type="number" step="0.01" min="0" name="gross_salary" id="gross_salary" 
                                   class="form-control form-control-lg bg-light" 
                                   value="{{ old('gross_salary', $payroll->gross_salary) }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">PAYE Tax Deductions ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="tax_deductions" id="tax_deductions" 
                                   class="form-control @error('tax_deductions') is-invalid @enderror" 
                                   value="{{ old('tax_deductions', $payroll->tax_deductions) }}" required>
                        </div>
                        @error('tax_deductions') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">NSSF Pension ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="pension_deductions" id="pension_deductions" 
                                   class="form-control @error('pension_deductions') is-invalid @enderror" 
                                   value="{{ old('pension_deductions', $payroll->pension_deductions) }}" required>
                        </div>
                        @error('pension_deductions') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">Net Salary ({{ $currencyCode }})</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary fw-bold">$</span>
                            <input type="number" step="0.01" name="net_salary" id="net_salary" 
                                   class="form-control bg-light fw-bold text-primary" 
                                   value="{{ old('net_salary', $payroll->net_salary) }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('admin.finance.payroll.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculatePayroll() {
    const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
    const allowances = parseFloat(document.getElementById('total_allowances').value) || 0;
    const gross = basic + allowances;
    
    document.getElementById('gross_salary').value = gross.toFixed(2);
    
    // Auto-calculate 10% PAYE and 5% Pension if not manually overridden
    const taxInput = document.getElementById('tax_deductions');
    const pensionInput = document.getElementById('pension_deductions');
    
    const tax = gross * 0.10;
    const pension = gross * 0.05;
    
    taxInput.value = tax.toFixed(2);
    pensionInput.value = pension.toFixed(2);
    
    const net = gross - tax - pension;
    document.getElementById('net_salary').value = net.toFixed(2);
}

function applySalary(amount) {
    document.getElementById('basic_salary').value = parseFloat(amount).toFixed(2);
    calculatePayroll();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('basic_salary').addEventListener('input', calculatePayroll);
    document.getElementById('total_allowances').addEventListener('input', calculatePayroll);
    
    document.getElementById('tax_deductions').addEventListener('input', function() {
        const gross = parseFloat(document.getElementById('gross_salary').value) || 0;
        const tax = parseFloat(this.value) || 0;
        const pension = parseFloat(document.getElementById('pension_deductions').value) || 0;
        document.getElementById('net_salary').value = (gross - tax - pension).toFixed(2);
    });
    
    document.getElementById('pension_deductions').addEventListener('input', function() {
        const gross = parseFloat(document.getElementById('gross_salary').value) || 0;
        const tax = parseFloat(document.getElementById('tax_deductions').value) || 0;
        const pension = parseFloat(this.value) || 0;
        document.getElementById('net_salary').value = (gross - tax - pension).toFixed(2);
    });
});
</script>
@endsection
