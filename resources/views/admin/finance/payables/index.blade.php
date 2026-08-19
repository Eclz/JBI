@extends('layouts.app')

@section('title', 'Accounts Payable & Vendor Invoices')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-truck me-2"></i>Accounts Payable & Suppliers
            </h1>
            <p class="text-muted mb-0">Manage university vendors, registered suppliers, incoming invoices, and payment disbursements</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-person-plus me-1"></i>New Supplier
            </button>
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                <i class="bi bi-file-earmark-plus me-1"></i>Record Vendor Invoice
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

    <div class="row g-4 mb-4">
        <!-- Registered Suppliers List -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i>Registered Vendors / Suppliers</h5>
                    <span class="badge bg-primary rounded-pill">{{ count($suppliers) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Code</th>
                                    <th>Company Name</th>
                                    <th>Contact</th>
                                    <th class="text-end pe-3">Invoices</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $sup)
                                    <tr>
                                        <td class="ps-3 font-monospace small fw-bold text-primary">{{ $sup->supplier_code }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $sup->company_name }}</div>
                                            <small class="text-muted">{{ $sup->tax_pin ?? 'TIN N/A' }}</small>
                                        </td>
                                        <td class="small">
                                            <div>{{ $sup->contact_person }}</div>
                                            <small class="text-muted">{{ $sup->phone }}</small>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="badge bg-secondary px-2 py-1">{{ $sup->invoices_count }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No suppliers registered.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Invoices List -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-receipt me-2 text-primary"></i>Vendor Invoices & Payables</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Invoice #</th>
                                    <th>Supplier</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td class="ps-3 font-monospace fw-bold text-dark">{{ $inv->invoice_number }}</td>
                                        <td class="fw-bold text-primary">{{ $inv->supplier->company_name ?? 'Supplier' }}</td>
                                        <td class="small">{{ $inv->due_date ? $inv->due_date->format('M d, Y') : '-' }}</td>
                                        <td>
                                            @if($inv->status === 'paid')
                                                <span class="badge bg-success px-2 py-1">Paid</span>
                                            @elseif($inv->status === 'partial')
                                                <span class="badge bg-warning text-dark px-2 py-1">Partial</span>
                                            @else
                                                <span class="badge bg-danger px-2 py-1">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-dark">{{ $currencyCode }} {{ number_format($inv->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No vendor invoices recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.finance.payables.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Register New Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Dell Technologies" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="John Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tax PIN / TIN</label>
                        <input type="text" name="tax_pin" class="form-control" placeholder="TIN-902192">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="addInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.finance.payables.invoices.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Record Vendor Invoice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">-- Choose Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_number" class="form-control" placeholder="INV-2026-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Vendor Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
