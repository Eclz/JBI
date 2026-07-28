@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Fee Records</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.show', $student->id) }}">{{ $student->name }}</a></li>
                    <li class="breadcrumb-item active">Fees</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.fees.records.create') }}" class="btn btn-primary me-2">
                <i class="bi bi-plus-lg me-1"></i> Add Fee Record
            </a>
            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Student
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="{{ $student->profile_picture ?? '/images/default-avatar.png' }}"
                     alt="{{ $student->name }}"
                     class="rounded-circle me-3"
                     style="width: 60px; height: 60px; object-fit: cover;">
                <div>
                    <h5 class="mb-1">{{ $student->name }}</h5>
                    <p class="text-muted mb-0">
                        {{ $student->email }}
                        @if($student->studentProfile && $student->studentProfile->department)
                            | {{ $student->studentProfile->department->name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated fee statistics section to use correct array keys -->
    <!-- Fee Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Fees</h6>
                    <h3 class="mb-0">{{ $currencyCode }} {{ number_format($stats['total_fees'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Paid</h6>
                    <h3 class="mb-0 text-success">{{ $currencyCode }} {{ number_format($stats['paid_fees'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pending</h6>
                    <h3 class="mb-0 text-warning">{{ $currencyCode }} {{ number_format($stats['pending_fees'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Overdue</h6>
                    <h3 class="mb-0 text-danger">{{ $currencyCode }} {{ number_format($stats['overdue_fees'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Records Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Payment History</h5>
        </div>
        <div class="card-body">
            @if($feeRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feeRecords as $record)
                                <tr>
                                    <td>
                                        <strong>{{ $record->invoice_number }}</strong>
                                    </td>
                                    <td>{{ $record->feeStructure->fee_name ?? 'N/A' }}</td>
                                    <td>{{ $currencyCode }} {{ number_format($record->amount, 2) }}</td>
                                    <td>{{ $currencyCode }} {{ number_format($record->amount_paid, 2) }}</td>
                                    <td>{{ $currencyCode }} {{ number_format($record->amount - $record->amount_paid, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->due_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($record->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($record->status === 'partial')
                                            <span class="badge bg-info">Partial</span>
                                        @elseif($record->status === 'overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.fees.records.show', $record->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($record->status !== 'paid')
                                            <a href="{{ route('admin.fees.records.process-payment', $record->id) }}"
                                               class="btn btn-sm btn-outline-success"
                                               title="Process Payment">
                                                <i class="bi bi-cash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $feeRecords->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No fee records found</p>
                    <a href="{{ route('admin.fees.records.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Create Fee Record
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
