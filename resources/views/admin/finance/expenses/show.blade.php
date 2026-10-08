@extends('layouts.app')

@section('title', 'Expense Requisition Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-receipt-cutoff me-2"></i>Expense Requisition: {{ $expense->expense_number ?? 'EXP-'.str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-muted mb-0">Detailed view of expenditure request and approval status</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary fw-bold me-2">
                <i class="bi bi-printer me-1"></i>Print Document
            </button>
            <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-secondary fw-bold">
                <i class="bi bi-arrow-left me-1"></i>Back to Expenses
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Document Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white" id="printableExpense">
                <!-- Document Header -->
                <div class="row border-bottom pb-4 mb-4 align-items-center">
                    <div class="col-sm-6">
                        <h2 class="fw-bold text-dark mb-1">EXPENDITURE VOUCHER</h2>
                        <h5 class="text-muted font-monospace mb-0">#{{ $expense->expense_number ?? 'EXP-'.str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        @if(strtolower($expense->status) === 'approved' || strtolower($expense->status) === 'paid')
                            <h3 class="text-success fw-bold text-uppercase mb-0 border border-success d-inline-block px-3 py-1 rounded-3 transform-rotate">APPROVED</h3>
                        @elseif(strtolower($expense->status) === 'rejected')
                            <h3 class="text-danger fw-bold text-uppercase mb-0 border border-danger d-inline-block px-3 py-1 rounded-3 transform-rotate">REJECTED</h3>
                        @else
                            <h3 class="text-warning fw-bold text-uppercase mb-0 border border-warning d-inline-block px-3 py-1 rounded-3 transform-rotate">PENDING</h3>
                        @endif
                    </div>
                </div>

                <!-- Requisition Details -->
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Requested By:</div>
                        <h6 class="fw-bold text-dark mb-1">{{ $expense->requester->name ?? 'N/A' }}</h6>
                        <div class="text-muted small">{{ $expense->department->name ?? 'General Administration' }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <table class="table table-sm table-borderless text-end mb-0">
                            <tr>
                                <td class="text-muted small fw-semibold pb-1">Expense Date:</td>
                                <td class="fw-bold text-dark pb-1">{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small fw-semibold pt-0">Category:</td>
                                <td class="fw-bold text-primary pt-0">{{ $expense->category ?? 'General' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Expense Title & Description -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-2">{{ $expense->title }}</h5>
                    <div class="p-3 bg-light rounded-3 border">
                        <p class="mb-0 text-dark">{{ $expense->description ?? 'No detailed description provided.' }}</p>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase small fw-bold text-muted py-3 w-75">Particulars</th>
                                <th class="text-uppercase small fw-bold text-muted py-3 text-end w-25">Amount ({{ $currencyCode }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">{{ $expense->title }}</div>
                                    <div class="small text-muted">{{ $expense->category ?? 'General Expense' }}</div>
                                </td>
                                <td class="text-end fw-bold py-3 fs-5 text-dark">{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="border-top border-2 border-dark">
                            <tr>
                                <td class="text-end fw-bold text-uppercase py-3">Total Approved Amount:</td>
                                <td class="text-end fw-bold text-success fs-4 py-3">{{ $currencyCode }} {{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Signatures -->
                <div class="row mt-5 pt-4 text-center border-top">
                    <div class="col-4">
                        <div class="border-bottom border-dark border-1 mx-4 mb-2" style="height: 30px;"></div>
                        <div class="small fw-bold text-uppercase text-muted">Requested By</div>
                        <div class="small">{{ $expense->requester->name ?? 'Signature' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="border-bottom border-dark border-1 mx-4 mb-2" style="height: 30px;">
                            @if(strtolower($expense->status) === 'approved' || strtolower($expense->status) === 'paid')
                                <span class="text-success" style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem;">Approved</span>
                            @endif
                        </div>
                        <div class="small fw-bold text-uppercase text-muted">Authorized By</div>
                        <div class="small">{{ $expense->approver->name ?? 'Finance Director' }}</div>
                    </div>
                    <div class="col-4">
                        <div class="border-bottom border-dark border-1 mx-4 mb-2" style="height: 30px;"></div>
                        <div class="small fw-bold text-uppercase text-muted">Disbursed By</div>
                        <div class="small">Bursar / Cashier</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Disbursement Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold text-uppercase">Payment Method</label>
                        <div class="fw-bold">{{ $expense->payment_method ? ucfirst(str_replace('_', ' ', $expense->payment_method)) : 'Pending Disbursement' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-semibold text-uppercase">Department / Cost Center</label>
                        <div class="fw-bold text-primary">{{ $expense->department->name ?? 'General Administration' }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 border-top border-primary border-4">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle me-1"></i>Audit Trail</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-1"><strong>Requested On:</strong> {{ $expense->created_at->format('M d, Y H:i') }}</li>
                        <li class="mb-1"><strong>Last Updated:</strong> {{ $expense->updated_at->format('M d, Y H:i') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transform-rotate {
        transform: rotate(-5deg);
        letter-spacing: 2px;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #printableExpense, #printableExpense * {
            visibility: visible;
        }
        #printableExpense {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            border: none !important;
        }
        .badge, .border {
            border: 1px solid #000 !important;
            color: #000 !important;
        }
    }
</style>
@endsection
