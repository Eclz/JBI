@extends('layouts.app')

@section('title', 'Revenue & Income Streams')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-currency-exchange me-2"></i>Revenue & Income Streams
            </h1>
            <p class="text-muted mb-0">Record and track non-tuition revenues, research grants, donations, facility rentals & consultancy income</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addRevenueModal">
                <i class="bi bi-plus-lg me-1"></i>Record New Revenue
            </button>
            <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary">Back to Finance Hub</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white border-start border-4 border-success">
                <div class="card-body p-3">
                    <span class="text-muted text-uppercase fw-bold small">Total Recorded Revenue</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $currencyCode }} {{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Category</th>
                            <th>Title / Description</th>
                            <th>Payer / Source</th>
                            <th>Method & Ref</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $rev)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-primary">{{ $rev->revenue_code }}</td>
                                <td><span class="badge bg-info text-dark px-2 py-1">{{ $rev->category }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $rev->title }}</div>
                                    <small class="text-muted">{{ $rev->notes }}</small>
                                </td>
                                <td>{{ $rev->payer_name ?? 'Government / Donor' }}</td>
                                <td class="small">
                                    <div><i class="bi bi-credit-card me-1"></i>{{ $rev->payment_method ?? 'N/A' }}</div>
                                    <small class="text-muted font-monospace">{{ $rev->reference_number ?? '-' }}</small>
                                </td>
                                <td class="small">{{ $rev->transaction_date->format('M d, Y') }}</td>
                                <td class="text-end pe-3 fw-bold text-success">{{ $currencyCode }} {{ number_format($rev->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No revenue records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($revenues->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $revenues->links() }}</div>
        @endif
    </div>
</div>

<!-- Add Revenue Modal -->
<div class="modal fade" id="addRevenueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.finance.revenue.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Record Non-Tuition Revenue</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Revenue Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="Government Funding">Government Funding / Subvention</option>
                                <option value="Research Grant">Research Grant</option>
                                <option value="Donation">Donation & Endowment</option>
                                <option value="Facility Rental">Facility Rental</option>
                                <option value="Consultancy Income">Consultancy Income</option>
                                <option value="Miscellaneous">Miscellaneous Revenue</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title / Description <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Auditorium Rental Income" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payer / Source Name</label>
                            <input type="text" name="payer_name" class="form-control" placeholder="e.g. Ministry of Education">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="e.g. Bank Transfer, Cheque">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reference / Receipt Number</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="e.g. EFT-99201">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Revenue Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
