@extends('layouts.app')

@section('title', 'Fee Structure Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $feeStructure->name }}</h1>
                    <p class="text-muted">Fee structure details and usage</p>
                </div>
                <div>
                    <a href="{{ route('admin.fees.structures.edit', $feeStructure) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit Structure
                    </a>
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Structures
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Structure Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $feeStructure->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($feeStructure->type === 'retake')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-arrow-repeat me-1"></i>Retake Fee</span>
                                        @elseif($feeStructure->type === 'missed_paper')
                                            <span class="badge bg-danger text-white"><i class="bi bi-file-earmark-x me-1"></i>Missed Paper Fee</span>
                                        @else
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $feeStructure->type)) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td><strong class="text-success">{{ $feeStructure->currency ?? $currencyCode }} {{ number_format($feeStructure->amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Programme:</strong></td>
                                    <td>{{ $feeStructure->program->name ?? 'All programmes' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Level:</strong></td>
                                    <td>{{ $feeStructure->programLevel->name ?? 'General' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Student region:</strong></td>
                                    <td>{{ ucfirst($feeStructure->student_region ?? 'All regions') }}</td>
                                </tr>
                                @if($feeStructure->total_amount)
                                    <tr>
                                        <td><strong>Programme total:</strong></td>
                                        <td>{{ $feeStructure->currency ?? $currencyCode }} {{ number_format($feeStructure->total_amount, 2) }}@if($feeStructure->total_amount_max && $feeStructure->total_amount_max != $feeStructure->total_amount) – {{ number_format($feeStructure->total_amount_max, 2) }}@endif</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Frequency:</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $feeStructure->frequency)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>{{ $feeStructure->academicYear->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Semester:</strong></td>
                                    <td>{{ $feeStructure->semester->name ?? 'All Semesters' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Due Date:</strong></td>
                                    <td>
                                        @if($feeStructure->due_date)
                                            {{ $feeStructure->due_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">No specific due date</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Late Fee:</strong></td>
                                    <td>
                                        @if($feeStructure->late_fee_amount > 0)
                                            {{ $feeStructure->currency ?? $currencyCode }} {{ number_format($feeStructure->late_fee_amount, 2) }}
                                            @if($feeStructure->late_fee_days)
                                                <br><small class="text-muted">After {{ $feeStructure->late_fee_days }} days</small>
                                            @endif
                                        @else
                                            <span class="text-muted">No late fee</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($feeStructure->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                        @if($feeStructure->is_mandatory)
                                            <span class="badge bg-warning">Mandatory</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $feeStructure->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>

                            @if($feeStructure->description)
                                <div class="mt-3">
                                    <strong>Description:</strong>
                                    <p class="text-muted mt-1">{{ $feeStructure->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Fee Records Using This Structure</h5>
                            <div>
                                <form action="{{ route('admin.fees.records.generate-invoices') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="fee_structure_id" value="{{ $feeStructure->id }}">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Generate invoices for all active students using this fee structure?')">
                                        <i class="fas fa-file-invoice me-1"></i>Generate Invoices
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($feeRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Invoice #</th>
                                                <th>Amount</th>
                                                <th>Paid</th>
                                                <th>Balance</th>
                                                <th>Status</th>
                                                <th>Due Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($feeRecords as $record)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $record->student->first_name }} {{ $record->student->last_name }}</strong>
                                                            <br><small class="text-muted">{{ $record->student->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td>{{ $record->invoice_number ?? 'N/A' }}</td>
                                                    <td>{{ $feeStructure->currency ?? $currencyCode }} {{ number_format($record->total_amount, 2) }}</td>
                                                    <td>{{ $feeStructure->currency ?? $currencyCode }} {{ number_format($record->paid_amount, 2) }}</td>
                                                    <td>{{ $feeStructure->currency ?? $currencyCode }} {{ number_format($record->balance_amount, 2) }}</td>
                                                    <td>
                                                        @switch($record->status)
                                                            @case('paid')
                                                                <span class="badge bg-success">Paid</span>
                                                                @break
                                                            @case('partial')
                                                                <span class="badge bg-warning">Partial</span>
                                                                @break
                                                            @case('pending')
                                                                @if($record->due_date && $record->due_date->isPast())
                                                                    <span class="badge bg-danger">Overdue</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Pending</span>
                                                                @endif
                                                                @break
                                                            @case('overdue')
                                                                <span class="badge bg-danger">Overdue</span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        @if($record->due_date)
                                                            {{ $record->due_date->format('M d, Y') }}
                                                            @if($record->due_date->isPast() && $record->status !== 'paid')
                                                                <br><small class="text-danger">{{ $record->due_date->diffForHumans() }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No due date</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.fees.records.show', $record) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center">
                                    {{ $feeRecords->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-invoice fa-2x text-muted mb-3"></i>
                                    <h6>No Fee Records Found</h6>
                                    <p class="text-muted">No invoices have been generated using this fee structure yet.</p>
                                    <form action="{{ route('admin.fees.records.generate-invoices') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="fee_structure_id" value="{{ $feeStructure->id }}">
                                        <button type="submit" class="btn btn-primary"
                                                onclick="return confirm('Generate invoices for all active students using this fee structure?')">
                                            <i class="fas fa-file-invoice me-2"></i>Generate Invoices
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
