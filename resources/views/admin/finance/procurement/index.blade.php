@extends('layouts.app')

@section('title', 'Procurement & Requisitions')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark"><i class="bi bi-cart-check me-2"></i>Procurement & Requisitions</h1>
            <p class="text-muted mb-0">Review, approve, or reject asset and equipment requisitions from university departments.</p>
        </div>
        <div>
            <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary fw-bold">Back to Finance Hub</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Req. Number</th>
                            <th>Department</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Est. Cost</th>
                            <th>Requested By</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Action / Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requisitions as $req)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">{{ $req->requisition_number }}</td>
                                <td class="fw-bold">{{ $req->department->name ?? 'General' }}</td>
                                <td>{{ $req->item_description }}</td>
                                <td>{{ $req->quantity }}</td>
                                <td class="fw-bold text-success">{{ $currencyCode }} {{ number_format($req->estimated_cost, 2) }}</td>
                                <td>{{ $req->requester->name ?? 'Unknown' }}</td>
                                <td class="text-muted small">{{ $req->created_at->format('M d, Y') }}</td>
                                <td class="text-end pe-4">
                                    @if($req->status === 'Pending')
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('admin.finance.procurement.approve', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success fw-bold px-3" onclick="return confirm('Approve this requisition?')">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.finance.procurement.reject', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" onclick="return confirm('Reject this requisition?')">Reject</button>
                                            </form>
                                        </div>
                                    @elseif($req->status === 'Approved')
                                        <span class="badge bg-success rounded-pill px-3 py-2">Approved</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No procurement requisitions submitted yet.
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
@endsection
