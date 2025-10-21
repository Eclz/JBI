@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Financial Report</h1>
                    <p class="text-muted">Fee collection and payment analysis</p>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

     Financial Statistics Cards
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Fees</h6>
                    <h2 class="mb-0">₦{{ number_format($stats['total_fees'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Paid</h6>
                    <h2 class="mb-0">₦{{ number_format($stats['total_paid'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Outstanding</h6>
                    <h2 class="mb-0">₦{{ number_format($stats['total_outstanding'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Collection Rate</h6>
                    <h2 class="mb-0">{{ $stats['total_fees'] > 0 ? number_format(($stats['total_paid'] / $stats['total_fees']) * 100, 1) : 0 }}%</h2>
                </div>
            </div>
        </div>
    </div>

     Filters
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.financial') }}">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->year_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select">
                            <option value="">All Semesters</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->semester_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.reports.financial') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
         Payment Status Breakdown
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Payment Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentBreakdown as $breakdown)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{
                                                $breakdown->status === 'paid' ? 'success' :
                                                ($breakdown->status === 'partial' ? 'info' :
                                                ($breakdown->status === 'overdue' ? 'danger' : 'warning'))
                                            }}">
                                                {{ ucfirst($breakdown->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($breakdown->count) }}</td>
                                        <td class="text-end">₦{{ number_format($breakdown->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

         Monthly Collection Trend
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Collection (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Collection</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyCollection as $collection)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($collection->month . '-01')->format('M Y') }}</td>
                                        <td class="text-end">₦{{ number_format($collection->total, 2) }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                     style="width: {{ $monthlyCollection->max('total') > 0 ? ($collection->total / $monthlyCollection->max('total')) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

     Fee Records Details
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Fee Records</h5>
            <a href="{{ route('admin.reports.financial', array_merge(request()->all(), ['format' => 'pdf'])) }}"
               class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Student</th>
                            <th>Fee Type</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeRecords as $record)
                            <tr>
                                <td>{{ $record->invoice_number }}</td>
                                <td>
                                    {{ $record->student->user->name }}<br>
                                    <small class="text-muted">{{ $record->student->admission_number }}</small>
                                </td>
                                <td>{{ $record->feeStructure->fee_type }}</td>
                                <td class="text-end">₦{{ number_format($record->total_amount, 2) }}</td>
                                <td class="text-end">₦{{ number_format($record->amount_paid, 2) }}</td>
                                <td class="text-end">₦{{ number_format($record->balance, 2) }}</td>
                                <td>{{ $record->due_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{
                                        $record->status === 'paid' ? 'success' :
                                        ($record->status === 'partial' ? 'info' :
                                        ($record->status === 'overdue' ? 'danger' : 'warning'))
                                    }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No fee records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3">Totals on this page:</th>
                            <th class="text-end">₦{{ number_format($feeRecords->sum('total_amount'), 2) }}</th>
                            <th class="text-end">₦{{ number_format($feeRecords->sum('amount_paid'), 2) }}</th>
                            <th class="text-end">₦{{ number_format($feeRecords->sum('balance'), 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3">
                {{ $feeRecords->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
