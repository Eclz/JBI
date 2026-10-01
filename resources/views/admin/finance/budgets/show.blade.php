@extends('layouts.app')

@section('title', 'Budget Allocation Details')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-pie-chart-fill me-2"></i>Budget Report: {{ $budget->budget_code ?? 'BGT-'.str_pad($budget->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-muted mb-0">Detailed view of departmental budget allocation and utilization</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary fw-bold me-2">
                <i class="bi bi-printer me-1"></i>Print Report
            </button>
            <a href="{{ route('admin.finance.budgets.index') }}" class="btn btn-secondary fw-bold">
                <i class="bi bi-arrow-left me-1"></i>Back to Budgets
            </a>
        </div>
    </div>

    @php
        $availableBalance = $budget->allocated_amount - ($budget->spent_amount + $budget->committed_amount);
        
        $spentPercentage = $budget->allocated_amount > 0 ? min(100, round(($budget->spent_amount / $budget->allocated_amount) * 100)) : 0;
        $committedPercentage = $budget->allocated_amount > 0 ? min(100, round(($budget->committed_amount / $budget->allocated_amount) * 100)) : 0;
        $totalUsedPercentage = min(100, $spentPercentage + $committedPercentage);
        
        $progressBarColor = 'bg-success';
        if ($totalUsedPercentage > 90) $progressBarColor = 'bg-danger';
        elseif ($totalUsedPercentage > 75) $progressBarColor = 'bg-warning';
    @endphp

    <div class="row g-4">
        <!-- Main Document Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white" id="printableBudget">
                <!-- Document Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">BUDGET ALLOCATION</h2>
                        <h5 class="text-muted font-monospace mb-3">{{ $budget->budget_code ?? 'BGT-'.str_pad($budget->id, 5, '0', STR_PAD_LEFT) }}</h5>
                        
                        <div class="mb-1 text-muted small text-uppercase fw-bold">Department / Cost Center:</div>
                        <h4 class="fw-bold text-primary mb-1">{{ $budget->department->name ?? 'General University Fund' }}</h4>
                        <div class="text-muted">Academic Year: <span class="fw-bold text-dark">{{ $budget->academic_year }}</span></div>
                    </div>
                    <div class="text-end">
                        <div class="mb-3">
                            @if(strtolower($budget->status) === 'active' || strtolower($budget->status) === 'approved')
                                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="bi bi-check-circle me-1"></i>ACTIVE</span>
                            @elseif(strtolower($budget->status) === 'exhausted')
                                <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill"><i class="bi bi-exclamation-octagon me-1"></i>EXHAUSTED</span>
                            @elseif(strtolower($budget->status) === 'closed')
                                <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill"><i class="bi bi-lock me-1"></i>CLOSED</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i class="bi bi-hourglass me-1"></i>{{ strtoupper($budget->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Utilization Progress -->
                <div class="mb-5">
                    <h5 class="fw-bold text-dark mb-3">Budget Utilization Status</h5>
                    
                    <div class="progress mb-2" style="height: 25px; border-radius: 12px; border: 1px solid #dee2e6;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $spentPercentage }}%;" title="Spent: {{ $spentPercentage }}%"></div>
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated text-dark" role="progressbar" style="width: {{ $committedPercentage }}%;" title="Committed: {{ $committedPercentage }}%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between small text-muted">
                        <div>0%</div>
                        <div class="d-flex gap-3">
                            <span><i class="bi bi-square-fill text-primary me-1"></i>Spent</span>
                            <span><i class="bi bi-square-fill text-warning me-1"></i>Committed</span>
                            <span><i class="bi bi-square bg-light border me-1"></i>Available</span>
                        </div>
                        <div>100%</div>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase small fw-bold text-muted py-3">Category / Description</th>
                                <th class="text-uppercase small fw-bold text-muted py-3 text-end" style="width: 200px;">Amount ({{ $currencyCode }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Allocated -->
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark fs-5">Total Approved Allocation</div>
                                    <div class="small text-muted">Initial approved budget for the academic year.</div>
                                </td>
                                <td class="text-end fw-bold py-3 fs-5 text-primary">{{ number_format($budget->allocated_amount, 2) }}</td>
                            </tr>
                            
                            <!-- Spent -->
                            <tr>
                                <td class="py-3 ps-4 border-start border-4 border-primary">
                                    <div class="fw-semibold text-dark">Less: Actual Spent Amounts</div>
                                    <div class="small text-muted">Expenses that have been fully processed and paid.</div>
                                </td>
                                <td class="text-end fw-semibold py-3 text-danger">- {{ number_format($budget->spent_amount, 2) }}</td>
                            </tr>
                            
                            <!-- Committed -->
                            @if($budget->committed_amount > 0)
                            <tr>
                                <td class="py-3 ps-4 border-start border-4 border-warning">
                                    <div class="fw-semibold text-dark">Less: Committed / Pending Amounts</div>
                                    <div class="small text-muted">Requisitions approved but not yet disbursed.</div>
                                </td>
                                <td class="text-end fw-semibold py-3 text-warning">- {{ number_format($budget->committed_amount, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot class="border-top border-2 border-dark bg-light">
                            <tr>
                                <td class="text-end fw-bold text-uppercase py-3">Available Balance to Spend:</td>
                                <td class="text-end fw-bold {{ $availableBalance > 0 ? 'text-success' : 'text-danger' }} fs-4 py-3">{{ $currencyCode }} {{ number_format($availableBalance, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Approval Signatures -->
                <div class="row mt-5 pt-4 text-center border-top">
                    <div class="col-6">
                        <div class="border-bottom border-dark border-1 mx-4 mb-2" style="height: 30px;">
                            <span class="text-success" style="font-family: 'Brush Script MT', cursive; font-size: 1.5rem;">Approved</span>
                        </div>
                        <div class="small fw-bold text-uppercase text-muted">Authorized By</div>
                        <div class="small">{{ $budget->approver->name ?? 'Finance Director / Vice Chancellor' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="border-bottom border-dark border-1 mx-4 mb-2" style="height: 30px;"></div>
                        <div class="small fw-bold text-uppercase text-muted">Department Head</div>
                        <div class="small">{{ $budget->department->head_name ?? 'Head of Department' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small fw-semibold text-uppercase">Utilization Rate</span>
                            <span class="fw-bold {{ $totalUsedPercentage > 90 ? 'text-danger' : 'text-primary' }}">{{ $totalUsedPercentage }}%</span>
                        </div>
                    </div>
                    
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center">
                            <div class="fs-1 text-primary me-3"><i class="bi bi-wallet2"></i></div>
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">Total Funds Handled</div>
                                <div class="fw-bold fs-5">{{ $currencyCode }} {{ number_format($budget->allocated_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 border-top border-primary border-4">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle me-1"></i>System Information</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-1"><strong>Created On:</strong> {{ $budget->created_at->format('M d, Y H:i') }}</li>
                        <li class="mb-1"><strong>Last Updated:</strong> {{ $budget->updated_at->format('M d, Y H:i') }}</li>
                    </ul>
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
        #printableBudget, #printableBudget * {
            visibility: visible;
        }
        #printableBudget {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            border: none !important;
        }
        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }
    }
</style>
@endsection
