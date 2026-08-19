@extends('layouts.app')

@section('title', 'Asset Management & Depreciation')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-qr-code-scan me-2"></i>University Asset Management & Depreciation
            </h1>
            <p class="text-muted mb-0">Register capital assets, barcode/QR tagging, location assignment, and annual depreciation tracking</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                <i class="bi bi-plus-lg me-1"></i>Register New Asset
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
            <div class="card border-0 shadow-sm rounded-3 bg-white border-start border-4 border-info">
                <div class="card-body p-3">
                    <span class="text-muted text-uppercase fw-bold small">Total Current Asset Valuation</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $currencyCode }} {{ number_format($totalAssetValue, 2) }}</h3>
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
                            <th class="ps-3">Asset Tag</th>
                            <th>Asset Name</th>
                            <th>Category</th>
                            <th>Department</th>
                            <th>Location</th>
                            <th>Purchase Cost</th>
                            <th class="text-end pe-3">Current Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $ast)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-primary">{{ $ast->asset_tag }}</td>
                                <td class="fw-bold text-dark">{{ $ast->asset_name }}</td>
                                <td><span class="badge bg-secondary px-2 py-1">{{ $ast->category }}</span></td>
                                <td>{{ $ast->department->name ?? 'General' }}</td>
                                <td class="small">{{ $ast->location ?? 'Main Campus' }}</td>
                                <td class="fw-semibold text-muted">{{ $currencyCode }} {{ number_format($ast->purchase_cost, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-success">{{ $currencyCode }} {{ number_format($ast->current_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No university assets registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assets->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $assets->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.finance.assets.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Register Capital Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" name="asset_name" class="form-control" placeholder="e.g. AI Server Workstation" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="Computer Hardware">Computer Hardware</option>
                                <option value="Laboratory Equipment">Laboratory Equipment</option>
                                <option value="Furniture & Fittings">Furniture & Fittings</option>
                                <option value="Motor Vehicles">Motor Vehicles</option>
                                <option value="Buildings & Land">Buildings & Land</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">-- Choose Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Purchase Cost ({{ $currencyCode }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="purchase_cost" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room / Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Room 102, Tech Center">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Register Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
