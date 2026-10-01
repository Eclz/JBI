@extends('layouts.app')

@section('title', 'Central Approval Hub')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-4">
        <h1 class="h3 mb-2" style="color: #1e293b; font-weight: 600;">Central Approval Hub</h1>
        <p class="text-muted">Review, approve, or reject submissions made by Deans across new faculty modules.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
            </ul>
        </div>
        <div class="card-body p-0 border-top">
            <div class="tab-content" id="approvalTabsContent">
                
                <!-- Academic Quality Tab -->
                <div class="tab-pane fade show active" id="quality" role="tabpanel" aria-labelledby="quality-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Program</th>
                                    <th>Review Title</th>
                                    <th>Submitted By</th>
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
                                        <td>{{ $review->updated_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qualityModal{{ $review->id }}">Review</button>
                                        </td>
                                    </tr>

                                    <!-- Quality Modal -->
                                    <div class="modal fade" id="qualityModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Review: {{ $review->title }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Notes:</h6>
                                                    <p style="white-space: pre-line;">{{ $review->review_notes }}</p>
                                                    
                                                    <h6>Metrics:</h6>
                                                    @if(is_array($review->metrics))
                                                        <ul class="list-group list-group-flush mb-4">
                                                            @foreach($review->metrics as $metric)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    {{ $metric['name'] }}
                                                                    <span class="badge bg-primary rounded-pill">{{ $metric['value'] }} / 5</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif

                                                    <form method="POST" id="qualityForm{{ $review->id }}" action="">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label">Feedback Notes (Required for Rejection)</label>
                                                            <textarea name="notes" class="form-control" rows="3" required></textarea>
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
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No pending academic quality reviews.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Faculty Evaluations Tab -->
                <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Faculty Member</th>
                                    <th>Evaluator (Dean)</th>
                                    <th>Year</th>
                                    <th>Score</th>
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
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#evalModal{{ $evaluation->id }}">Review</button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Eval Modal -->
                                    <div class="modal fade" id="evalModal{{ $evaluation->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Evaluate: {{ $evaluation->faculty->full_name ?? 'N/A' }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Dean's Comments:</h6>
                                                    <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $evaluation->comments }}</p>
                                                    
                                                    <form method="POST" id="evalForm{{ $evaluation->id }}" action="">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label">Admin Notes (Appended to comments)</label>
                                                            <textarea name="notes" class="form-control" rows="3" required></textarea>
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
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No pending faculty evaluations.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Budget Requests Tab -->
                <div class="tab-pane fade" id="budgets" role="tabpanel" aria-labelledby="budgets-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Department</th>
                                    <th>Title</th>
                                    <th>Amount</th>
                                    <th>Requester</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budgetRequests as $budget)
                                    <tr>
                                        <td>{{ $budget->department->name ?? 'N/A' }}</td>
                                        <td>{{ $budget->title }}</td>
                                        <td class="font-monospace text-primary fw-medium">₵{{ number_format($budget->amount, 2) }}</td>
                                        <td>{{ $budget->requester->full_name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetModal{{ $budget->id }}">Review</button>
                                        </td>
                                    </tr>

                                    <!-- Budget Modal -->
                                    <div class="modal fade" id="budgetModal{{ $budget->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Budget Request: {{ $budget->title }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <span class="text-muted d-block small">Expected Delivery</span>
                                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($budget->expected_delivery_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    <h6>Description & Justification:</h6>
                                                    <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $budget->description }}</p>
                                                    
                                                    <form method="POST" id="budgetForm{{ $budget->id }}" action="">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label">Finance/Admin Notes</label>
                                                            <textarea name="notes" class="form-control" rows="3" required></textarea>
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
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No pending budget requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Student Issues Tab -->
                <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Issue Type</th>
                                    <th>Reporter (Dean)</th>
                                    <th>Date Escalated</th>
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
                                        <td>{{ $issue->updated_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#issueModal{{ $issue->id }}">Review & Resolve</button>
                                        </td>
                                    </tr>

                                    <!-- Issue Modal -->
                                    <div class="modal fade" id="issueModal{{ $issue->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Escalated Issue: {{ $issue->student->full_name ?? 'N/A' }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Description:</h6>
                                                    <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $issue->description }}</p>
                                                    
                                                    <h6>Prior Actions Taken by Dean:</h6>
                                                    <p style="white-space: pre-line;" class="p-3 bg-light rounded border">{{ $issue->action_taken ?? 'None' }}</p>
                                                    
                                                    <form method="POST" id="issueForm{{ $issue->id }}" action="{{ route('admin.approval-hub.student-issue.resolve', $issue) }}">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label">Final Admin Resolution / Actions Taken</label>
                                                            <textarea name="notes" class="form-control" rows="3" required></textarea>
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
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No escalated student issues.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Partnerships Tab -->
                <div class="tab-pane fade" id="partnerships" role="tabpanel" aria-labelledby="partnerships-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Organization</th>
                                    <th>Type</th>
                                    <th>Expected Funding</th>
                                    <th>Manager (Dean)</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partnerships as $partnership)
                                    <tr>
                                        <td><strong>{{ $partnership->organization_name }}</strong></td>
                                        <td>{{ $partnership->partnership_type }}</td>
                                        <td>
                                            @if($partnership->funding_amount > 0)
                                                ₵{{ number_format($partnership->funding_amount, 2) }}
                                            @else
                                                <span class="text-muted fst-italic">Non-Financial</span>
                                            @endif
                                        </td>
                                        <td>{{ $partnership->manager->full_name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#partnershipModal{{ $partnership->id }}">Review</button>
                                        </td>
                                    </tr>

                                    <!-- Partnership Modal -->
                                    <div class="modal fade" id="partnershipModal{{ $partnership->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Proposed Partnership: {{ $partnership->organization_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
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
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No pending partnerships.</td></tr>
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
