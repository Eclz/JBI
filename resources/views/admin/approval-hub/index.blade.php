@extends('layouts.app')

@section('title', 'Central Approval Hub')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1" style="color: #1e293b; font-weight: 600;">Central Approval Hub</h1>
            <p class="text-muted mb-0">Review, approve, or reject submissions made by Deans across faculty modules.</p>
        </div>
        <div class="btn-group" role="group" aria-label="Status Filter">
            <a href="{{ route('admin.approval-hub.index', ['status' => 'all_active']) }}" class="btn btn-sm {{ ($statusFilter ?? 'all_active') === 'all_active' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Active Submissions
            </a>
            <a href="{{ route('admin.approval-hub.index', ['status' => 'pending_only']) }}" class="btn btn-sm {{ ($statusFilter ?? '') === 'pending_only' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Pending Only
            </a>
            <a href="{{ route('admin.approval-hub.index', ['status' => 'all']) }}" class="btn btn-sm {{ ($statusFilter ?? '') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                All History
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <ul class="nav nav-tabs border-bottom-0" id="approvalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-medium" id="quality-tab" data-bs-toggle="tab" data-bs-target="#quality" type="button" role="tab" aria-controls="quality" aria-selected="true">
                        Academic Quality
                        @if($qualityReviews->count() > 0)
                            <span class="badge bg-danger ms-1 rounded-pill">{{ $qualityReviews->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-medium" id="evaluations-tab" data-bs-toggle="tab" data-bs-target="#evaluations" type="button" role="tab" aria-controls="evaluations" aria-selected="false">
                        Faculty Evaluations
                        @if($evaluations->count() > 0)
                            <span class="badge bg-danger ms-1 rounded-pill">{{ $evaluations->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-medium" id="budgets-tab" data-bs-toggle="tab" data-bs-target="#budgets" type="button" role="tab" aria-controls="budgets" aria-selected="false">
                        Budget Requests
                        @if($budgetRequests->count() > 0)
                            <span class="badge bg-danger ms-1 rounded-pill">{{ $budgetRequests->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-medium" id="issues-tab" data-bs-toggle="tab" data-bs-target="#issues" type="button" role="tab" aria-controls="issues" aria-selected="false">
                        Student Issues
                        @if($studentIssues->count() > 0)
                            <span class="badge bg-danger ms-1 rounded-pill">{{ $studentIssues->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-medium" id="partnerships-tab" data-bs-toggle="tab" data-bs-target="#partnerships" type="button" role="tab" aria-controls="partnerships" aria-selected="false">
                        Partnerships
                        @if($partnerships->count() > 0)
                            <span class="badge bg-danger ms-1 rounded-pill">{{ $partnerships->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-medium" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">
                        Student Payments
                        @if($studentPayments->count() > 0)
                            <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $studentPayments->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0 border-top">
            <div class="tab-content" id="approvalTabsContent">
                
                <!-- Academic Quality Tab -->
                <div class="tab-pane fade show active" id="quality" role="tabpanel" aria-labelledby="quality-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Program</th>
                                    <th>Review Title</th>
                                    <th>Submitted By</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($qualityReviews as $review)
                                    <tr>
                                        <td>{{ $review->program->name ?? 'N/A' }}</td>
                                        <td><strong>{{ $review->title }}</strong></td>
                                        <td>{{ $review->dean->full_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($review->status === 'Approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($review->status === 'Rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($review->status === 'Pending Registrar Approval' || $review->status === 'Pending Approval')
                                                <span class="badge bg-warning text-dark">Pending Approval</span>
                                            @else
                                                <span class="badge bg-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->updated_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qualityModal{{ $review->id }}">Review</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No academic quality reviews found for this view.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Faculty Evaluations Tab -->
                <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Faculty Member</th>
                                    <th>Evaluator (Dean)</th>
                                    <th>Year</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->faculty->full_name ?? 'N/A' }}</td>
                                        <td>{{ $evaluation->evaluator->full_name ?? 'N/A' }}</td>
                                        <td>{{ $evaluation->academic_year }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $evaluation->performance_score }} / 100</span></td>
                                        <td>
                                            @if($evaluation->status === 'Approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($evaluation->status === 'Rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($evaluation->status === 'Pending Approval')
                                                <span class="badge bg-warning text-dark">Pending Approval</span>
                                            @else
                                                <span class="badge bg-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#evalModal{{ $evaluation->id }}">Review</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No faculty evaluations found for this view.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Budget Requests Tab -->
                <div class="tab-pane fade" id="budgets" role="tabpanel" aria-labelledby="budgets-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Department</th>
                                    <th>Title</th>
                                    <th>Amount ({{ $systemCurrency }})</th>
                                    <th>Requester</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budgetRequests as $budget)
                                    <tr>
                                        <td>{{ $budget->department->name ?? 'N/A' }}</td>
                                        <td>{{ $budget->title }}</td>
                                        <td class="font-monospace text-primary fw-medium">{{ $systemCurrency }}{{ number_format($budget->amount, 2) }}</td>
                                        <td>{{ $budget->requester->full_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($budget->status === 'Approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($budget->status === 'Rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($budget->status === 'Pending Finance Approval')
                                                <span class="badge bg-warning text-dark">Pending Finance Approval</span>
                                            @else
                                                <span class="badge bg-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetModal{{ $budget->id }}">Review</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No budget requests found for this view.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Student Issues Tab -->
                <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Issue Type</th>
                                    <th>Reporter (Dean)</th>
                                    <th>Status</th>
                                    <th>Date Reported</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentIssues as $issue)
                                    <tr>
                                        <td>{{ $issue->student->full_name ?? 'N/A' }} ({{ $issue->student->admission_number ?? 'N/A' }})</td>
                                        <td>
                                            @if($issue->issue_type == 'Academic') <span class="badge bg-primary">Academic</span>
                                            @elseif($issue->issue_type == 'Disciplinary') <span class="badge bg-danger">Disciplinary</span>
                                            @else <span class="badge bg-info text-dark">Welfare</span>
                                            @endif
                                        </td>
                                        <td>{{ $issue->reporter->full_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($issue->status === 'Resolved')
                                                <span class="badge bg-success">Resolved</span>
                                            @elseif($issue->status === 'Escalated')
                                                <span class="badge bg-danger">Escalated</span>
                                            @elseif($issue->status === 'In Progress')
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            @else
                                                <span class="badge bg-secondary">Open</span>
                                            @endif
                                        </td>
                                        <td>{{ $issue->updated_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#issueModal{{ $issue->id }}">Review & Resolve</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No student issues found for this view.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Partnerships Tab -->
                <div class="tab-pane fade" id="partnerships" role="tabpanel" aria-labelledby="partnerships-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Organization</th>
                                    <th>Type</th>
                                    <th>Expected Funding ({{ $systemCurrency }})</th>
                                    <th>Manager (Dean)</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partnerships as $partnership)
                                    <tr>
                                        <td><strong>{{ $partnership->organization_name }}</strong></td>
                                        <td>{{ $partnership->partnership_type }}</td>
                                        <td class="font-monospace">
                                            @if($partnership->funding_amount > 0)
                                                {{ $systemCurrency }}{{ number_format($partnership->funding_amount, 2) }}
                                            @else
                                                <span class="text-muted fst-italic">Non-Financial</span>
                                            @endif
                                        </td>
                                        <td>{{ $partnership->manager->full_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($partnership->status === 'Active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($partnership->status === 'Concluded')
                                                <span class="badge bg-secondary">Concluded</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#partnershipModal{{ $partnership->id }}">Review</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No partnerships found for this view.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Student Payments Tab -->
                <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Fee Structure / Purpose</th>
                                    <th>Reference / PRN</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Proof</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentPayments as $payment)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                                            </span>
                                            <br><small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($payment->student)
                                                <strong>{{ $payment->student->name }}</strong>
                                                <br><small class="text-muted">{{ $payment->student->email }}</small>
                                                @if($payment->student->studentProfile?->admission_number)
                                                    <br><span class="badge bg-secondary font-monospace">{{ $payment->student->studentProfile->admission_number }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $payment->feeRecord?->feeStructure?->name ?? 'Institutional Fee' }}</strong>
                                            @if($payment->notes)
                                                <br><small class="text-muted">{{ Str::limit($payment->notes, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace">
                                                {{ $payment->reference_number ?: ($payment->transaction_id ?: 'N/A') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary text-white">
                                                {{ $payment->payment_method ?: 'Bank Transfer' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-6">
                                                {{ $currencyCode }} {{ number_format($payment->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($payment->status === 'completed')
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Approval</span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->payment_proof)
                                                <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-paperclip me-1"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted small">None</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($payment->status === 'pending' && auth()->user()->hasPermission('fees', 'approve'))
                                                <div class="btn-group" role="group">
                                                    <form method="POST" action="{{ route('admin.fees.payments.approve', $payment) }}" class="d-inline" onsubmit="return confirm('Approve payment of {{ $currencyCode }} {{ number_format($payment->amount, 2) }} and credit student account?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-danger ms-1"
                                                            onclick="openHubPaymentRejectModal({{ $payment->id }}, '{{ addslashes($payment->student?->name ?? 'Student') }}', '{{ $currencyCode }} {{ number_format($payment->amount, 2) }}', '{{ addslashes($payment->reference_number ?: $payment->transaction_id) }}')">
                                                        <i class="fas fa-times me-1"></i> Reject
                                                    </button>
                                                </div>
                                            @elseif($payment->status === 'completed')
                                                <a href="{{ route('admin.fees.payments.receipt', $payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-receipt me-1"></i> Receipt
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center py-5 text-muted">No student payments found for this view.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    color: #64748b;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 1rem 1.5rem;
}
.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #1e293b;
}
.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background-color: transparent;
}
</style>
@endsection

@push('modals')
    <!-- Academic Quality Modals -->
    @foreach($qualityReviews as $review)
        <div class="modal fade" id="qualityModal{{ $review->id }}" tabindex="-1" aria-labelledby="qualityModalLabel{{ $review->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qualityModalLabel{{ $review->id }}">Review: {{ $review->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <span class="text-muted d-block small">Program:</span>
                            <span class="fw-medium">{{ $review->program->name ?? 'N/A' }}</span>
                        </div>
                        <h6>Notes:</h6>
                        <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $review->review_notes }}</p>
                        
                        @if(is_array($review->metrics) && count($review->metrics) > 0)
                            <h6>Metrics:</h6>
                            <ul class="list-group list-group-flush mb-4">
                                @foreach($review->metrics as $metric)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $metric['name'] ?? '' }}
                                        <span class="badge bg-primary rounded-pill">{{ $metric['value'] ?? 0 }} / 5</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" id="qualityForm{{ $review->id }}" action="">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Feedback Notes (Required only for Rejection)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes for approval, or explanation if rejecting..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="qualityForm{{ $review->id }}" formaction="{{ route('admin.approval-hub.quality.reject', $review) }}" class="btn btn-danger">Reject</button>
                        <button type="submit" form="qualityForm{{ $review->id }}" formaction="{{ route('admin.approval-hub.quality.approve', $review) }}" class="btn btn-success">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Faculty Evaluation Modals -->
    @foreach($evaluations as $evaluation)
        <div class="modal fade" id="evalModal{{ $evaluation->id }}" tabindex="-1" aria-labelledby="evalModalLabel{{ $evaluation->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="evalModalLabel{{ $evaluation->id }}">Evaluate: {{ $evaluation->faculty->full_name ?? 'N/A' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Academic Year:</span>
                                <span class="fw-medium">{{ $evaluation->academic_year }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Performance Score:</span>
                                <span class="badge bg-info text-dark fs-6">{{ $evaluation->performance_score }} / 100</span>
                            </div>
                        </div>

                        <h6>Dean's Comments:</h6>
                        <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $evaluation->comments }}</p>
                        
                        <form method="POST" id="evalForm{{ $evaluation->id }}" action="">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Admin Feedback Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes to append..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="evalForm{{ $evaluation->id }}" formaction="{{ route('admin.approval-hub.evaluation.reject', $evaluation) }}" class="btn btn-danger">Reject</button>
                        <button type="submit" form="evalForm{{ $evaluation->id }}" formaction="{{ route('admin.approval-hub.evaluation.approve', $evaluation) }}" class="btn btn-success">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Budget Request Modals -->
    @foreach($budgetRequests as $budget)
        <div class="modal fade" id="budgetModal{{ $budget->id }}" tabindex="-1" aria-labelledby="budgetModalLabel{{ $budget->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="budgetModalLabel{{ $budget->id }}">Budget Request: {{ $budget->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Department:</span>
                                <span class="fw-medium">{{ $budget->department->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Requested Amount:</span>
                                <span class="fw-bold text-primary font-monospace fs-5">{{ $systemCurrency }}{{ number_format($budget->amount, 2) }}</span>
                            </div>
                        </div>

                        <h6>Description & Justification:</h6>
                        <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $budget->description }}</p>
                        
                        <form method="POST" id="budgetForm{{ $budget->id }}" action="">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Finance/Admin Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes for approval or revision..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="budgetForm{{ $budget->id }}" formaction="{{ route('admin.approval-hub.budget.reject', $budget) }}" class="btn btn-danger">Return for Revision</button>
                        <button type="submit" form="budgetForm{{ $budget->id }}" formaction="{{ route('admin.approval-hub.budget.approve', $budget) }}" class="btn btn-success">Approve Funding</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Student Issue Modals -->
    @foreach($studentIssues as $issue)
        <div class="modal fade" id="issueModal{{ $issue->id }}" tabindex="-1" aria-labelledby="issueModalLabel{{ $issue->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="issueModalLabel{{ $issue->id }}">Issue: {{ $issue->student->full_name ?? 'N/A' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Student:</span>
                                <span class="fw-medium">{{ $issue->student->full_name ?? 'N/A' }} ({{ $issue->student->admission_number ?? 'N/A' }})</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Issue Category:</span>
                                <span class="badge bg-secondary">{{ $issue->issue_type }}</span>
                            </div>
                        </div>

                        <h6>Description:</h6>
                        <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $issue->description }}</p>
                        
                        <h6>Prior Actions Taken by Dean:</h6>
                        <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $issue->action_taken ?? 'None recorded.' }}</p>
                        
                        <form method="POST" id="issueForm{{ $issue->id }}" action="{{ route('admin.approval-hub.student-issue.resolve', $issue) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Admin Resolution / Actions Taken</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Record the resolution or action taken..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="issueForm{{ $issue->id }}" class="btn btn-success">Mark as Resolved</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Partnership Modals -->
    @foreach($partnerships as $partnership)
        <div class="modal fade" id="partnershipModal{{ $partnership->id }}" tabindex="-1" aria-labelledby="partnershipModalLabel{{ $partnership->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="partnershipModalLabel{{ $partnership->id }}">Proposed Partnership: {{ $partnership->organization_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Expected Funding / Commitment:</span>
                                <span class="fw-bold font-monospace text-primary fs-5">
                                    @if($partnership->funding_amount > 0)
                                        {{ $systemCurrency }}{{ number_format($partnership->funding_amount, 2) }}
                                    @else
                                        Non-Financial
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block small">Partnership Type:</span>
                                <span class="fw-medium">{{ $partnership->partnership_type }}</span>
                            </div>
                        </div>

                        <h6>Strategic Objectives & Shared Outcomes:</h6>
                        <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $partnership->objectives }}</p>
                        
                        <form method="POST" id="partnershipForm{{ $partnership->id }}" action="">
                            @csrf
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="partnershipForm{{ $partnership->id }}" formaction="{{ route('admin.approval-hub.partnership.reject', $partnership) }}" class="btn btn-danger">Reject</button>
                        <button type="submit" form="partnershipForm{{ $partnership->id }}" formaction="{{ route('admin.approval-hub.partnership.approve', $partnership) }}" class="btn btn-success">Approve (Mark Active)</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Payment Reject Modal -->
    <div class="modal fade" id="hubPaymentRejectModal" tabindex="-1" aria-labelledby="hubPaymentRejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form id="hubPaymentRejectForm" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="hubPaymentRejectModalLabel"><i class="bi bi-x-circle me-2"></i>Reject Student Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle fs-3 me-3 text-warning"></i>
                                <div>
                                    <strong>Payment will be marked as Failed/Rejected.</strong>
                                    <div class="small">The student will be notified and the fee balance will remain unpaid.</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Student:</label>
                            <div id="hubRejectStudentName" class="fw-semibold"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Amount & Reference:</label>
                            <div><span id="hubRejectPaymentAmount" class="fw-bold text-danger"></span> (<span id="hubRejectPaymentRef" class="font-monospace"></span>)</div>
                        </div>
                        <div class="mb-3">
                            <label for="hub_rejection_reason" class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="hub_rejection_reason" name="rejection_reason" rows="3" required placeholder="Explain why this payment cannot be approved (e.g. invalid receipt, bank transfer not received, amount discrepancy)..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openHubPaymentRejectModal(paymentId, studentName, amount, ref) {
        document.getElementById('hubPaymentRejectForm').action = '/admin/fees/payments/' + paymentId + '/reject';
        document.getElementById('hubRejectStudentName').textContent = studentName;
        document.getElementById('hubRejectPaymentAmount').textContent = amount;
        document.getElementById('hubRejectPaymentRef').textContent = ref;
        document.getElementById('hub_rejection_reason').value = '';
        const modal = new bootstrap.Modal(document.getElementById('hubPaymentRejectModal'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const activeTabKey = 'approval_hub_active_tab';
        const savedTab = localStorage.getItem(activeTabKey);
        if (savedTab) {
            const triggerEl = document.querySelector(`button[data-bs-target="${savedTab}"]`);
            if (triggerEl && typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(event) {
                const target = event.target.getAttribute('data-bs-target');
                if (target) {
                    localStorage.setItem(activeTabKey, target);
                }
            });
        });
    });
</script>
@endpush
