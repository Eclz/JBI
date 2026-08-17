@extends('layouts.app')

@section('title', 'Create Fee Record')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Fee Record</h4>
                    <a href="{{ route('admin.fees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Fees
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.fees.records.store') }}" id="feeRecordForm">
                        @csrf

                        <div class="row">
                            <!-- Student Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Student <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror"
                                            name="user_id" id="user_id" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }}
                                                ({{ $student->studentProfile->student_id ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fee Structure Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fee_structure_id" class="form-label">Fee Structure <span class="text-danger">*</span></label>
                                    <select class="form-control @error('fee_structure_id') is-invalid @enderror"
                                            name="fee_structure_id" id="fee_structure_id" required>
                                        <option value="">Select Fee Structure</option>
                                        @foreach($feeStructures as $structure)
                                            <option value="{{ $structure->id }}"
                                                    data-amount="{{ $structure->amount }}"
                                                    {{ old('fee_structure_id') == $structure->id ? 'selected' : '' }}>
                                                {{ $structure->name }} - {{ $currencyCode }} {{ number_format($structure->amount, 2) }}
                                                ({{ $structure->academicYear->name ?? 'All Years' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fee_structure_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Base Amount -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Base Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $currencyCode }}</span>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                               name="amount" id="amount" step="0.01" min="0"
                                               value="{{ old('amount') }}" required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Discount Amount -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label">Discount Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $currencyCode }}</span>
                                        <input type="number" class="form-control @error('discount_amount') is-invalid @enderror"
                                               name="discount_amount" id="discount_amount" step="0.01" min="0"
                                               value="{{ old('discount_amount', 0) }}">
                                    </div>
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Late Fee -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="late_fee" class="form-label">Late Fee</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $currencyCode }}</span>
                                        <input type="number" class="form-control @error('late_fee') is-invalid @enderror"
                                               name="late_fee" id="late_fee" step="0.01" min="0"
                                               value="{{ old('late_fee', 0) }}">
                                    </div>
                                    @error('late_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total Amount (Calculated) -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Total Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $currencyCode }}</span>
                                        <input type="text" class="form-control bg-light" id="total_amount_display" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Due Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                           name="due_date" id="due_date"
                                           value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Invoice Number (Auto-generated) -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control bg-light"
                                           value="Auto-generated" readonly>
                                    <small class="form-text text-muted">Invoice number will be automatically generated</small>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Notes -->
                        <div class="mb-3">
                            <label for="payment_notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('payment_notes') is-invalid @enderror"
                                      name="payment_notes" id="payment_notes" rows="3"
                                      placeholder="Additional notes about this fee record...">{{ old('payment_notes') }}</textarea>
                            @error('payment_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Summary Card -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Fee Record Summary</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Base Amount:</strong>
                                        <div id="summary_base_amount">$0.00</div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Discount:</strong>
                                        <div id="summary_discount" class="text-success">-$0.00</div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Late Fee:</strong>
                                        <div id="summary_late_fee" class="text-warning">+$0.00</div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total Amount:</strong>
                                        <div id="summary_total" class="text-primary h5">$0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Fee Record
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
    const feeStructureSelect = document.getElementById('fee_structure_id');
    const amountInput = document.getElementById('amount');
    const discountInput = document.getElementById('discount_amount');
    const lateFeeInput = document.getElementById('late_fee');
    const totalAmountDisplay = document.getElementById('total_amount_display');

    // Update amount when fee structure is selected
    feeStructureSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const amount = parseFloat(selectedOption.dataset.amount) || 0;
            amountInput.value = amount.toFixed(2);
            calculateTotal();
        } else {
            amountInput.value = '';
            calculateTotal();
        }
    });

    // Calculate total when any amount field changes
    [amountInput, discountInput, lateFeeInput].forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    function calculateTotal() {
        const baseAmount = parseFloat(amountInput.value) || 0;
        const discountAmount = parseFloat(discountInput.value) || 0;
        const lateFee = parseFloat(lateFeeInput.value) || 0;

        // Validate discount doesn't exceed base amount
        if (discountAmount > baseAmount) {
            discountInput.setCustomValidity('Discount cannot exceed base amount');
        } else {
            discountInput.setCustomValidity('');
        }

        const totalAmount = baseAmount - discountAmount + lateFee;

        // Update displays
        totalAmountDisplay.value = totalAmount.toFixed(2);
        document.getElementById('summary_base_amount').textContent = '$' + baseAmount.toFixed(2);
        document.getElementById('summary_discount').textContent = '-$' + discountAmount.toFixed(2);
        document.getElementById('summary_late_fee').textContent = '+$' + lateFee.toFixed(2);
        document.getElementById('summary_total').textContent = '$' + totalAmount.toFixed(2);
    }

    // Initial calculation
    calculateTotal();

    // Form validation
    document.getElementById('feeRecordForm').addEventListener('submit', function(e) {
        const baseAmount = parseFloat(amountInput.value) || 0;
        const discountAmount = parseFloat(discountInput.value) || 0;

        if (discountAmount > baseAmount) {
            e.preventDefault();
            alert('Discount amount cannot exceed the base amount.');
            discountInput.focus();
            return false;
        }

        if (baseAmount <= 0) {
            e.preventDefault();
            alert('Base amount must be greater than zero.');
            amountInput.focus();
            return false;
        }
    });
});
</script>
@endpush
