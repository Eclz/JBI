@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Edit Fee Record</h1>
                    <p class="text-muted mb-0">Update fee record details</p>
                </div>
                <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Fees
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Fee Record Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.update', $fee) }}" method="POST" id="editFeeForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Student</label>
                                <input type="text" class="form-control"
                                       value="{{ $fee->student->first_name }} {{ $fee->student->last_name }}"
                                       disabled>
                                <small class="text-muted">Student cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fee Structure</label>
                                <input type="text" class="form-control"
                                       value="{{ $fee->feeStructure->name }}"
                                       disabled>
                                <small class="text-muted">Fee structure cannot be changed</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" class="form-control"
                                       value="{{ $fee->invoice_number }}"
                                       disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       id="due_date"
                                       name="due_date"
                                       value="{{ old('due_date', $fee->due_date->format('Y-m-d')) }}"
                                       required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Amount Details</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="amount" class="form-label">Base Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           id="amount"
                                           name="amount"
                                           value="{{ old('amount', $fee->amount) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="discount_amount" class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('discount_amount') is-invalid @enderror"
                                           id="discount_amount"
                                           name="discount_amount"
                                           value="{{ old('discount_amount', $fee->discount_amount ?? 0) }}"
                                           step="0.01"
                                           min="0">
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="late_fee" class="form-label">Late Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('late_fee') is-invalid @enderror"
                                           id="late_fee"
                                           name="late_fee"
                                           value="{{ old('late_fee', $fee->late_fee ?? 0) }}"
                                           step="0.01"
                                           min="0">
                                    @error('late_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="payment_notes" class="form-label">Payment Notes</label>
                            <textarea class="form-control @error('payment_notes') is-invalid @enderror"
                                      id="payment_notes"
                                      name="payment_notes"
                                      rows="3"
                                      placeholder="Enter any additional notes about this fee record">{{ old('payment_notes', $fee->payment_notes) }}</textarea>
                            @error('payment_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fees.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Fee Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Status Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">Current Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'partial' ? 'warning' : ($fee->status === 'overdue' ? 'danger' : 'secondary')) }}">
                            {{ ucfirst($fee->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Paid Amount</small>
                        <strong class="text-success">${{ number_format($fee->paid_amount, 2) }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Balance Amount</small>
                        <strong class="text-danger">${{ number_format($fee->balance_amount, 2) }}</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Amount</small>
                        <strong>${{ number_format($fee->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Calculation Preview Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="card-title mb-0">Amount Preview</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Amount:</span>
                        <strong id="preview_base">$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount:</span>
                        <strong class="text-success" id="preview_discount">-$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Late Fee:</span>
                        <strong class="text-danger" id="preview_late">+$0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>New Total:</span>
                        <strong id="preview_total">$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Already Paid:</span>
                        <strong class="text-success">-${{ number_format($fee->paid_amount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span><strong>New Balance:</strong></span>
                        <strong class="text-primary" id="preview_balance">$0.00</strong>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i>
                <strong>Note:</strong> Updating amounts will recalculate the balance. The paid amount and payment history will remain unchanged.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const discountInput = document.getElementById('discount_amount');
    const lateFeeInput = document.getElementById('late_fee');
    const paidAmount = {{ $fee->paid_amount }};

    function updatePreview() {
        const amount = parseFloat(amountInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const lateFee = parseFloat(lateFeeInput.value) || 0;

        const total = amount - discount + lateFee;
        const balance = total - paidAmount;

        document.getElementById('preview_base').textContent = '$' + amount.toFixed(2);
        document.getElementById('preview_discount').textContent = '-$' + discount.toFixed(2);
        document.getElementById('preview_late').textContent = '+$' + lateFee.toFixed(2);
        document.getElementById('preview_total').textContent = '$' + total.toFixed(2);
        document.getElementById('preview_balance').textContent = '$' + balance.toFixed(2);

        // Update balance color
        const balanceElement = document.getElementById('preview_balance');
        if (balance <= 0) {
            balanceElement.classList.remove('text-primary', 'text-danger');
            balanceElement.classList.add('text-success');
        } else if (balance < total) {
            balanceElement.classList.remove('text-success', 'text-danger');
            balanceElement.classList.add('text-primary');
        } else {
            balanceElement.classList.remove('text-success', 'text-primary');
            balanceElement.classList.add('text-danger');
        }
    }

    amountInput.addEventListener('input', updatePreview);
    discountInput.addEventListener('input', updatePreview);
    lateFeeInput.addEventListener('input', updatePreview);

    // Initial calculation
    updatePreview();

    // Form validation
    document.getElementById('editFeeForm').addEventListener('submit', function(e) {
        const amount = parseFloat(amountInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        if (discount > amount) {
            e.preventDefault();
            alert('Discount amount cannot be greater than the base amount.');
            discountInput.focus();
            return false;
        }
    });
});
</script>
@endsection
