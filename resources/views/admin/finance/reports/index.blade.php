@extends('layouts.app')

@section('title', 'Financial Statements & General Ledger')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-journal-text me-2"></i>Financial Statements & General Ledger
            </h1>
            <p class="text-muted mb-0">Executive income statement, total revenue streams, operating expenditures & net surplus calculation</p>
        </div>
        <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary">Back to Finance Hub</a>
    </div>

    <!-- Income Statement Summary -->
    <div class="row g-4 mb-4">
        <!-- Revenues Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>University Total Revenue Streams</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold text-dark">Tuition & Student Fees Collected:</span>
                        <span class="fw-bold text-dark fs-6">{{ $currencyCode }} {{ number_format($tuitionRevenue, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold text-dark">Non-Tuition Revenues & Grants:</span>
                        <span class="fw-bold text-dark fs-6">{{ $currencyCode }} {{ number_format($otherRevenue, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-success mb-0">Total Consolidated Revenue:</h5>
                        <h4 class="fw-bold text-success mb-0">{{ $currencyCode }} {{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenditures Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cart-x me-2"></i>Consolidated Operational Expenditures</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold text-dark">Approved Operational Expenses:</span>
                        <span class="fw-bold text-dark fs-6">{{ $currencyCode }} {{ number_format($operatingExpenses, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold text-dark">Staff Net Payroll Expenses:</span>
                        <span class="fw-bold text-dark fs-6">{{ $currencyCode }} {{ number_format($payrollExpenses, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-danger mb-0">Total Expenditures:</h5>
                        <h4 class="fw-bold text-danger mb-0">{{ $currencyCode }} {{ number_format($totalExpenses, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Financial Position Banner -->
    <div class="card border-0 shadow-sm rounded-3 bg-white border-start border-5 {{ $netSurplus >= 0 ? 'border-success' : 'border-danger' }}">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <span class="text-uppercase fw-bold text-muted small d-block mb-1">CONSOLIDATED NET FINANCIAL POSITION</span>
                <h3 class="fw-bold text-dark mb-0">{{ $netSurplus >= 0 ? 'Net Surplus / Profit' : 'Net Deficit' }}</h3>
            </div>
            <div>
                <h2 class="fw-bold mb-0 {{ $netSurplus >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $currencyCode }} {{ number_format(abs($netSurplus), 2) }}
                </h2>
            </div>
        </div>
    </div>
</div>
@endsection
