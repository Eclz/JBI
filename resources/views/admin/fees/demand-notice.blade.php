@extends('layouts.app')

@section('title', 'Demand Notice')

@section('content')
<div class="container-fluid py-4 demand-page">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="mb-0">Demand Notice</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.fees.records.show', $fee) }}" class="btn btn-outline-secondary">Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print Notice</button>
        </div>
    </div>

    <div class="card shadow-sm printable-demand">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/jbi-blue.webp') }}" alt="JBI University Logo" class="demand-logo">
                    <div>
                        <h4 class="mb-0">JBI University</h4>
                        <div class="text-muted small">Finance Department</div>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $noticeNumber = 'DMN-' . str_pad((string) $fee->id, 8, '0', STR_PAD_LEFT);
                        $verificationCode = strtoupper(substr(hash('sha256', 'DMN|' . $fee->id . '|' . $fee->invoice_number . '|' . $fee->total_amount), 0, 16));
                    @endphp
                    <div><strong>Demand #:</strong> {{ $noticeNumber }}</div>
                    <div><strong>Issue Date:</strong> {{ now()->format('M d, Y') }}</div>
                    <div><strong>Invoice:</strong> {{ $fee->invoice_number }}</div>
                </div>
            </div>

            <div class="alert alert-danger py-2 mb-4">
                <strong>Outstanding Balance:</strong>
                {{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}
                | <strong>Due Date:</strong> {{ $fee->due_date?->format('M d, Y') ?? 'N/A' }}
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <h6 class="mb-2">Student Information</h6>
                    <div>{{ $fee->student?->full_name ?: ($fee->student?->first_name . ' ' . $fee->student?->last_name) }}</div>
                    <div>{{ $fee->student?->email }}</div>
                    <div>Admission #: {{ $fee->student?->studentProfile?->admission_number ?? 'N/A' }}</div>
                    <div>Department: {{ $fee->student?->studentProfile?->department?->name ?? 'N/A' }}</div>
                </div>
                <div class="col-6 text-end">
                    <h6 class="mb-2">Fee Details</h6>
                    <div>{{ $fee->feeStructure?->name ?? 'N/A' }}</div>
                    <div>Academic Year: {{ $fee->feeStructure?->academicYear?->name ?? 'N/A' }}</div>
                    <div>Semester: {{ $fee->feeStructure?->semester?->name ?? 'N/A' }}</div>
                    <div>Status: {{ $fee->display_status }}</div>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Base Fee</td>
                            <td class="text-end">{{ $currencyCode }} {{ number_format($fee->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td class="text-end text-success">-{{ $currencyCode }} {{ number_format($fee->discount_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Late Fee</td>
                            <td class="text-end text-warning">+{{ $currencyCode }} {{ number_format($fee->late_fee, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Total Billed Amount</strong></td>
                            <td class="text-end"><strong>{{ $currencyCode }} {{ number_format($fee->total_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Total Amount Paid to Date</td>
                            <td class="text-end text-success">{{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong>Outstanding Balance / Payable</strong></td>
                            <td class="text-end text-danger"><strong>{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <h6>Payment Instructions</h6>
                <p class="small text-muted mb-1">Please settle the outstanding balance immediately through authorized university bank accounts or online payment channels to avoid registration holds or examination restrictions.</p>
                <p class="small text-muted mb-0">Bring this notice and proof of payment to the Finance Office for verification and receipt issuance.</p>
            </div>

            <div class="small mb-4">
                <strong>Verification Code:</strong> {{ $verificationCode }}
                <span class="text-muted">| Verify at: {{ route('receipts.verify') }}</span>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-4 text-center">
                    <div class="signature-line"></div>
                    <div class="small text-muted">Student Signature</div>
                </div>
                <div class="col-4 text-center">
                    <div class="signature-line"></div>
                    <div class="small text-muted">Finance Officer</div>
                </div>
                <div class="col-4 text-center">
                    <div class="stamp-box">OFFICIAL STAMP</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.demand-logo { width: 72px; height: 72px; object-fit: contain; }
.signature-line { height: 34px; border-bottom: 1px solid #333; margin-bottom: 8px; }
.stamp-box { border: 1px dashed #777; min-height: 56px; padding: 14px 8px; font-size: 12px; color: #666; }
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .container-fluid, .receipt-page { padding: 0 !important; margin: 0 !important; }
    .printable-receipt {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .card-body { padding: 1.5rem !important; }
}
</style>
@endpush
