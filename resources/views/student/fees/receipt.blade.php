@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 receipt-page">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="mb-1">Payment Receipt</h2>
            <p class="mb-0 text-muted">Receipt for {{ $fee->feeStructure->name ?? 'Fee' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary">Back to Fees</a>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">Print Summary</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm printable-receipt">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Logo" class="receipt-logo">
                    <div>
                        <h4 class="mb-0">JBI University</h4>
                        <div class="text-muted small">Official Student Payment Receipt</div>
                    </div>
                </div>
                <div class="text-end">
                    <div><strong>Receipt:</strong> {{ $receiptNumber }}</div>
                    <div><strong>Invoice:</strong> {{ $fee->invoice_number ?? 'N/A' }}</div>
                    <div><strong>Date:</strong> {{ now()->format('M d, Y H:i') }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <h6>Fee Summary</h6>
                    <table class="table table-sm table-borderless">
                        <tr><td>Total Amount</td><td class="text-end">{{ $currencyCode }} {{ number_format($fee->total_amount, 2) }}</td></tr>
                        <tr><td>Paid</td><td class="text-end text-success">{{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}</td></tr>
                        <tr><td>Balance</td><td class="text-end text-danger">{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</td></tr>
                        <tr>
                            <td>Status</td>
                            <td class="text-end">
                                <span class="badge {{ $fee->status === 'paid' ? 'bg-success' : ($fee->status === 'partial' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ ucfirst($fee->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-7">
                    <h6>Payment History</h6>
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="no-print">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date?->format('M d, Y g:i A') ?? '-' }}</td>
                                            <td>{{ $payment->payment_method_label ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td class="fw-semibold">{{ $currencyCode }} {{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $payment->status_badge_class ?? 'bg-secondary' }}">{{ ucfirst($payment->status) }}</span>
                                            </td>
                                            <td class="no-print">
                                                @if($payment->status === 'completed')
                                                    <a href="{{ route('student.fees.transaction-receipt', [$fee, $payment]) }}" target="_blank" class="btn btn-sm btn-outline-primary">Print</a>
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
                        <div class="text-center py-4 text-muted">No payments recorded.</div>
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12">
                    <div class="small"><strong>Verification Code:</strong> {{ $verificationCode }}</div>
                    <div class="small text-muted">Verify at: {{ $verificationUrl }}</div>
                    <div class="small text-muted">Use receipt number and code to confirm authenticity.</div>
                </div>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-4 text-center">
                    <div class="signature-line"></div>
                    <div class="small text-muted">Student Signature</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="signature-line"></div>
                    <div class="small text-muted">Finance Officer</div>
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
