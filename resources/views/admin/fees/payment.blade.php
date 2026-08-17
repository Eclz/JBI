@extends('layouts.app')

@section('title', 'Process Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Process Payment</h4>
                    <a href="{{ route('admin.fees.records.show', $fee) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Fee Details
                    </a>
                </div>
                <div class="card-body">
                    <!-- Fee Summary -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>{{ $fee->student->first_name }} {{ $fee->student->last_name }}</h5>
                                    <p class="mb-1"><strong>Fee Type:</strong> {{ $fee->feeStructure->name }}</p>
                                    <p class="mb-1"><strong>Invoice:</strong> <span class="badge bg-secondary">{{ $fee->invoice_number }}</span></p>
                                    <p class="mb-0"><strong>Due Date:</strong>
                                        <span class="{{ $fee->is_overdue ? 'text-danger' : '' }}">
                                            {{ $fee->due_date->format('M d, Y') }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h6 class="text-muted">Outstanding Balance</h6>
                                    <h3 class="text-danger">{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</h3>
                                    <small class="text-muted">
                                        Total: {{ $currencyCode }} {{ number_format($fee->total_amount, 2) }} |
                                        Paid: {{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form method="POST" action="{{ route('admin.fees.records.process-payment', $fee) }}" id="paymentForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Payment Amount -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $currencyCode }}</span>
                                        <input type="number" class="form-control @error('payment_amount') is-invalid @enderror"
                                               name="payment_amount" id="payment_amount" step="0.01"
                                               min="0.01" max="{{ $fee->balance_amount }}"
                                               value="{{ old('payment_amount', $fee->balance_amount) }}" required>
                                        <button type="button" class="btn btn-outline-secondary" id="payFullAmount">
                                            Pay Full
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">
                                        Maximum: {{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}
                                    </small>
                                    @error('payment_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                           name="payment_date" id="payment_date"
                                           value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                           max="{{ now()->format('Y-m-d') }}" required>
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_method') is-invalid @enderror"
                                            name="payment_method" id="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Transaction ID -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_id" class="form-label">Transaction ID / Reference</label>
                                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                           name="transaction_id" id="transaction_id"
                                           value="{{ old('transaction_id') }}"
                                           placeholder="Check number, transaction ID, etc.">
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="paymentProofWrapper" style="display: none;">
                            <label for="payment_proof" class="form-label">Cash Payment Proof <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('payment_proof') is-invalid @enderror"
                                   name="payment_proof" id="payment_proof" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Upload a receipt or proof of cash payment (PDF, JPG, PNG).</div>
                            @error('payment_proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Notes -->
                        <div class="mb-3">
                            <label for="payment_notes" class="form-label">Payment Notes</label>
                            <textarea class="form-control @error('payment_notes') is-invalid @enderror"
                                      name="payment_notes" id="payment_notes" rows="3"
                                      placeholder="Additional notes about this payment...">{{ old('payment_notes') }}</textarea>
                            @error('payment_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Summary -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Payment Summary</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Current Balance:</strong>
                                        <div class="text-danger h5">{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Payment Amount:</strong>
                                        <div class="text-primary h5" id="summary_payment_amount">$0.00</div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>New Balance:</strong>
                                        <div class="h5" id="summary_new_balance">{{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Status After Payment:</strong>
                                        <div class="h5" id="summary_new_status">
                                            <span class="badge bg-secondary">Partial</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fees.records.show', $fee) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success" id="processPaymentBtn">
                                <i class="fas fa-dollar-sign"></i> Process Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentAmountInput = document.getElementById('payment_amount');
    const payFullAmountBtn = document.getElementById('payFullAmount');
    const processPaymentBtn = document.getElementById('processPaymentBtn');
    const currentBalance = {{ $fee->balance_amount }};
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentProofWrapper = document.getElementById('paymentProofWrapper');
    const paymentProofInput = document.getElementById('payment_proof');

    // Pay full amount button
    payFullAmountBtn.addEventListener('click', function() {
        paymentAmountInput.value = currentBalance.toFixed(2);
        updateSummary();
    });

    // Update summary when payment amount changes
    paymentAmountInput.addEventListener('input', updateSummary);
    paymentMethodSelect.addEventListener('change', togglePaymentProof);

    function updateSummary() {
        const paymentAmount = parseFloat(paymentAmountInput.value) || 0;
        const newBalance = currentBalance - paymentAmount;

        // Update summary displays
        document.getElementById('summary_payment_amount').textContent = '$' + paymentAmount.toFixed(2);
        document.getElementById('summary_new_balance').textContent = '$' + newBalance.toFixed(2);

        // Update status badge
        const statusElement = document.getElementById('summary_new_status');
        if (newBalance <= 0) {
            statusElement.innerHTML = '<span class="badge bg-success">Paid</span>';
            statusElement.className = 'h5 text-success';
        } else if (paymentAmount > 0) {
            statusElement.innerHTML = '<span class="badge bg-warning">Partial</span>';
            statusElement.className = 'h5 text-warning';
        } else {
            statusElement.innerHTML = '<span class="badge bg-secondary">Pending</span>';
            statusElement.className = 'h5 text-secondary';
        }

        // Update button text
        if (newBalance <= 0) {
            processPaymentBtn.innerHTML = '<i class="fas fa-check"></i> Complete Payment';
            processPaymentBtn.className = 'btn btn-success';
        } else {
            processPaymentBtn.innerHTML = '<i class="fas fa-dollar-sign"></i> Process Partial Payment';
            processPaymentBtn.className = 'btn btn-primary';
        }
    }

    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const paymentAmount = parseFloat(paymentAmountInput.value) || 0;

        if (paymentAmount <= 0) {
            e.preventDefault();
            alert('Payment amount must be greater than zero.');
            paymentAmountInput.focus();
            return false;
        }

        if (paymentAmount > currentBalance) {
            e.preventDefault();
            alert('Payment amount cannot exceed the outstanding balance of $' + currentBalance.toFixed(2));
            paymentAmountInput.focus();
            return false;
        }

        // Confirmation for large payments
        if (paymentAmount >= currentBalance) {
            if (!confirm('This will fully pay the outstanding balance. Are you sure you want to proceed?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Initial summary update
    updateSummary();

    // Show/hide transaction ID field based on payment method
    paymentMethodSelect.addEventListener('change', function() {
        const transactionIdField = document.getElementById('transaction_id');
        const transactionIdLabel = transactionIdField.previousElementSibling;

        if (['bank_transfer', 'credit_card', 'debit_card', 'online', 'mobile_money', 'check'].includes(this.value)) {
            transactionIdLabel.innerHTML = 'Transaction ID / Reference <span class="text-danger">*</span>';
            transactionIdField.required = true;
        } else {
            transactionIdLabel.innerHTML = 'Transaction ID / Reference';
            transactionIdField.required = false;
        }
    });

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

    togglePaymentProof();
});
</script>
@endpush
