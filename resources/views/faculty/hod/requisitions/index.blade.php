@extends('layouts.app')

@section('title', 'Departmental Requisitions')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark"><i class="bi bi-cart4 me-2"></i>Departmental Requisitions</h1>
            <p class="text-muted mb-0">Request lab equipment, computer hardware, or teaching materials for {{ $department->name }}.</p>
        </div>
        <div>
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#newRequisitionModal">
                <i class="bi bi-plus-lg me-1"></i>New Requisition
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Requisition ID</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Est. Cost</th>
                            <th>Date Requested</th>
                            <th class="text-end pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requisitions as $req)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $req->requisition_number }}</td>
                                <td class="fw-bold">{{ $req->item_description }}</td>
                                <td>{{ $req->quantity }}</td>
                                <td>${{ number_format($req->estimated_cost, 2) }}</td>
                                <td class="text-muted">{{ $req->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    @if($req->status === 'Pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending Finance Approval</span>
                                    @elseif($req->status === 'Approved')
                                        <span class="badge bg-success rounded-pill px-3 py-2">Approved</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No requisitions submitted yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requisitions->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $requisitions->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="newRequisitionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('faculty.hod.requisitions.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>New Requisition</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Description <span class="text-danger">*</span></label>
                        <input type="text" name="item_description" class="form-control" placeholder="e.g. Physics Lab Oscilloscopes" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Total Estimated Cost ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="estimated_cost" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 mt-4 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> Submitting this form will send a formal request to the University Finance / Procurement department for approval.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
