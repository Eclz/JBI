@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Financial Report</h2>
            <p class="text-muted mb-0">Fee collection and payment analysis</p>
        </div>
        <a href="{{ route('admin.reports.financial.export', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Billed</div>
                    <div class="h4 mb-0">{{ $currencyCode }} {{ number_format($revenue['total_billed'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Collected</div>
                    <div class="h4 mb-0 text-success">{{ $currencyCode }} {{ number_format($revenue['total_collected'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Outstanding</div>
                    <div class="h4 mb-0 text-danger">{{ $currencyCode }} {{ number_format($revenue['total_pending'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Collection Rate</div>
                    <div class="h4 mb-0">{{ number_format($revenue['collection_rate'] ?? 0, 1) }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.financial') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All</option>
                        @foreach(($academicYears ?? []) as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name ?? $year->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Semester</label>
                    <select name="semester_id" class="form-select">
                        <option value="">All</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>{{ $semester->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Payment Status Breakdown</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentBreakdown as $row)
                                    <tr>
                                        <td>{{ ucfirst($row->status) }}</td>
                                        <td class="text-end">{{ number_format($row->count) }}</td>
                                        <td class="text-end">{{ $currencyCode }} {{ number_format($row->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white"><strong>Recent Collection Trend</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Collected</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collectionTrends as $trend)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                        <td class="text-end">{{ $currencyCode }} {{ number_format($trend->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted">No collections in this date range</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Fee Records</strong>
            <span class="badge bg-secondary">{{ $feeRecords->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Student</th>
                            <th>Fee Type</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th>Status</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeRecords as $record)
                            <tr>
                                <td>{{ $record->invoice_number }}</td>
                                <td>{{ $record->student?->full_name ?: ($record->student?->name ?? 'N/A') }}</td>
                                <td>{{ $record->feeStructure?->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ $currencyCode }} {{ number_format($record->total_amount, 2) }}</td>
                                <td class="text-end">{{ $currencyCode }} {{ number_format($record->paid_amount, 2) }}</td>
                                <td class="text-end">{{ $currencyCode }} {{ number_format($record->balance_amount, 2) }}</td>
                                <td><span class="badge bg-{{ $record->status === 'paid' ? 'success' : ($record->status === 'partial' ? 'warning text-dark' : ($record->status === 'overdue' ? 'danger' : 'secondary')) }}">{{ ucfirst($record->status) }}</span></td>
                                <td>{{ $record->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No fee records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $feeRecords->links() }}
        </div>
    </div>
</div>
@endsection
