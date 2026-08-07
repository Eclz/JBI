@extends('layouts.app')

@section('title', 'Accounts Receivable & Debt Aging')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">
                <i class="bi bi-person-lines-fill me-2"></i>Accounts Receivable & Student Debtors
            </h1>
            <p class="text-muted mb-0">Track outstanding tuition balances, student debt aging schedules & fee reminders</p>
        </div>
        <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-outline-secondary">Back to Finance Hub</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white border-start border-4 border-danger">
                <div class="card-body p-3">
                    <span class="text-muted text-uppercase fw-bold small">Total Outstanding Receivables</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $currencyCode }} {{ number_format($totalReceivable, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Student Name</th>
                            <th>Email Address</th>
                            <th>Programme</th>
                            <th>Total Billed</th>
                            <th>Total Paid</th>
                            <th class="text-end pe-3">Outstanding Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debtors as $record)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $record->student->full_name ?? 'Student Account' }}</td>
                                <td class="small">{{ $record->student->email ?? '-' }}</td>
                                <td class="small text-muted">{{ $record->student->studentProfile->program ?? 'General Degree' }}</td>
                                <td class="fw-semibold">{{ $currencyCode }} {{ number_format($record->total_amount, 2) }}</td>
                                <td class="fw-semibold text-success">{{ $currencyCode }} {{ number_format($record->paid_amount, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-danger">{{ $currencyCode }} {{ number_format($record->balance_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No student debtors found. All fees settled.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($debtors->hasPages())
            <div class="card-footer bg-white border-top p-3">{{ $debtors->links() }}</div>
        @endif
    </div>
</div>
@endsection
