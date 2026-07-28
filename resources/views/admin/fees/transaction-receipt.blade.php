@extends('layouts.app')

@section('title', 'Transaction Receipt')

@section('content')
<div class="container-fluid py-4 receipt-page">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="mb-0">Transaction Receipt</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.fees.records.show', $fee) }}" class="btn btn-outline-secondary">Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print Receipt</button>
        </div>
    </div>

    <div class="card shadow-sm printable-receipt">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/jbi-logo.webp') }}" alt="University Logo" class="receipt-logo">
                    <div>
                        <h4 class="mb-0">JBI University</h4>
                        <div class="text-muted small">Official Transaction Receipt</div>
                    </div>
                </div>
                <div class="text-end">
                    <div><strong>Receipt #:</strong> {{ $receiptNumber }}</div>
                    <div><strong>Date:</strong> {{ $payment->payment_date?->format('M d, Y H:i') ?? now()->format('M d, Y H:i') }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="mb-2">Student Details</h6>
                    <div>{{ $fee->student?->full_name ?: ($fee->student?->first_name . ' ' . $fee->student?->last_name) }}</div>
                    <div>{{ $fee->student?->email }}</div>
                    <div>Admission #: {{ $fee->student?->studentProfile?->admission_number ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="mb-2">Invoice Details</h6>
                    <div>Invoice #: {{ $fee->invoice_number }}</div>
                    <div>Fee Type: {{ $fee->feeStructure?->name ?? 'N/A' }}</div>
                    <div>Due Date: {{ $fee->due_date?->format('M d, Y') ?? 'N/A' }}</div>
                </div>
            </div>

            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th style="width: 30%;">Payment Method</th>
                        <td>{{ $payment->payment_method_label ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    </tr>
                    <tr>
                        <th>Processed By</th>
                        <td>{{ $payment->processedBy?->full_name ?: ($payment->processedBy?->name ?? 'System') }}</td>
                    </tr>
                    <tr>
                        <th>Amount Paid</th>
                        <td><strong>{{ $currencyCode }} {{ number_format($payment->amount, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-8 small text-muted">
                    This receipt confirms only the transaction listed above.
                    <div class="mt-2"><strong>Verification Code:</strong> {{ $verificationCode }}</div>
                    <div class="text-muted">Verify at: {{ $verificationUrl }}</div>
                    <div class="text-muted">Enter receipt number and verification code on the verification page.</div>
                </div>
                <div class="col-md-4">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td><strong>Invoice Total:</strong></td>
                            <td class="text-end">{{ $currencyCode }} {{ number_format($fee->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Paid:</strong></td>
                            <td class="text-end">{{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Balance:</strong></td>
                            <td class="text-end">{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-5 pt-3 border-top">
                <div class="col-md-4 text-center">
                    <div class="signature-line"></div>
                    <div class="small text-muted">Student Signature</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="signature-line"></div>
                    <div class="small text-muted">Cashier / Finance Officer</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="stamp-box">OFFICIAL STAMP</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.receipt-logo { width: 72px; height: 72px; object-fit: contain; }
.signature-line { height: 34px; border-bottom: 1px solid #333; margin-bottom: 8px; }
.stamp-box { border: 1px dashed #777; min-height: 56px; padding: 14px 8px; font-size: 12px; color: #666; }
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .container-fluid { padding: 1rem !important; }
}
</style>
@endpush
