@extends('layouts.app')

@section('title', 'Banking & Cash Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-piggy-bank me-2"></i>Banking & Cash Management
            </h1>
            <p class="text-muted mb-0">Register university bank accounts, monitor cash balances & petty cash disbursements</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="bi bi-plus-lg me-1"></i>Add Bank Account
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

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Bank Name</th>
                            <th>Account Name</th>
                            <th>Account Number</th>
                            <th>Branch</th>
                            <th>Currency</th>
                            <th class="text-end pe-3">Current Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $acc)
                            <tr>
                                <td class="ps-3 fw-bold text-dark fs-6"><i class="bi bi-bank me-2 text-primary"></i>{{ $acc->bank_name }}</td>
                                <td class="fw-semibold">{{ $acc->account_name }}</td>
                                <td class="font-monospace fw-bold text-primary">{{ $acc->account_number }}</td>
                                <td class="small text-muted">{{ $acc->branch ?? 'Main Branch' }}</td>
                                <td><span class="badge bg-secondary">{{ $acc->currency ?? 'UGX' }}</span></td>
                                <td class="text-end pe-3 fw-bold text-success fs-6">{{ $acc->currency ?? 'UGX' }} {{ number_format($acc->current_balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No bank accounts registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.finance.banking.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-bank me-2"></i>Add Bank Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" placeholder="e.g. Stanbic Bank Uganda" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control" placeholder="e.g. JBI University Main Collection" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" placeholder="9030001234567" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Branch Location</label>
                        <input type="text" name="branch" class="form-control" placeholder="Kampala Main Branch">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Opening Balance (UGX) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="current_balance" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Bank Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
