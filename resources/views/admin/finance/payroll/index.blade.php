@extends('layouts.app')

@section('title', 'Payroll & Staff Salaries')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-person-badge me-2"></i>Payroll & Salary Management
            </h1>
            <p class="text-muted mb-0">Generate staff salaries, PAYE tax deductions, NSSF pension contributions & net payroll</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
                <i class="bi bi-gear me-1"></i>Generate Monthly Payroll
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
                            <th class="ps-3">Staff Name</th>
                            <th>Month / Year</th>
                            <th>Basic Salary</th>
                            <th>Allowances</th>
                            <th>PAYE Tax (10%)</th>
                            <th>NSSF Pension (5%)</th>
                            <th class="text-end pe-3">Net Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $pr)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $pr->user->full_name ?? 'University Staff' }}</td>
                                <td class="fw-bold text-primary">{{ $pr->month_year }}</td>
                                <td>{{ $currencyCode }} {{ number_format($pr->basic_salary, 2) }}</td>
                                <td>{{ $currencyCode }} {{ number_format($pr->total_allowances, 2) }}</td>
                                <td class="text-danger">{{ $currencyCode }} {{ number_format($pr->tax_deductions, 2) }}</td>
                                <td class="text-danger">{{ $currencyCode }} {{ number_format($pr->pension_deductions, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-success fs-6">{{ $currencyCode }} {{ number_format($pr->net_salary, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No payroll records generated yet. Click "Generate Monthly Payroll" above.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payrolls->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $payrolls->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="generatePayrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.finance.payroll.generate') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-gear me-2"></i>Generate Monthly Payroll</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payroll Period (Month & Year) <span class="text-danger">*</span></label>
                        <input type="text" name="month_year" class="form-control" value="{{ date('F Y') }}" required>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i>This will compute basic salaries, allowances, 10% PAYE tax, and 5% NSSF contributions for all active staff ({{ $staffCount }} staff members).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Compute Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
