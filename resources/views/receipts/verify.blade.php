@extends('layouts.app')

@section('title', 'Verify Receipt')

@section('content')
<div class="container py-4" style="max-width: 780px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-1">Receipt Verification</h3>
            <p class="text-muted mb-4">Enter the receipt number and verification code to confirm authenticity.</p>

            <form method="POST" action="{{ route('receipts.verify.submit') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Receipt Number</label>
                    <input type="text" name="receipt_number" class="form-control @error('receipt_number') is-invalid @enderror"
                           value="{{ old('receipt_number', $receiptNumber ?? '') }}" placeholder="e.g. RCPT-TXN-00000123" required>
                    @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Verification Code</label>
                    <input type="text" name="verification_code" class="form-control @error('verification_code') is-invalid @enderror"
                           value="{{ old('verification_code', $verificationCode ?? '') }}" placeholder="e.g. A1B2C3D4E5F6A7B8" required>
                    @error('verification_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Verify Receipt</button>
                </div>
            </form>

            @isset($result)
                <hr class="my-4">
                @if($result['valid'])
                    <div class="alert alert-success mb-3"><strong>Valid:</strong> {{ $result['message'] }}</div>
                    @if($result['type'] === 'transaction')
                        @php $payment = $result['record']; @endphp
                        <table class="table table-sm table-bordered mb-0">
                            <tr><th>Type</th><td>Transaction Receipt</td></tr>
                            <tr><th>Student</th><td>{{ $payment->student?->full_name ?: ($payment->student?->name ?? 'N/A') }}</td></tr>
                            <tr><th>Invoice</th><td>{{ $payment->feeRecord?->invoice_number ?? 'N/A' }}</td></tr>
                            <tr><th>Amount</th><td>{{ $currencyCode }} {{ number_format($payment->amount, 2) }}</td></tr>
                            <tr><th>Status</th><td>{{ ucfirst($payment->status) }}</td></tr>
                            <tr><th>Date</th><td>{{ $payment->payment_date?->format('M d, Y H:i') ?? 'N/A' }}</td></tr>
                        </table>
                    @elseif($result['type'] === 'summary')
                        @php $fee = $result['record']; @endphp
                        <table class="table table-sm table-bordered mb-0">
                            <tr><th>Type</th><td>Summary Receipt</td></tr>
                            <tr><th>Student</th><td>{{ $fee->student?->full_name ?: ($fee->student?->name ?? 'N/A') }}</td></tr>
                            <tr><th>Invoice</th><td>{{ $fee->invoice_number ?? 'N/A' }}</td></tr>
                            <tr><th>Total</th><td>{{ $currencyCode }} {{ number_format($fee->total_amount, 2) }}</td></tr>
                            <tr><th>Paid</th><td>{{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}</td></tr>
                            <tr><th>Balance</th><td>{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</td></tr>
                        </table>
                    @endif
                @else
                    <div class="alert alert-danger mb-0"><strong>Invalid:</strong> {{ $result['message'] }}</div>
                @endif
            @endisset
        </div>
    </div>
</div>
@endsection
