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
                            <th>Net Salary</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $pr)
                            @php
                                $staffUser = $pr->user;
                                $jobRole = null;
                                if ($staffUser) {
                                    if ($staffUser->role_id) {
                                        $jobRole = \App\Models\HrJobRole::where('role_id', $staffUser->role_id)->first();
                                    }
                                    if (!$jobRole && $staffUser->hrProfile?->salary_band) {
                                        $jobRole = \App\Models\HrJobRole::where('title', $staffUser->hrProfile->salary_band)->first();
                                    }
                                    if (!$jobRole && $staffUser->hrProfile?->job_title) {
                                        $jobRole = \App\Models\HrJobRole::where('title', $staffUser->hrProfile->job_title)->first();
                                    }
                                    if (!$jobRole && $staffUser->roleCatalog) {
                                        $jobRole = \App\Models\HrJobRole::where('title', $staffUser->roleCatalog->name)->first();
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $pr->user->full_name ?? $pr->user->name ?? 'University Staff' }}</div>
                                    @if($jobRole)
                                        <div class="small mt-1">
                                            <span class="badge bg-primary text-white">{{ $jobRole->title }}</span>
                                            @if($jobRole->salary_band_min || $jobRole->salary_band_max)
                                                <span class="text-muted small ms-1">({{ $currencyCode }} {{ number_format($jobRole->salary_band_min, 0) }} - {{ number_format($jobRole->salary_band_max, 0) }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold text-primary">{{ $pr->month_year }}</td>
                                <td>{{ $currencyCode }} {{ number_format($pr->basic_salary, 2) }}</td>
                                <td>{{ $currencyCode }} {{ number_format($pr->total_allowances, 2) }}</td>
                                <td class="text-danger">{{ $currencyCode }} {{ number_format($pr->tax_deductions, 2) }}</td>
                                <td class="text-danger">{{ $currencyCode }} {{ number_format($pr->pension_deductions, 2) }}</td>
                                <td class="fw-bold text-success fs-6">{{ $currencyCode }} {{ number_format($pr->net_salary, 2) }}</td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#showPayrollModal{{ $pr->id }}" title="View"><i class="bi bi-eye"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPayrollModal{{ $pr->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                        <form action="{{ route('admin.finance.payroll.destroy', $pr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payroll record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No payroll records generated yet. Click "Generate Monthly Payroll" above.</td>
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
@foreach($payrolls as $pr)
    @php
        $staffUser = $pr->user;
        $jobRole = null;
        if ($staffUser) {
            if ($staffUser->role_id) {
                $jobRole = \App\Models\HrJobRole::where('role_id', $staffUser->role_id)->first();
            }
            if (!$jobRole && $staffUser->hrProfile?->salary_band) {
                $jobRole = \App\Models\HrJobRole::where('title', $staffUser->hrProfile->salary_band)->first();
            }
            if (!$jobRole && $staffUser->hrProfile?->job_title) {
                $jobRole = \App\Models\HrJobRole::where('title', $staffUser->hrProfile->job_title)->first();
            }
            if (!$jobRole && $staffUser->roleCatalog) {
                $jobRole = \App\Models\HrJobRole::where('title', $staffUser->roleCatalog->name)->first();
            }
        }
    @endphp

    <!-- Show Modal for {{ $pr->id }} -->
    <div class="modal fade" id="showPayrollModal{{ $pr->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Payroll Record Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted fw-semibold">Staff Name</span>
                            <span class="fw-bold">{{ $pr->user->full_name ?? 'University Staff' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted fw-semibold">Month / Year</span>
                            <span class="badge bg-primary px-3 py-2 fs-6">{{ $pr->month_year }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted fw-semibold">Basic Salary</span>
                            <span class="fw-bold">{{ $currencyCode }} {{ number_format($pr->basic_salary, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted fw-semibold">Total Allowances</span>
                            <span class="fw-bold text-success">+ {{ $currencyCode }} {{ number_format($pr->total_allowances, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted fw-semibold">PAYE Tax Deductions (10%)</span>
                            <span class="fw-bold text-danger">- {{ $currencyCode }} {{ number_format($pr->tax_deductions, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted fw-semibold">NSSF Pension (5%)</span>
                            <span class="fw-bold text-danger">- {{ $currencyCode }} {{ number_format($pr->pension_deductions, 2) }}</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                    <span class="text-uppercase fw-bold text-muted small">Net Salary</span>
                    <h4 class="mb-0 fw-bold text-success">{{ $currencyCode }} {{ number_format($pr->net_salary, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal for {{ $pr->id }} -->
    <div class="modal fade" id="editPayrollModal{{ $pr->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.finance.payroll.update', $pr->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Payroll: {{ $pr->user->full_name ?? 'University Staff' }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($jobRole)
                            <div class="alert alert-info py-2 px-3 mb-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong><i class="bi bi-award me-1"></i>Job Role:</strong> {{ $jobRole->title }}
                                </div>
                                <div class="mb-2">
                                    <strong>Salary Band:</strong>
                                    @if($jobRole->salary_band_min || $jobRole->salary_band_max)
                                        <span class="badge bg-primary text-white ms-1">
                                            {{ $currencyCode }} {{ number_format($jobRole->salary_band_min, 0) }} - {{ number_format($jobRole->salary_band_max, 0) }}
                                        </span>
                                    @else
                                        <span class="text-muted ms-1">Not configured</span>
                                    @endif
                                </div>
                                @if($jobRole->salary_band_min || $jobRole->salary_band_max)
                                    <div class="btn-group btn-group-sm w-100 mt-1">
                                        @if($jobRole->salary_band_min)
                                            <button type="button" class="btn btn-outline-primary bg-white py-1"
                                                    onclick="document.getElementById('modal_basic_salary_{{ $pr->id }}').value = '{{ $jobRole->salary_band_min }}'">
                                                Apply Min ({{ $currencyCode }} {{ number_format($jobRole->salary_band_min, 0) }})
                                            </button>
                                        @endif
                                        @if($jobRole->salary_band_max)
                                            <button type="button" class="btn btn-outline-primary bg-white py-1"
                                                    onclick="document.getElementById('modal_basic_salary_{{ $pr->id }}').value = '{{ $jobRole->salary_band_max }}'">
                                                Apply Max ({{ $currencyCode }} {{ number_format($jobRole->salary_band_max, 0) }})
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Basic Salary ({{ $currencyCode }})</label>
                            <input type="number" step="0.01" name="basic_salary" id="modal_basic_salary_{{ $pr->id }}" class="form-control" value="{{ $pr->basic_salary }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Total Allowances ({{ $currencyCode }})</label>
                            <input type="number" step="0.01" name="total_allowances" class="form-control" value="{{ $pr->total_allowances }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">PAYE Tax ({{ $currencyCode }})</label>
                                <input type="number" step="0.01" name="tax_deductions" class="form-control" value="{{ $pr->tax_deductions }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NSSF Pension ({{ $currencyCode }})</label>
                                <input type="number" step="0.01" name="pension_deductions" class="form-control" value="{{ $pr->pension_deductions }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
