@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #212529; font-weight: 700;">Pay Fee</h2>
            <p class="mb-0 text-muted">Complete payment for this fee record</p>
        </div>
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Fees
        </a>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Fee Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted">Fee Type</div>
                        <div class="fw-semibold">{{ $fee->feeStructure->name ?? 'General Fee' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Total Amount</div>
                        <div class="fw-semibold">{{ $currencyCode }} {{ number_format($fee->total_amount, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Paid</div>
                        <div class="fw-semibold text-success">{{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Balance</div>
                        <div class="fw-semibold text-danger">{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted">Due Date</div>
                        <div class="fw-semibold">{{ $fee->due_date ? $fee->due_date->format('M d, Y') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Make a Payment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.fees.processPayment', $fee) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $fee->balance_amount }}"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" value="{{ old('amount', $fee->balance_amount) }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum payable: {{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Select payment method</option>
                                @foreach($paymentMethods as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_method') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="paymentProofWrapper" style="display: none;">
                            <label for="payment_proof" class="form-label">Cash Payment Proof <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('payment_proof') is-invalid @enderror"
                                   id="payment_proof" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Upload a receipt or proof of cash payment (PDF, JPG, PNG).</div>
                            @error('payment_proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any payment notes..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-credit-card me-1"></i> Submit Payment
                        </button>
                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-shield-check me-2"></i>
                Payments are recorded immediately. Keep your receipt for reference.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentProofWrapper = document.getElementById('paymentProofWrapper');
    const paymentProofInput = document.getElementById('payment_proof');

    function togglePaymentProof() {
        const isCash = paymentMethodSelect.value === 'cash';
        paymentProofWrapper.style.display = isCash ? 'block' : 'none';
        if (isCash) {
            paymentProofInput.setAttribute('required', 'required');
        } else {
            paymentProofInput.removeAttribute('required');
            paymentProofInput.value = '';
        }
    }

    paymentMethodSelect.addEventListener('change', togglePaymentProof);
    togglePaymentProof();
});
</script>
@endpush
