@extends('layouts.app')

@section('title', 'Research Grants & Donor Funds')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-journal-medical me-2"></i>Research Grants & Donor Funding
            </h1>
            <p class="text-muted mb-0">Track research grant allocations, donor organizations, principal investigators & project disbursements</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addGrantModal">
                <i class="bi bi-plus-lg me-1"></i>Register Research Grant
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
                            <th class="ps-3">Grant Code</th>
                            <th>Project Title</th>
                            <th>Donor Organization</th>
                            <th>Principal Investigator</th>
                            <th>Grant Duration</th>
                            <th class="text-end pe-3">Total Grant Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grants as $gnt)
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-primary">{{ $gnt->grant_code }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $gnt->project_title }}</div>
                                </td>
                                <td><span class="badge bg-info text-dark px-2.5 py-1.5 fw-bold">{{ $gnt->donor_organization }}</span></td>
                                <td>{{ $gnt->principalInvestigator->full_name ?? 'Principal Investigator' }}</td>
                                <td class="small">{{ $gnt->start_date->format('M Y') }} - {{ $gnt->end_date->format('M Y') }}</td>
                                <td class="text-end pe-3 fw-bold text-success fs-6">{{ $currencyCode }} {{ number_format($gnt->total_grant_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No research grants registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($grants->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $grants->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addGrantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.finance.grants.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Register Research Grant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Project Title <span class="text-danger">*</span></label>
                            <input type="text" name="project_title" class="form-control" placeholder="e.g. AI for Agricultural Yield" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Donor Organization <span class="text-danger">*</span></label>
                            <input type="text" name="donor_organization" class="form-control" placeholder="e.g. USAID / World Bank" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Principal Investigator</label>
                            <select name="principal_investigator_id" class="form-select">
                                <option value="">-- Select Investigator --</option>
                                @foreach($professors as $prof)
                                    <option value="{{ $prof->id }}">{{ $prof->full_name }} ({{ $prof->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Grant Amount (UGX) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_grant_amount" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+2 years')) }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Register Grant</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
