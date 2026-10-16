@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Expense Claims</h2>
            <p class="text-muted mb-0">Review, approve, and manage employee reimbursement requests.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Report</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createClaimModal">
                <i class="bi bi-receipt me-1"></i> Log Claim
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pending Amount</h6>
                            <h3 class="mb-0 fw-bold">${{ number_format($stats['total_pending_amount'], 2) }}</h3>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-white fs-4">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pending Review</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-white fs-4">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Approved for Payout</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['approved'] }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Settled / Paid</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['paid'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Claims List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Recent Claims</h5>
            <ul class="nav nav-pills card-header-pills" id="claimsTabs">
                <li class="nav-item">
                    <a class="nav-link active py-1 px-3 fs-6" href="#">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1 px-3 fs-6 text-muted" href="#">Pending</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1 px-3 fs-6 text-muted" href="#">Approved</a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            @if($claims->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="text-muted mb-3"><i class="bi bi-receipt" style="font-size: 4rem;"></i></div>
                    <h5 class="text-muted">No expense claims found</h5>
                    <p class="text-muted text-center mb-4">No employees have submitted expense claims yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0">Claim ID</th>
                                <th class="py-3 px-4 border-0">Employee</th>
                                <th class="py-3 px-4 border-0">Date & Category</th>
                                <th class="py-3 px-4 border-0">Amount</th>
                                <th class="py-3 px-4 border-0">Status</th>
                                <th class="py-3 px-4 border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claims as $claim)
                                <tr>
                                    <td class="px-4 fw-semibold text-muted">
                                        EXP-{{ str_pad($claim->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $claim->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($claim->user->name).'&background=random' }}" alt="{{ $claim->user->name }}" class="rounded-circle me-3" style="width: 35px; height: 35px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $claim->user->name }}</h6>
                                                <small class="text-muted">{{ $claim->user->hrProfile->department ?? 'General' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        <div class="mb-1">{{ $claim->claim_date->format('d M Y') }}</div>
                                        <span class="badge bg-light text-dark border">{{ $claim->category }}</span>
                                    </td>
                                    <td class="px-4 fw-bold">
                                        {{ $claim->currency }} {{ number_format($claim->amount, 2) }}
                                    </td>
                                    <td class="px-4">
                                        @if($claim->status === 'Pending')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                        @elseif($claim->status === 'Approved')
                                            <span class="badge bg-info"><i class="bi bi-check2 me-1"></i> Approved</span>
                                        @elseif($claim->status === 'Paid')
                                            <span class="badge bg-success"><i class="bi bi-cash me-1"></i> Paid</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewClaimModal{{ $claim->id }}">
                                            Review
                                        </button>
                                    </td>
                                </tr>

                                <!-- View/Review Claim Modal -->
                                <div class="modal fade" id="viewClaimModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Review Claim: EXP-{{ str_pad($claim->id, 4, '0', STR_PAD_LEFT) }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded border">
                                                    <img src="{{ $claim->user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($claim->user->name).'&background=random' }}" alt="{{ $claim->user->name }}" class="rounded-circle me-3 border" style="width: 48px; height: 48px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $claim->user->name }}</h6>
                                                        <small class="text-muted">{{ $claim->user->hrProfile->job_title ?? 'Staff' }} • {{ $claim->user->hrProfile->department ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="row g-3 mb-4">
                                                    <div class="col-6">
                                                        <label class="text-muted small fw-bold text-uppercase">Claim Date</label>
                                                        <div>{{ $claim->claim_date->format('d M Y') }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="text-muted small fw-bold text-uppercase">Category</label>
                                                        <div>{{ $claim->category }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="text-muted small fw-bold text-uppercase">Amount Requested</label>
                                                        <div class="h5 mb-0 fw-bold text-primary">{{ $claim->currency }} {{ number_format($claim->amount, 2) }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="text-muted small fw-bold text-uppercase">Receipt/Proof</label>
                                                        <div>
                                                            @if($claim->receipt_path)
                                                                <a href="{{ Storage::url($claim->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-paperclip me-1"></i> View Receipt</a>
                                                            @else
                                                                <span class="text-muted fst-italic">No receipt provided</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label class="text-muted small fw-bold text-uppercase mb-1">Description</label>
                                                    <div class="p-3 bg-light rounded text-muted small border">
                                                        {{ $claim->description }}
                                                    </div>
                                                </div>
                                                
                                                @if($claim->status === 'Rejected')
                                                <div class="mb-3">
                                                    <label class="text-danger small fw-bold text-uppercase mb-1">Rejection Reason</label>
                                                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded small border border-danger border-opacity-25">
                                                        {{ $claim->rejection_reason }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Action Form -->
                                            <div class="modal-footer bg-light">
                                                <form action="{{ route('human-resources.expense-claims.update', $claim->id) }}" method="POST" class="w-100">
                                                    @csrf
                                                    
                                                    @if($claim->status === 'Pending')
                                                        <div class="mb-3" id="rejectReasonDiv{{ $claim->id }}" style="display: none;">
                                                            <label class="form-label text-danger fw-bold">Reason for Rejection</label>
                                                            <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Explain why this claim is rejected..."></textarea>
                                                        </div>
                                                        <div class="d-flex gap-2 justify-content-end">
                                                            <button type="button" class="btn btn-outline-danger" onclick="document.getElementById('rejectReasonDiv{{ $claim->id }}').style.display='block'; this.style.display='none'; document.getElementById('confirmRejectBtn{{ $claim->id }}').style.display='block'; document.getElementById('approveBtn{{ $claim->id }}').style.display='none';">Reject Claim</button>
                                                            
                                                            <button type="submit" name="status" value="Rejected" class="btn btn-danger" id="confirmRejectBtn{{ $claim->id }}" style="display: none;">Confirm Rejection</button>
                                                            
                                                            <button type="submit" name="status" value="Approved" class="btn btn-success" id="approveBtn{{ $claim->id }}">Approve for Payout</button>
                                                        </div>
                                                    @elseif($claim->status === 'Approved')
                                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                                            <span class="text-success"><i class="bi bi-check-circle me-1"></i> Approved by {{ $claim->approver->name ?? 'HR' }}</span>
                                                            <button type="submit" name="status" value="Paid" class="btn btn-primary">Mark as Paid / Settled</button>
                                                        </div>
                                                    @elseif($claim->status === 'Paid')
                                                        <div class="w-100 text-center text-success fw-bold">
                                                            <i class="bi bi-cash-stack me-2"></i> This claim has been fully settled and paid.
                                                        </div>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Claim Modal (For Admin/HR logging on behalf) -->
<div class="modal fade" id="createClaimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.expense-claims.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Log Expense Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->hrProfile->department ?? 'General' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Claim Date</label>
                            <input type="date" name="claim_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Travel">Travel & Transport</option>
                                <option value="Meals">Meals & Entertainment</option>
                                <option value="Supplies">Office Supplies / Hardware</option>
                                <option value="Training">Training & Education</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" class="form-control" required step="0.01" min="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Business Purpose</label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="e.g. Flight to conference in London"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt / Proof (Optional)</label>
                        <input type="file" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Claim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
