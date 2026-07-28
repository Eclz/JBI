@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header section -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2" style="color: #212529; font-weight: 700; font-size: 1.75rem;">Fee Payments</h2>
                <p class="mb-0" style="color: #6c757d;">Manage your tuition and fee payments</p>
            </div>
            @if($firstPayable)
                <a href="{{ route('student.fees.pay', $firstPayable) }}" class="btn btn-outline-primary">
                    <i class="bi bi-credit-card me-1"></i> Make Payment
                </a>
            @else
                <button type="button" class="btn btn-outline-secondary" disabled>
                    <i class="bi bi-check-circle me-1"></i> No Payments Due
                </button>
            @endif
        </div>
    </div>

    <!-- Fee summary cards -->
    <div class="row g-3 mb-4">
        <!-- Total Fees Card -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Total Fees
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ $currencyCode }} {{ number_format($summary['total_fees'] ?? 0, 2) }}
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">total amount due</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-cash-stack fs-4" style="color: #0d6efd;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paid Amount Card -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Paid Amount
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ $currencyCode }} {{ number_format($summary['paid_amount'] ?? 0, 2) }}
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">amount paid</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-check-circle fs-4" style="color: #198754;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outstanding Balance Card -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-uppercase small fw-semibold" style="color: #6c757d; font-size: 0.75rem; letter-spacing: 0.5px;">
                                Outstanding Balance
                            </p>
                            <h3 class="mb-0" style="color: #212529; font-weight: 700; font-size: 2rem;">
                                {{ $currencyCode }} {{ number_format($summary['outstanding'] ?? 0, 2) }}
                            </h3>
                            <p class="mb-0 mt-1 small" style="color: #6c757d;">remaining balance</p>
                        </div>
                        <div class="p-3 rounded" style="background-color: rgba(220, 53, 69, 0.1);">
                            <i class="bi bi-exclamation-triangle fs-4" style="color: #dc3545;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment progress bar -->
    @if(($summary['total_fees'] ?? 0) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0" style="color: #212529; font-weight: 600;">Payment Progress</h6>
                        <span class="badge bg-primary">{{ number_format((($summary['paid_amount'] ?? 0) / ($summary['total_fees'] ?? 1)) * 100, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ (($summary['paid_amount'] ?? 0) / ($summary['total_fees'] ?? 1)) * 100 }}%"
                             aria-valuenow="{{ (($summary['paid_amount'] ?? 0) / ($summary['total_fees'] ?? 1)) * 100 }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Semester Balances</h5>
                </div>
                <div class="card-body p-0">
                    @if($semesterBalances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Semester</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Total</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Paid</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($semesterBalances as $semester => $totals)
                                <tr>
                                    <td class="px-4 py-3" style="color: #212529;">{{ $semester }}</td>
                                    <td class="px-4 py-3" style="color: #212529; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($totals['total'], 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #198754; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($totals['paid'], 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #dc3545; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($totals['balance'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-calendar2-week display-6" style="color: #dee2e6;"></i>
                        <h6 class="mt-3" style="color: #6c757d;">No semester balances yet</h6>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Fee records table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Fee Records</h5>
                </div>
                <div class="card-body p-0">
                    @if($feeRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Fee Type</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Amount</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Paid</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Balance</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Due Date</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Status</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeRecords as $record)
                                <tr>
                                    <td class="px-4 py-3" style="color: #212529;">
                                        <div class="fw-semibold">{{ $record->feeStructure->fee_type ?? 'General Fee' }}</div>
                                        <small style="color: #6c757d;">{{ $record->description ?? '' }}</small>
                                    </td>
                                    <td class="px-4 py-3" style="color: #212529; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($record->total_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #198754; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($record->paid_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #dc3545; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($record->balance_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #212529;">
                                        {{ $record->due_date ? \Carbon\Carbon::parse($record->due_date)->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($record->status === 'paid')
                                            <span class="badge" style="background-color: #d1e7dd; color: #0f5132; padding: 0.35rem 0.65rem; font-weight: 600;">Paid</span>
                                        @elseif($record->status === 'partial')
                                            <span class="badge" style="background-color: #fff3cd; color: #856404; padding: 0.35rem 0.65rem; font-weight: 600;">Partial</span>
                                        @else
                                            <span class="badge" style="background-color: #f8d7da; color: #842029; padding: 0.35rem 0.65rem; font-weight: 600;">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($record->status !== 'paid')
                                            <a href="{{ route('student.fees.pay', $record) }}" class="btn btn-sm btn-primary">Pay Now</a>
                                        @else
                                            <a href="{{ route('student.fees.receipt', $record) }}" class="btn btn-sm btn-outline-secondary">Receipt</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-receipt display-1" style="color: #dee2e6;"></i>
                        <h5 class="mt-3" style="color: #6c757d;">No fee records found</h5>
                        <p style="color: #adb5bd;">Fee records will appear here when they are added by the administration.</p>
                    </div>
                    @endif
                </div>
                @if($feeRecords->hasPages())
                <div class="card-footer bg-white border-top">
                    {{ $feeRecords->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0" style="color: #212529; font-weight: 600;">Payment Ledger</h5>
                </div>
                <div class="card-body p-0">
                    @if($paymentLedger->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Date</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Fee</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Semester</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Amount</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Paid To Date</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Balance After</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Method</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Status</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Proof</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentLedger as $entry)
                                <tr>
                                    @php
                                        $payment = $entry['payment'];
                                    @endphp
                                    <td class="px-4 py-3" style="color: #212529;">
                                        {{ $payment->payment_date?->format('M d, Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #212529;">
                                        {{ $payment->feeRecord?->feeStructure?->name ?? 'General Fee' }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #212529;">
                                        {{ $payment->feeRecord?->feeStructure?->semester?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #212529; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #198754; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($entry['paid_to_date'], 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #dc3545; font-weight: 600;">
                                        {{ $currencyCode }} {{ number_format($entry['balance_after'], 2) }}
                                    </td>
                                    <td class="px-4 py-3" style="color: #212529;">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($payment->status === 'completed')
                                            <span class="badge" style="background-color: #d1e7dd; color: #0f5132; padding: 0.35rem 0.65rem; font-weight: 600;">Completed</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge" style="background-color: #fff3cd; color: #856404; padding: 0.35rem 0.65rem; font-weight: 600;">Pending</span>
                                        @else
                                            <span class="badge" style="background-color: #f8d7da; color: #842029; padding: 0.35rem 0.65rem; font-weight: 600;">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($payment->payment_proof)
                                            <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-journal-text display-6" style="color: #dee2e6;"></i>
                        <h6 class="mt-3" style="color: #6c757d;">No payment records yet</h6>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
