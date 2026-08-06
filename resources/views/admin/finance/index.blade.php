@extends('layouts.app')

@section('title', 'University Bursar & Finance Hub')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Executive Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-bank me-2"></i>University Finance & Bursar Hub
            </h1>
            <p class="text-muted mb-0">Executive financial dashboard, budget utilization, expenditures, revenue streams & statutory compliance</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-primary fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Manage Fee Structures
            </a>
            <a href="{{ route('admin.fees.index') }}" class="btn btn-primary fw-bold">
                <i class="bi bi-receipt me-1"></i>Student Fee Records
            </a>
        </div>
    </div>

    <!-- Financial KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Tuition Collected -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small" style="font-size: 0.75rem;">Tuition Fees Collected</span>
                        <div class="p-2 rounded bg-success bg-opacity-10 text-success">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($totalTuitionCollected, 2) }}</h3>
                    <small class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>Paid Student Fees</small>
                </div>
            </div>
        </div>

        <!-- Outstanding Student Balances -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-danger">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small" style="font-size: 0.75rem;">Outstanding Fee Balances</span>
                        <div class="p-2 rounded bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($totalOutstandingFees, 2) }}</h3>
                    <small class="text-danger fw-semibold"><i class="bi bi-clock-history me-1"></i>Accounts Receivable</small>
                </div>
            </div>
        </div>

        <!-- Other Revenues (Grants, Donations, Rentals) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small" style="font-size: 0.75rem;">Other Revenues & Grants</span>
                        <div class="p-2 rounded bg-info bg-opacity-10 text-info">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($totalOtherRevenue, 2) }}</h3>
                    <small class="text-info fw-semibold"><i class="bi bi-building-up me-1"></i>Non-tuition Income</small>
                </div>
            </div>
        </div>

        <!-- Total Expenditures -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted text-uppercase fw-bold small" style="font-size: 0.75rem;">Operational Expenditures</span>
                        <div class="p-2 rounded bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $currencyCode }} {{ number_format($totalExpenses, 2) }}</h3>
                    <small class="text-warning fw-semibold"><i class="bi bi-receipt-cutoff me-1"></i>Approved Expenses</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Sub-Modules Grid -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-grid-3x3-gap me-2"></i>Finance System Modules</h5>
        </div>
        <div class="card-body p-3">
            <div class="row g-3 text-center">
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-spreadsheet fs-2 mb-2"></i>
                        <span class="fw-bold small">Fee Structures</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.revenue.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-currency-exchange fs-2 mb-2"></i>
                        <span class="fw-bold small">Revenue & Income</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.budgets.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-pie-chart fs-2 mb-2"></i>
                        <span class="fw-bold small">Budget Allocations</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-cart-check fs-2 mb-2"></i>
                        <span class="fw-bold small">Expenditures</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.payables.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-truck fs-2 mb-2"></i>
                        <span class="fw-bold small">Accounts Payable</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.receivables.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-person-lines-fill fs-2 mb-2"></i>
                        <span class="fw-bold small">Accounts Receivable</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.payroll.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-person-badge fs-2 mb-2"></i>
                        <span class="fw-bold small">Payroll Management</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-qr-code-scan fs-2 mb-2"></i>
                        <span class="fw-bold small">Asset Management</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.banking.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-piggy-bank fs-2 mb-2"></i>
                        <span class="fw-bold small">Banking & Cash</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.grants.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-journal-medical fs-2 mb-2"></i>
                        <span class="fw-bold small">Research Grants</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.reports.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-journal-text fs-2 mb-2"></i>
                        <span class="fw-bold small">Financial Statements</span>
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="{{ route('admin.finance.audit.index') }}" class="btn btn-outline-primary w-100 p-3 rounded-3 h-100 text-decoration-none d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-shield-check fs-2 mb-2"></i>
                        <span class="fw-bold small">Audit & Compliance</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Financial Transactions & Budget Allocations -->
    <div class="row g-4">
        <!-- Bank Accounts & Balances -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-bank me-2 text-primary"></i>University Bank Accounts</h5>
                    <a href="{{ route('admin.finance.banking.index') }}" class="btn btn-sm btn-outline-primary">Manage Accounts</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Bank Name</th>
                                    <th>Account Number</th>
                                    <th>Branch</th>
                                    <th class="text-end pe-3">Current Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bankAccounts as $acc)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $acc->bank_name }}</td>
                                        <td class="font-monospace">{{ $acc->account_number }}</td>
                                        <td class="small text-muted">{{ $acc->branch ?? 'Main Branch' }}</td>
                                        <td class="text-end pe-3 fw-bold text-success">{{ $currencyCode }} {{ number_format($acc->current_balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No bank accounts registered.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Budget Allocations & Utilization -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Departmental Budget Utilization</h5>
                    <a href="{{ route('admin.finance.budgets.index') }}" class="btn btn-sm btn-outline-primary">View All Budgets</a>
                </div>
                <div class="card-body p-3">
                    @forelse($budgets->take(4) as $bdg)
                        @php
                            $perc = $bdg->allocated_amount > 0 ? min(100, round(($bdg->spent_amount / $bdg->allocated_amount) * 100)) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">{{ $bdg->department->name ?? 'General Department' }}</span>
                                <span class="small fw-semibold text-muted">{{ $currencyCode }} {{ number_format($bdg->spent_amount, 2) }} / {{ number_format($bdg->allocated_amount, 2) }} ({{ $perc }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $perc > 85 ? 'bg-danger' : ($perc > 60 ? 'bg-warning' : 'bg-success') }}" role="progressbar" style="width: {{ $perc }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">No departmental budget allocations set.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
