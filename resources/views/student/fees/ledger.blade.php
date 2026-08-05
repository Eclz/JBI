@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header section -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2" style="color: #212529; font-weight: 700; font-size: 1.75rem;">My Student Ledger</h2>
                <p class="mb-0" style="color: #6c757d;">Track all your processed fee transactions and account balances</p>
            </div>
            <a href="{{ route('student.fees.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-receipt me-1"></i> View Invoices
            </a>
        </div>
    </div>

    <!-- Payment Ledger Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-clock-history me-2"></i>Account Payment History</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($paymentLedger) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Transaction Date</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Fee Type</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Semester</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Paid Amount</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Paid To Date</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Balance After</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Payment Method</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem;">Status</th>
                                    <th class="border-0 py-3 px-4" style="color: #495057; font-weight: 600; font-size: 0.875rem; text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentLedger as $entry)
                                <tr>
                                    @php
                                        $payment = $entry['payment'];
                                    @endphp
                                    <td class="px-4 py-3 text-dark">
                                        {{ $payment->payment_date?->format('M d, Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-dark">
                                        {{ $payment->feeRecord?->feeStructure?->name ?? 'General Fee' }}
                                    </td>
                                    <td class="px-4 py-3 text-dark">
                                        {{ $payment->feeRecord?->feeStructure?->semester?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-dark fw-bold">
                                        {{ $currencyCode }} {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-success fw-bold">
                                        {{ $currencyCode }} {{ number_format($entry['paid_to_date'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-danger fw-bold">
                                        {{ $currencyCode }} {{ number_format($entry['balance_after'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-dark">
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
                                            <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i> View Proof
                                            </a>
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
                        <i class="bi bi-journal-text display-1" style="color: #dee2e6;"></i>
                        <h5 class="mt-3 text-muted">No Payment Records Yet</h5>
                        <p class="text-muted small">Your payment transactions and ledger records will be displayed here when you make a payment.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
