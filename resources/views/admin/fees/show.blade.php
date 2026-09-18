@extends('layouts.app')

@section('title', 'Fee Record Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Fee Record Details</h4>
                    <div>
                        @if($fee->status != 'paid')
                            <a href="{{ route('admin.fees.records.payment', $fee) }}" class="btn btn-success me-2">
                                <i class="fas fa-dollar-sign"></i> Record Payment
                            </a>
                        @endif
                        <a href="{{ route('admin.fees.records.demand-notice', $fee) }}" class="btn btn-outline-warning me-2" target="_blank">
                            <i class="fas fa-file-alt"></i> Demand Notice
                        </a>
                        <a href="{{ route('admin.fees.records.receipt', $fee) }}" class="btn btn-outline-dark me-2" target="_blank">
                            <i class="fas fa-receipt"></i> Receipt
                        </a>
                        <a href="{{ route('admin.fees.records.edit', $fee) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.fees.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Fees
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Student Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Student Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($fee->student->profile_picture)
                                            <img src="{{ asset('storage/' . $fee->student->profile_picture) }}"
                                                 class="rounded-circle me-3" width="64" height="64">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 64px; height: 64px;">
                                                <span class="text-white h4 mb-0">{{ substr($fee->student->first_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h5 class="mb-1">{{ $fee->student->first_name }} {{ $fee->student->last_name }}</h5>
                                            <p class="text-muted mb-0">{{ $fee->student->email }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <strong>Student ID:</strong>
                                            <p>{{ $fee->student->studentProfile->student_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Phone:</strong>
                                            <p>{{ $fee->student->studentProfile->phone ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Department:</strong>
                                            <p>
                                                @if($fee->student->studentProfile && $fee->student->studentProfile->department)
                                                    @if(is_object($fee->student->studentProfile->department))
                                                        {{ $fee->student->studentProfile->department->name ?? 'N/A' }}
                                                    @else
                                                        {{ $fee->student->studentProfile->department }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Year of Study:</strong>
                                            <p>{{ $fee->student->studentProfile->year_of_study ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Fee Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <strong>Invoice Number:</strong>
                                            <p><span class="badge bg-secondary">{{ $fee->invoice_number }}</span></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Fee Type:</strong>
                                            <p>{{ $fee->feeStructure->name }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Academic Year:</strong>
                                            <p>{{ $fee->feeStructure->academicYear->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Semester:</strong>
                                            <p>{{ $fee->feeStructure->semester->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Due Date:</strong>
                                            <p class="{{ $fee->is_overdue ? 'text-danger' : '' }}">
                                                {{ $fee->due_date->format('M d, Y') }}
                                                @if($fee->is_overdue)
                                                    <br><small>({{ $fee->due_date->diffForHumans() }})</small>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Status:</strong>
                                            <p>
                                                <span class="badge bg-{{
                                                    $fee->status == 'paid' ? 'success' :
                                                    ($fee->is_overdue ? 'danger' :
                                                    ($fee->status == 'partial' ? 'warning' : 'secondary'))
                                                }}">
                                                    {{ $fee->is_overdue && $fee->status != 'paid' ? 'Overdue' : ucfirst($fee->status) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <h6 class="text-muted">Base Amount</h6>
                                        <h4>{{ $currencyCode }} {{ number_format($fee->amount, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <h6 class="text-muted">Discount</h6>
                                        <h4 class="text-success">-{{ $currencyCode }} {{ number_format($fee->discount_amount, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <h6 class="text-muted">Late Fee</h6>
                                        <h4 class="text-warning">+{{ $currencyCode }} {{ number_format($fee->late_fee, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <h6 class="text-muted">Total Amount</h6>
                                        <h4 class="text-primary">{{ $currencyCode }} {{ number_format($fee->total_amount, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <h6 class="text-muted">Paid Amount</h6>
                                        <h4 class="text-success">{{ $currencyCode }} {{ number_format($fee->paid_amount, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <h6 class="text-muted">Balance</h6>
                                        <h4 class="{{ $fee->balance_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $currencyCode }} {{ number_format($fee->balance_amount, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            @if($fee->balance_amount > 0)
                                <div class="progress mt-3" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ ($fee->paid_amount / $fee->total_amount) * 100 }}%">
                                        {{ number_format(($fee->paid_amount / $fee->total_amount) * 100, 1) }}% Paid
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success mt-3 mb-0">
                                    <i class="fas fa-check-circle"></i> This fee has been fully paid.
                                    @if($fee->paid_date)
                                        <br><small>Paid on: {{ $fee->paid_date->format('M d, Y g:i A') }}</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if($fee->payment_history && count($fee->payment_history) > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Transaction ID</th>
                                            <th>Processed By</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fee->payment_history as $payment)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}</td>
                                            <td>{{ $currencyCode }} {{ number_format($payment['amount'], 2) }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $payment['method'])) }}</span>
                                            </td>
                                            <td>{{ $payment['transaction_id'] ?? 'N/A' }}</td>
                                            <td>
                                                @if(isset($payment['processed_by']))
                                                    @php
                                                        $processor = \App\Models\User::find($payment['processed_by']);
                                                    @endphp
                                                    {{ $processor ? $processor->first_name . ' ' . $processor->last_name : 'Unknown' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $payment['notes'] ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($payments->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Student Payment Logs</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Paid To Date</th>
                                            <th>Balance After</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Proof</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $entry)
                                        <tr>
                                            @php
                                                $payment = $entry['payment'];
                                            @endphp
                                            <td>{{ $payment->payment_date?->format('M d, Y H:i') ?? 'N/A' }}</td>
                                            <td>{{ $currencyCode }} {{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $currencyCode }} {{ number_format($entry['paid_to_date'], 2) }}</td>
                                            <td>{{ $currencyCode }} {{ number_format($entry['balance_after'], 2) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td>
                                                <span class="badge bg-{{
                                                    $payment->status === 'completed' ? 'success' :
                                                    ($payment->status === 'pending' ? 'warning' : 'secondary')
                                                }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment->payment_proof)
                                                    <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.fees.payments.approve', $payment) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                                onclick="return confirm('Approve this payment and post it to the fee record?')">
                                                            Approve
                                                        </button>
                                                    </form>
                                                @elseif($payment->status === 'completed')
                                                    <a href="{{ route('admin.fees.payments.receipt', $payment) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary">
                                                        Print Receipt
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
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($fee->payment_notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $fee->payment_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Audit Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Audit Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Created:</strong>
                                    <p>{{ $fee->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Last Updated:</strong>
                                    <p>{{ $fee->updated_at->format('M d, Y g:i A') }}</p>
                                </div>
                                @if($fee->processor)
                                <div class="col-md-6">
                                    <strong>Last Processed By:</strong>
                                    <p>{{ $fee->processor->first_name }} {{ $fee->processor->last_name }}</p>
                                </div>
                                @endif
                                @if($fee->paid_date)
                                <div class="col-md-6">
                                    <strong>Fully Paid Date:</strong>
                                    <p>{{ $fee->paid_date->format('M d, Y g:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReceipt() {
    window.print();
}

function sendReminder() {
    if (confirm('Send payment reminder to {{ $fee->student->first_name }} {{ $fee->student->last_name }}?')) {
        // Implementation for sending reminder
        alert('Reminder sent successfully!');
    }
}

function confirmDelete() {
    if (confirm('Are you sure you want to delete this fee record? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.fees.records.destroy", $fee) }}';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .card-header, .navbar, .sidebar {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .container-fluid {
        padding: 0 !important;
    }
}
</style>
@endpush
