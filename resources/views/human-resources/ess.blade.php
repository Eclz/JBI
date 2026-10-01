@extends('layouts.app')

@section('title', 'Employee Self Service')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Employee Self Service (ESS)</h2>
            <p class="text-muted mb-0">Manage your employment profile, leaves, and time tracking.</p>
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

    <div class="row g-4">
        <!-- Quick Links -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i>My Profile</h5>
                    <p class="text-muted small mb-4">View and update your personal information and contact details.</p>
                    <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-primary w-100 mb-2">View Profile Directory</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-calendar-event text-success me-2"></i>Leave Requests</h5>
                    <p class="text-muted small mb-4">Apply for annual, sick, or personal leave, and view balances.</p>
                    <a href="{{ route('human-resources.leaves.index') }}" class="btn btn-outline-success w-100 mb-2">My Leaves</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i>Time & Attendance</h5>
                    <p class="text-muted small mb-4">Log your working hours and view timesheets.</p>
                    
                    @if(isset($activeLog) && $activeLog)
                        <form action="{{ route('human-resources.ess.clock-out') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 mb-2 fw-bold">
                                <i class="bi bi-stop-circle me-1"></i> Clock Out (Started at {{ \Carbon\Carbon::parse($activeLog->clock_in)->format('h:i A') }})
                            </button>
                        </form>
                    @else
                        <form action="{{ route('human-resources.ess.clock-in') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100 mb-2 fw-bold">
                                <i class="bi bi-play-circle me-1"></i> Clock In Now
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('human-resources.sections.show', 'time-tracking') }}" class="btn btn-link text-decoration-none p-0 mt-2 small">View Timesheets <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-warning me-2"></i>Expense Claims</h5>
                    <p class="text-muted small mb-4">Submit requests for reimbursement of business expenses.</p>
                    <button type="button" class="btn btn-outline-warning text-dark w-100 mb-2" data-bs-toggle="modal" data-bs-target="#createEssClaimModal">
                        Submit New Claim
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create ESS Claim Modal -->
<div class="modal fade" id="createEssClaimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('human-resources.ess.expense-claims.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Submit Expense Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
