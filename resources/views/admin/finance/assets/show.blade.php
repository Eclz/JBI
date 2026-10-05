@extends('layouts.app')

@section('title', 'Asset Details: ' . $asset->asset_tag)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark"><i class="bi bi-info-square me-2"></i>Asset Details</h1>
            <p class="text-muted mb-0">View comprehensive information, depreciation, and QR code for this asset.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary fw-bold">
                <i class="bi bi-printer me-1"></i> Print Asset Tag
            </button>
            <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-outline-secondary fw-bold">Back to Assets</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark">Asset Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Asset Name</p>
                            <h5 class="fw-bold text-dark">{{ $asset->asset_name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Asset Tag / ID</p>
                            <h5 class="font-monospace fw-bold text-primary">{{ $asset->asset_tag }}</h5>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <p class="text-muted small fw-bold mb-1"><i class="bi bi-tags me-1"></i> Category</p>
                                <span class="badge bg-secondary px-2 py-1 fs-6">{{ $asset->category }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <p class="text-muted small fw-bold mb-1"><i class="bi bi-building me-1"></i> Department</p>
                                <span class="fw-bold text-dark">{{ $asset->department->name ?? 'General (Not Assigned)' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <p class="text-muted small fw-bold mb-1"><i class="bi bi-geo-alt me-1"></i> Location / Room</p>
                                <span class="fw-bold text-dark">{{ $asset->location ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-5 mb-3 border-bottom pb-2">Financial & Depreciation Details</h5>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Purchase Cost</p>
                            <h5 class="fw-bold text-dark">${{ number_format($asset->purchase_cost, 2) }}</h5>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Purchase Date</p>
                            <span class="fw-bold text-dark">{{ $asset->purchase_date->format('M d, Y') }}</span>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Depreciation Rate</p>
                            <span class="fw-bold text-danger">{{ $asset->annual_depreciation_rate ?? '0' }}% per year</span>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted small text-uppercase fw-bold mb-1">Current Value</p>
                            <h5 class="fw-bold text-success">${{ number_format($asset->current_value, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Printable QR Code Tag -->
            <div class="card border-0 shadow-sm text-center" style="border-radius: 12px; border: 2px dashed #ccc !important;">
                <div class="card-body p-5">
                    <h5 class="fw-bold text-dark mb-4 text-uppercase">JBI University Property</h5>
                    
                    <div class="bg-white p-2 d-inline-block rounded mb-3 shadow-sm border">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('admin.finance.assets.show', $asset->id)) }}&margin=10" alt="QR Code for {{ $asset->asset_tag }}" class="img-fluid" style="width: 200px; height: 200px;">
                    </div>
                    
                    <h4 class="font-monospace fw-bold text-primary mb-1">{{ $asset->asset_tag }}</h4>
                    <p class="text-muted small fw-bold mb-0">{{ $asset->asset_name }}</p>
                    
                    <div class="mt-4 pt-3 border-top d-print-none">
                        <p class="text-muted small mb-0"><i class="bi bi-upc-scan me-1"></i> Scan to view asset details on mobile.</p>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4 d-print-none" style="border-radius: 12px;">
                <div class="card-body p-4 text-center">
                    <p class="text-muted small fw-bold mb-2">Asset Status</p>
                    @if($asset->status === 'Active' || $asset->status === 'In Use')
                        <span class="badge bg-success rounded-pill px-4 py-2 fs-6">Active / In Use</span>
                    @elseif($asset->status === 'Under Maintenance')
                        <span class="badge bg-warning text-dark rounded-pill px-4 py-2 fs-6">Under Maintenance</span>
                    @else
                        <span class="badge bg-secondary rounded-pill px-4 py-2 fs-6">{{ $asset->status ?? 'Active' }}</span>
                    @endif
                    
                    <div class="d-grid mt-4">
                        <a href="{{ route('admin.finance.assets.edit', $asset->id) }}" class="btn btn-outline-primary fw-bold"><i class="bi bi-pencil me-2"></i>Edit Asset Information</a>
                    </div>
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
        .container-fluid {
            padding: 0 !important;
        }
        .col-lg-4 {
            width: 100% !important;
            float: none !important;
        }
        .col-lg-4 .card:first-child, .col-lg-4 .card:first-child * {
            visibility: visible;
        }
        .col-lg-4 .card:first-child {
            position: absolute;
            left: 50%;
            top: 20%;
            transform: translate(-50%, -20%);
            width: 80mm;
            border: 2px solid #000 !important;
            box-shadow: none !important;
            padding: 10mm !important;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>
@endsection
