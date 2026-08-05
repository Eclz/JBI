@extends('layouts.app')

@section('title', 'My Transactions')

@section('content')
<div class="container-fluid px-4 py-4">
    @if(Auth::user()->isStudent())
        @include('partials.student-header-bar')
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-file-earmark-check text-primary me-2"></i>MY TRANSACTIONS & FINANCIAL STATEMENT
            </h5>
            <p class="text-muted small mb-0">All tuition fees, functional fees, retakes, and missed paper exam fee transactions</p>
        </div>
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-receipt me-1"></i>View Bills & Invoices
        </a>
    </div>

    <!-- Financial Overview Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-primary border-4 rounded-3">
                <small class="text-muted text-uppercase fw-semibold d-block mb-1">Total Fee Transactions</small>
                <h4 class="fw-bold text-primary mb-0">UGX {{ number_format($feeRecords->sum('total_amount') > 0 ? $feeRecords->sum('total_amount') : $feeRecords->sum('amount'), 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-success border-4 rounded-3">
                <small class="text-muted text-uppercase fw-semibold d-block mb-1">Total Paid To Date</small>
                <h4 class="fw-bold text-success mb-0">UGX {{ number_format($feeRecords->sum('paid_amount'), 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4 rounded-3">
                <small class="text-muted text-uppercase fw-semibold d-block mb-1">Outstanding Balance Due</small>
                <h4 class="fw-bold text-danger mb-0">UGX {{ number_format($feeRecords->sum('balance_amount'), 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Fee Records & Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-clock-history me-2"></i>ITEMIZED FEE TRANSACTIONS</h6>
            <span class="badge bg-primary px-3 py-1">{{ $feeRecords->count() }} RECORDS</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th>REF / INVOICE NO</th>
                            <th>FEE CATEGORY & DESCRIPTION</th>
                            <th>TOTAL AMOUNT</th>
                            <th>PAID AMOUNT</th>
                            <th>BALANCE DUE</th>
                            <th>DATE GENERATED</th>
                            <th class="text-end">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeRecords as $record)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">{{ $record->invoice_number ?? 'INV-'.$record->id }}</span>
                                </td>
                                <td>
                                    @if($record->type === 'retake_fee')
                                        <span class="badge bg-warning text-dark me-1"><i class="bi bi-arrow-repeat me-1"></i>RETAKE FEE</span>
                                    @elseif($record->type === 'missed_paper_fee')
                                        <span class="badge bg-danger me-1"><i class="bi bi-exclamation-triangle me-1"></i>MISSED PAPER FEE</span>
                                    @elseif($record->type === 'registration' || str_contains(strtolower($record->payment_notes ?? ''), 'functional'))
                                        <span class="badge bg-info me-1"><i class="bi bi-gear me-1"></i>FUNCTIONAL FEE</span>
                                    @else
                                        <span class="badge bg-primary me-1"><i class="bi bi-book me-1"></i>TUITION FEE</span>
                                    @endif
                                    <span class="fw-semibold text-dark">{{ $record->payment_notes ?? $record->feeStructure?->name ?? 'Semester Tuition Fee' }}</span>
                                </td>
                                <td class="fw-bold">UGX {{ number_format($record->total_amount > 0 ? $record->total_amount : $record->amount, 2) }}</td>
                                <td class="fw-bold text-success">UGX {{ number_format($record->paid_amount, 2) }}</td>
                                <td class="fw-bold text-danger">UGX {{ number_format($record->balance_amount, 2) }}</td>
                                <td>{{ $record->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    @if($record->status === 'paid' || $record->balance_amount <= 0)
                                        <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>PAID</span>
                                    @elseif($record->paid_amount > 0)
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-pie-chart me-1"></i>PARTIAL</span>
                                    @else
                                        <span class="badge bg-danger px-2 py-1"><i class="bi bi-clock me-1"></i>UNPAID</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-2 text-muted opacity-50"></i>
                                    No fee transaction records found for your account.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
