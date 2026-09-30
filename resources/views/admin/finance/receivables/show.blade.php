@extends('layouts.app')

@section('title', 'Receivable Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-file-earmark-text me-2"></i>Invoice Details: {{ $record->invoice_number }}
            </h1>
            <p class="text-muted mb-0">Detailed view of student fee invoice and payment history</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary fw-bold me-2">
                <i class="bi bi-printer me-1"></i>Print Invoice
            </button>
            <a href="{{ route('admin.finance.receivables.index') }}" class="btn btn-secondary fw-bold">
                <i class="bi bi-arrow-left me-1"></i>Back to Receivables
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Document Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white" id="printableInvoice">
                <!-- Document Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">INVOICE</h2>
                        <h5 class="text-muted font-monospace mb-3">#{{ $record->invoice_number }}</h5>
                        
                        <div class="mb-1 text-muted small text-uppercase fw-bold">Billed To:</div>
                        <h5 class="fw-bold text-primary mb-0">{{ $record->student->name ?? 'N/A' }}</h5>
                        <div class="text-muted small">{{ $record->student->email ?? 'No email provided' }}</div>
                        <div class="text-muted small mt-1">Student ID: {{ $record->student->studentProfile->student_number ?? 'N/A' }}</div>
                        <div class="text-muted small">Program: {{ $record->student->studentProfile->program->name ?? 'N/A' }}</div>
                    </div>
                    <div class="text-end">
                        <div class="mb-3">
                            @if($record->status === 'paid')
                                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="bi bi-check-circle me-1"></i>PAID IN FULL</span>
                            @elseif($record->status === 'partial')
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i class="bi bi-clock-history me-1"></i>PARTIAL PAYMENT</span>
                            @elseif($record->status === 'overdue')
                                <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i>OVERDUE</span>
                            @elseif($record->status === 'cancelled')
                                <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill"><i class="bi bi-x-circle me-1"></i>CANCELLED</span>
                            @else
                                <span class="badge bg-info px-3 py-2 fs-6 rounded-pill"><i class="bi bi-hourglass me-1"></i>PENDING</span>
                            @endif
                        </div>
                        <table class="table table-sm table-borderless text-end mb-0">
                            <tr>
                                <td class="text-muted small fw-semibold pb-1">Date Issued:</td>
                                <td class="fw-bold text-dark pb-1">{{ $record->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold pt-0">Due Date:</td>
                                <td class="fw-bold text-danger pt-0">{{ \Carbon\Carbon::parse($record->due_date)->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Invoice Line Items (Summarized) -->
                <div class="table-responsive mb-4">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="border-bottom border-top border-2 border-dark">
                            <tr>
                                <th class="text-uppercase small fw-bold text-muted py-3">Description</th>
                                <th class="text-uppercase small fw-bold text-muted py-3 text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="border-bottom">
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Tuition & Academic Fees</div>
                                    <div class="small text-muted">Base fees for the semester based on fee structure.</div>
                                </td>
                                <td class="text-end fw-semibold py-3">{{ $currencyCode }} {{ number_format($record->amount, 2) }}</td>
                            </tr>
                            @if($record->late_fee > 0)
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Late Payment Penalty</div>
                                    <div class="small text-muted">Applied penalty for missed deadlines.</div>
                                </td>
                                <td class="text-end fw-semibold py-3 text-danger">+ {{ $currencyCode }} {{ number_format($record->late_fee, 2) }}</td>
                            </tr>
                            @endif
                            @if($record->discount_amount > 0)
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Scholarship / Discount</div>
                                    <div class="small text-muted">Applied reductions and bursaries.</div>
                                </td>
                                <td class="text-end fw-semibold py-3 text-success">- {{ $currencyCode }} {{ number_format($record->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row justify-content-end">
                    <div class="col-md-6 col-lg-5">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted fw-bold">Total Amount</td>
                                <td class="text-end fw-bold">{{ $currencyCode }} {{ number_format($record->total_amount, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted fw-bold pb-2">Amount Paid</td>
                                <td class="text-end fw-bold text-success pb-2">- {{ $currencyCode }} {{ number_format($record->paid_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-uppercase pt-3 fs-5">Balance Due</td>
                                <td class="text-end fw-bold text-danger pt-3 fs-5">{{ $currencyCode }} {{ number_format($record->balance_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-wallet2 me-2 text-primary"></i>Payment Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold text-uppercase">Payment Method</label>
                        <div class="fw-bold">{{ $record->payment_method ? ucfirst(str_replace('_', ' ', $record->payment_method)) : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold text-uppercase">Transaction ID</label>
                        <div class="font-monospace fw-bold">{{ $record->transaction_id ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold text-uppercase">Last Payment Date</label>
                        <div class="fw-bold">{{ $record->paid_date ? \Carbon\Carbon::parse($record->paid_date)->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    @if($record->payment_notes)
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold text-uppercase">Payment Notes</label>
                        <div class="bg-light p-3 rounded-3 small border">{{ $record->payment_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 border-top border-primary border-4">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle me-1"></i>System Information</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-1"><strong>Created:</strong> {{ $record->created_at->format('M d, Y H:i') }}</li>
                        <li class="mb-1"><strong>Last Updated:</strong> {{ $record->updated_at->format('M d, Y H:i') }}</li>
                        <li><strong>Processed By:</strong> {{ $record->processed_by ? \App\Models\User::find($record->processed_by)->name : 'System Generated' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableInvoice, #printableInvoice * {
            visibility: visible;
        }
        #printableInvoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            border: none !important;
        }
        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }
    }
</style>
@endsection
