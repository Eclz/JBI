@extends('layouts.app')

@section('title', 'Fee Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Fee Management</h4>
                    <div>
                        @if(auth()->user()->hasPermission('fees', 'create'))
                        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#generateInvoicesModal">
                            <i class="fas fa-file-invoice"></i> Generate Invoices
                        </button>
                        @endif
                        @if(auth()->user()->hasPermission('fees', 'edit'))
                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#sendRemindersModal">
                            <i class="fas fa-bell"></i> Send Reminders
                        </button>
                        @endif
                        @if(auth()->user()->hasPermission('fees', 'create'))
                        <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-success me-2">
                            <i class="fas fa-plus"></i> Add Fee Structure
                        </a>
                        <a href="{{ route('admin.fees.records.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Fee Record
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Collected</h6>
                                            <h4>{{ $currencyCode }} {{ number_format($totalCollected, 2) }}</h4>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pending</h6>
                                            <h4>{{ $currencyCode }} {{ number_format($totalPending, 2) }}</h4>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Overdue</h6>
                                            <h4>{{ $currencyCode }} {{ number_format($totalOverdue, 2) }}</h4>
                                        </div>
                                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">This Month</h6>
                                            <h4>{{ $currencyCode }} {{ number_format($thisMonthCollection, 2) }}</h4>
                                        </div>
                                        <i class="fas fa-calendar fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="feesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !(request('tx_page') || request('transaction_search') || request('transaction_method')) ? 'active' : '' }}" id="records-tab" data-bs-toggle="tab"
                                    data-bs-target="#records" type="button" role="tab">
                                <i class="fas fa-file-invoice-dollar me-1"></i> Fee Records
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ (request('tx_page') || request('transaction_search') || request('transaction_method')) ? 'active' : '' }}" id="transactions-tab" data-bs-toggle="tab"
                                    data-bs-target="#transactions" type="button" role="tab">
                                <i class="fas fa-receipt me-1 text-success"></i> Transactions & Receipts
                            </button>
                        </li>
                        @if(optional(auth()->user()->roleCatalog)->code !== 'admissions_officer')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="structures-tab" data-bs-toggle="tab"
                                    data-bs-target="#structures" type="button" role="tab">
                                <i class="fas fa-layer-group me-1"></i> Fee Structures
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reports-tab" data-bs-toggle="tab"
                                    data-bs-target="#reports" type="button" role="tab">
                                <i class="fas fa-chart-line me-1"></i> Reports
                            </button>
                        </li>
                        @endif
                    </ul>

                    <div class="tab-content" id="feesTabContent">
                        <!-- Fee Records Tab -->
                        <div class="tab-pane fade {{ !(request('tx_page') || request('transaction_search') || request('transaction_method')) ? 'show active' : '' }}" id="records" role="tabpanel">
                            <div class="mt-3">
                                <!-- Search and Filter -->
                                <form method="GET" action="{{ route('admin.fees.index') }}" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="search"
                                                   value="{{ request('search') }}" placeholder="Search student...">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" name="status">
                                                <option value="">All Status</option>
                                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" name="semester_id">
                                                <option value="">All Semesters</option>
                                                @foreach($semesters as $semester)
                                                    <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                                        {{ $semester->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" class="form-control" name="due_date"
                                                   value="{{ request('due_date') }}" placeholder="Due before">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                                            <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                         @if(auth()->user()->hasPermission('fees', 'export'))
                                         <div class="col-md-1">
                                             <button type="button" class="btn btn-outline-success" onclick="exportFeeRecords()">
                                                 <i class="fas fa-download"></i>
                                             </button>
                                         </div>
                                         @endif
                                    </div>
                                </form>

                                <!-- Fee Records Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Student</th>
                                                <th>Fee Type</th>
                                                <th>Invoice #</th>
                                                <th>Amount</th>
                                                <th>Paid</th>
                                                <th>Balance</th>
                                                <th>Due Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($feeRecords as $record)
                                            <tr class="{{ $record->is_overdue ? 'table-warning' : '' }}">
                                                 <td>
                                                     <div class="d-flex align-items-center">
                                                         @if($record->student)
                                                             @if($record->student->profile_picture)
                                                                 <img src="{{ asset('storage/' . $record->student->profile_picture) }}"
                                                                      class="rounded-circle me-2" width="32" height="32">
                                                             @else
                                                                 <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                      style="width: 32px; height: 32px; min-width: 32px;">
                                                                     <span class="text-white small fw-bold">{{ substr($record->student->first_name ?? 'S', 0, 1) }}</span>
                                                                 </div>
                                                             @endif
                                                             <div>
                                                                 <div class="fw-bold text-dark">{{ $record->student->first_name }} {{ $record->student->last_name }}</div>
                                                                 <small class="text-muted">{{ $record->student->email }}</small>
                                                             </div>
                                                         @else
                                                             <span class="badge bg-secondary">Unassigned</span>
                                                         @endif
                                                     </div>
                                                 </td>
                                                <td>{{ $record->feeStructure->name }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $record->invoice_number }}</span>
                                                </td>
                                                <td>{{ $currencyCode }} {{ number_format($record->amount, 2) }}</td>
                                                <td>{{ $currencyCode }} {{ number_format($record->paid_amount, 2) }}</td>
                                                <td>{{ $currencyCode }} {{ number_format($record->balance_amount, 2) }}</td>
                                                <td>
                                                    {{ $record->due_date->format('M d, Y') }}
                                                    @if($record->is_overdue)
                                                        <br><small class="text-danger">{{ $record->due_date->diffForHumans() }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{
                                                        $record->status == 'paid' ? 'success' :
                                                        ($record->is_overdue ? 'danger' :
                                                        ($record->status == 'partial' ? 'warning' : 'secondary'))
                                                    }}">
                                                        {{ $record->is_overdue && $record->status != 'paid' ? 'Overdue' : ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                     <div class="btn-group" role="group">
                                                         <a href="{{ route('admin.fees.records.show', $record) }}"
                                                            class="btn btn-sm btn-outline-info" title="View">
                                                             <i class="fas fa-eye"></i>
                                                         </a>
                                                         @if($record->status != 'paid' && auth()->user()->hasPermission('fees', 'create'))
                                                         <a href="{{ route('admin.fees.records.payment', $record) }}"
                                                            class="btn btn-sm btn-outline-success" title="Record Payment">
                                                             <i class="fas fa-dollar-sign"></i>
                                                         </a>
                                                         @endif
                                                         @if($record->status != 'paid' && auth()->user()->hasPermission('fees', 'edit'))
                                                         <button type="button" class="btn btn-sm btn-outline-warning" title="Send Payment Reminder"
                                                                 onclick="openSingleReminderModal({{ $record->id }}, {{ $record->user_id }}, '{{ addslashes($record->student->name ?? 'Student') }}', '{{ $currencyCode }} {{ number_format($record->balance_amount, 2) }}', '{{ addslashes($record->feeStructure->name ?? 'Fee') }}')">
                                                             <i class="fas fa-bell"></i>
                                                         </button>
                                                         @endif
                                                         @if(auth()->user()->hasPermission('fees', 'edit'))
                                                         <a href="{{ route('admin.fees.records.edit', $record) }}"
                                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                                             <i class="fas fa-edit"></i>
                                                         </a>
                                                         @endif
                                                         @if($record->paid_amount == 0 && auth()->user()->hasPermission('fees', 'delete'))
                                                         <button type="button" class="btn btn-sm btn-outline-danger"
                                                                 onclick="confirmDelete({{ $record->id }})" title="Delete">
                                                             <i class="fas fa-trash"></i>
                                                         </button>
                                                         @endif
                                                     </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No fee records found</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        Showing {{ $feeRecords->firstItem() ?? 0 }} to {{ $feeRecords->lastItem() ?? 0 }}
                                        of {{ $feeRecords->total() }} results
                                    </div>
                                    {{ $feeRecords->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>

                        <!-- Transactions & Receipts Tab -->
                        <div class="tab-pane fade {{ (request('tx_page') || request('transaction_search') || request('transaction_method')) ? 'show active' : '' }}" id="transactions" role="tabpanel">
                            <div class="mt-3">
                                <!-- Transactions Search & Filter -->
                                <form method="GET" action="{{ route('admin.fees.index') }}" class="mb-3">
                                    <input type="hidden" name="tx_page" value="1">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                <input type="text" class="form-control" name="transaction_search"
                                                       value="{{ request('transaction_search') }}"
                                                       placeholder="Search reference #, transaction ID, or student name/email...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" name="transaction_method">
                                                <option value="">All Payment Methods</option>
                                                <option value="Bank Transfer" {{ request('transaction_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="Cash" {{ request('transaction_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="Online" {{ request('transaction_method') == 'Online' ? 'selected' : '' }}>Online</option>
                                                <option value="Cheque" {{ request('transaction_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-filter me-1"></i> Filter Transactions
                                            </button>
                                            <a href="{{ route('admin.fees.index') }}#transactions" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-1"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>

                                <!-- Transactions Summary Banner -->
                                <div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 bg-success text-white rounded">
                                            <i class="fas fa-coins fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Total Recorded Transactions</div>
                                            <div class="fw-bold fs-5 text-dark">{{ $currencyCode }} {{ number_format($totalTransactionsAmount, 2) }}</div>
                                        </div>
                                        <div class="border-start ps-3 ms-2">
                                            <div class="small text-muted">Total Records</div>
                                            <div class="fw-bold fs-5 text-primary">{{ $transactions->total() }}</div>
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1 text-info"></i> Includes standard fee payments and verified admission payments.
                                    </div>
                                </div>

                                <!-- Transactions Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Date</th>
                                                <th>Student</th>
                                                <th>Purpose / Description</th>
                                                <th>Reference / Tx ID</th>
                                                <th>Method</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions as $tx)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold">
                                                        {{ $tx->payment_date ? \Carbon\Carbon::parse($tx->payment_date)->format('M d, Y') : $tx->created_at->format('M d, Y') }}
                                                    </span>
                                                    <br><small class="text-muted">{{ $tx->created_at->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if($tx->student)
                                                        <div>
                                                            <strong>{{ $tx->student->name }}</strong>
                                                            <br><small class="text-muted">{{ $tx->student->email }}</small>
                                                            @if($tx->student->studentProfile?->admission_number)
                                                                <br><span class="badge bg-secondary font-monospace">{{ $tx->student->studentProfile->admission_number }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted fst-italic">Applicant / User Deleted</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tx->feeRecord && $tx->feeRecord->feeStructure)
                                                        <strong>{{ $tx->feeRecord->feeStructure->name }}</strong>
                                                        @if($tx->notes)
                                                            <br><small class="text-muted">{{ Str::limit($tx->notes, 40) }}</small>
                                                        @endif
                                                    @elseif($tx->notes)
                                                        <span class="text-dark">{{ Str::limit($tx->notes, 50) }}</span>
                                                    @else
                                                        <span class="badge bg-info text-dark">Admission & Application Fee</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border font-monospace">
                                                        {{ $tx->reference_number ?: ($tx->transaction_id ?: 'N/A') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                                        {{ $tx->payment_method ?: 'Bank Transfer' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success fs-6">
                                                        {{ $currencyCode }} {{ number_format($tx->amount, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i> {{ ucfirst($tx->status ?: 'completed') }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.fees.payments.receipt', $tx->id) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Print / View Official Receipt">
                                                            <i class="fas fa-receipt me-1"></i> Receipt
                                                        </a>
                                                        @if($tx->payment_proof)
                                                        <a href="{{ asset('storage/' . $tx->payment_proof) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-secondary"
                                                           title="View Attached Payment Slip / Proof">
                                                            <i class="fas fa-paperclip"></i>
                                                        </a>
                                                        @endif
                                                        @if($tx->fee_record_id)
                                                        <a href="{{ route('admin.fees.records.show', $tx->fee_record_id) }}"
                                                           class="btn btn-sm btn-outline-info"
                                                           title="View Invoice">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No finance transactions found</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Transactions Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }}
                                        of {{ $transactions->total() }} transactions
                                    </div>
                                    {{ $transactions->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>

                        <!-- Fee Structures Tab -->
                        <div class="tab-pane fade" id="structures" role="tabpanel">
                            <div class="mt-3">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Frequency</th>
                                                <th>Academic Year</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($feeStructures as $structure)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $structure->name }}</strong>
                                                        @if($structure->description)
                                                            <br><small class="text-muted">{{ Str::limit($structure->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($structure->type) }}</span>
                                                </td>
                                                <td>{{ $currencyCode }} {{ number_format($structure->amount, 2) }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $structure->frequency)) }}</td>
                                                <td>{{ $structure->academicYear->name ?? 'All Years' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $structure->is_active ? 'success' : 'secondary' }}">
                                                        {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                    @if($structure->is_mandatory)
                                                        <span class="badge bg-warning">Mandatory</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.fees.structures.show', $structure) }}"
                                                           class="btn btn-sm btn-outline-info" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.fees.structures.edit', $structure) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="confirmDeleteStructure({{ $structure->id }})" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No fee structures found</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Reports Tab -->
                        <div class="tab-pane fade" id="reports" role="tabpanel">
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Collection Trends (Last 6 Months)</h5>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="collectionChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Payment Status Distribution</h5>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="statusChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Collections by Semester</h5>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="semesterChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Cash Payment Approvals</h5>
                                            </div>
                                            <div class="card-body">
                                                <canvas id="cashStatusChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Semester Student Payment Breakdown</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped align-middle">
                                                        <thead>
                                                            <tr>
                                                                <th>Semester</th>
                                                                <th>Student</th>
                                                                <th class="text-end">Invoices</th>
                                                                <th class="text-end">Total</th>
                                                                <th class="text-end">Paid</th>
                                                                <th class="text-end">Balance</th>
                                                                <th class="text-end">Rate</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($semesterStudentBreakdown as $row)
                                                                @php
                                                                    $rate = (float) $row->total_amount > 0 ? ((float) $row->paid_amount / (float) $row->total_amount) * 100 : 0;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $row->semester_name }}</td>
                                                                    <td>{{ $row->student_name }}</td>
                                                                    <td class="text-end">{{ number_format($row->invoice_count) }}</td>
                                                                    <td class="text-end">{{ $currencyCode }} {{ number_format($row->total_amount, 2) }}</td>
                                                                    <td class="text-end">{{ $currencyCode }} {{ number_format($row->paid_amount, 2) }}</td>
                                                                    <td class="text-end">{{ $currencyCode }} {{ number_format($row->balance_amount, 2) }}</td>
                                                                    <td class="text-end">{{ number_format($rate, 1) }}%</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center text-muted">No semester payment breakdown data available.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Invoices Modal -->
<div class="modal fade" id="generateInvoicesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.fees.records.generate-invoices') }}" id="invoiceGenerationForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Invoices</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fee_structure_id" class="form-label">Fee Structure</label>
                        <select class="form-control" name="fee_structure_id" id="fee_structure_id" required>
                            <option value="">Select Fee Structure</option>
                            @foreach($feeStructures as $structure)
                                <option value="{{ $structure->id }}"
                                        data-amount="{{ (float) $structure->amount }}"
                                        data-due-date="{{ optional($structure->due_date)->format('Y-m-d') }}">
                                    {{ $structure->name }} - {{ $currencyCode }} {{ number_format($structure->amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" id="feeStructureMeta">Select a fee structure to see amount and default due date.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Generate for</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="generation_type" id="all_students" value="all" checked>
                            <label class="form-check-label" for="all_students">All Active Students</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="generation_type" id="selected_students" value="selected">
                            <label class="form-check-label" for="selected_students">Selected Students</label>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="invoice_semester_id" class="form-label">Filter by Semester (Optional)</label>
                            <select class="form-control" name="semester_id" id="invoice_semester_id">
                                <option value="">All Semesters</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="invoice_course_id" class="form-label">Filter by Course (Optional)</label>
                            <select class="form-control" name="course_id" id="invoice_course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-semester-id="{{ $course->semester_id }}">
                                        {{ $course->code ?? $course->course_code }} - {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="invoice_due_date" class="form-label">Due Date Override (Optional)</label>
                        <input type="date" class="form-control" name="due_date" id="invoice_due_date">
                        <small class="text-muted">Leave blank to use fee structure due date (or default 30 days).</small>
                    </div>
                    <div class="mb-3 d-none" id="studentSelectWrapper">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="student_ids" class="form-label mb-0">Select Students</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectVisibleStudents">Select Visible</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelectedStudents">Clear</button>
                            </div>
                        </div>
                        <input type="text" class="form-control mb-2" id="studentSearchInput" placeholder="Search by name, admission number or email...">
                        <select class="form-control" name="student_ids[]" id="student_ids" multiple size="8">
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                        data-search="{{ strtolower(($student->full_name ?: ($student->first_name . ' ' . $student->last_name)) . ' ' . ($student->studentProfile->admission_number ?? '') . ' ' . $student->email) }}">
                                    {{ $student->full_name ?: ($student->first_name . ' ' . $student->last_name) }}
                                    ({{ $student->studentProfile->admission_number ?? $student->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple students.</small>
                    </div>
                    <div class="border rounded p-3 bg-light">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="small text-muted">Active Students</div>
                                <div class="fw-bold" id="summaryTotalStudents">{{ $students->count() }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Selected Students</div>
                                <div class="fw-bold" id="summarySelectedStudents">0</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Estimated Total</div>
                                <div class="fw-bold" id="summaryEstimatedTotal">{{ $currencyCode }} 0.00</div>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">Semester and course filters are applied to enrolled students before invoice generation.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="generateInvoicesSubmitBtn">Generate Invoices</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Reminders Modal -->
<div class="modal fade" id="sendRemindersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.fees.records.send-reminders') }}">
                @csrf
                <input type="hidden" name="fee_record_id" id="reminder_fee_record_id" value="">
                <div class="modal-header bg-warning bg-opacity-25">
                    <h5 class="modal-title"><i class="fas fa-bell me-2 text-warning"></i>Send Fee Payment Reminders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Single Student Notice if triggered from a specific invoice row -->
                    <div id="singleReminderNotice" class="alert alert-info d-none mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-user-tag me-1"></i> Quick Reminder for: <strong id="noticeStudentName"></strong><br>
                                <small class="text-muted">Invoice: <span id="noticeFeeName"></span> | Outstanding: <span id="noticeBalance" class="fw-bold text-danger"></span></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetReminderTarget()">Switch to Group</button>
                        </div>
                    </div>

                    <!-- Target Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Audience <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 p-2 bg-light rounded border">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="target_group" value="group" checked onchange="toggleReminderAudience()">
                                <label class="form-check-label fw-semibold" for="target_group">
                                    <i class="fas fa-users me-1 text-primary"></i> Filtered Group of Students
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="target_single" value="single" onchange="toggleReminderAudience()">
                                <label class="form-check-label fw-semibold" for="target_single">
                                    <i class="fas fa-user me-1 text-success"></i> Single Student
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Single Student Selector -->
                    <div id="singleStudentContainer" class="mb-3 d-none">
                        <label for="reminder_student_id" class="form-label fw-bold">Select Student <span class="text-danger">*</span></label>
                        <select class="form-select" name="student_id" id="reminder_student_id">
                            <option value="">-- Choose a student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }} ({{ $student->email }}) {{ $student->studentProfile?->admission_number ? ' - Adm: ' . $student->studentProfile->admission_number : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Search and pick any registered student with pending balances.</small>
                    </div>

                    <!-- Group Filters -->
                    <div id="groupFiltersContainer">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="reminder_type" class="form-label fw-bold">Reminder Status Filter</label>
                                <select class="form-select" name="reminder_type" id="reminder_type">
                                    <option value="all">All Pending / Partial Balances</option>
                                    <option value="due_soon">Due Soon (Approaching Deadline)</option>
                                    <option value="overdue">Overdue Only</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="days_before_container" style="display: none;">
                                <label for="days_before_due" class="form-label fw-bold">Days Before Due Date</label>
                                <input type="number" class="form-control" name="days_before_due" id="days_before_due" value="7" min="1" max="30">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="reminder_department_id" class="form-label fw-semibold">Department</label>
                                <select class="form-select" name="department_id" id="reminder_department_id">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="reminder_program_id" class="form-label fw-semibold">Program</label>
                                <select class="form-select" name="program_id" id="reminder_program_id">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $prog)
                                        <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="reminder_semester_id" class="form-label fw-semibold">Semester</label>
                                <select class="form-select" name="semester_id" id="reminder_semester_id">
                                    <option value="">All Semesters</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Message -->
                    <div class="mb-3">
                        <label for="custom_message" class="form-label fw-bold">Custom Note / Urgent Instructions (Optional)</label>
                        <textarea class="form-control" name="custom_message" id="custom_message" rows="3" placeholder="Leave empty for standard reminder, or enter custom instructions (e.g. deadline extension, bank deposit details)..."></textarea>
                        <small class="text-muted"><i class="fas fa-paper-plane me-1 text-primary"></i> Dispatched via in-app alert, portal direct message, and email notification.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane me-1"></i> Send Reminders</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function exportFeeRecords() {
    window.location.href = '{{ route("admin.fees.records.export") }}';
}

function confirmDelete(recordId) {
    if (confirm('Are you sure you want to delete this fee record?')) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/fees/records/${recordId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDeleteStructure(structureId) {
    if (confirm('Are you sure you want to delete this fee structure?')) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/fees/structures/${structureId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function openSingleReminderModal(recordId, studentId, studentName, balance, feeName) {
    const feeInput = document.getElementById('reminder_fee_record_id');
    if (feeInput) feeInput.value = recordId || '';

    const singleRadio = document.getElementById('target_single');
    const groupRadio = document.getElementById('target_group');
    if (singleRadio && groupRadio) {
        singleRadio.checked = true;
        groupRadio.checked = false;
    }
    toggleReminderAudience();

    const studentSelect = document.getElementById('reminder_student_id');
    if (studentSelect && studentId) {
        studentSelect.value = studentId;
    }

    const nameEl = document.getElementById('noticeStudentName');
    const feeEl = document.getElementById('noticeFeeName');
    const balEl = document.getElementById('noticeBalance');
    const noticeEl = document.getElementById('singleReminderNotice');

    if (nameEl) nameEl.textContent = studentName;
    if (feeEl) feeEl.textContent = feeName;
    if (balEl) balEl.textContent = balance;
    if (noticeEl) noticeEl.classList.remove('d-none');

    const modalEl = document.getElementById('sendRemindersModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function resetReminderTarget() {
    const feeInput = document.getElementById('reminder_fee_record_id');
    if (feeInput) feeInput.value = '';

    const singleRadio = document.getElementById('target_single');
    const groupRadio = document.getElementById('target_group');
    if (singleRadio && groupRadio) {
        groupRadio.checked = true;
        singleRadio.checked = false;
    }

    const noticeEl = document.getElementById('singleReminderNotice');
    if (noticeEl) noticeEl.classList.add('d-none');

    toggleReminderAudience();
}

function toggleReminderAudience() {
    const singleRadio = document.getElementById('target_single');
    const isSingle = singleRadio ? singleRadio.checked : false;
    const singleContainer = document.getElementById('singleStudentContainer');
    const groupContainer = document.getElementById('groupFiltersContainer');

    if (singleContainer) {
        singleContainer.classList.toggle('d-none', !isSingle);
    }
    if (groupContainer) {
        groupContainer.classList.toggle('d-none', isSingle);
    }
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    const currencyCode = @json($currencyCode);

    // Collection Chart
    const collectionCtx = document.getElementById('collectionChart').getContext('2d');
    new Chart(collectionCtx, {
        type: 'line',
        data: {
            labels: @json($collectionChartLabels),
            datasets: [{
                label: 'Collections ($)',
                data: @json($collectionChartData),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return currencyCode + ' ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Collections: ' + currencyCode + ' ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Overdue', 'Partial'],
            datasets: [{
                data: @json($statusChartData),
                backgroundColor: [
                    '#28a745',
                    '#6c757d',
                    '#dc3545',
                    '#ffc107'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Semester Chart
    const semesterCtx = document.getElementById('semesterChart').getContext('2d');
    new Chart(semesterCtx, {
        type: 'bar',
        data: {
            labels: @json($semesterChartLabels),
            datasets: [{
                label: 'Paid Amount',
                data: @json($semesterChartData),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return currencyCode + ' ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Cash Status Chart
    const cashStatusCtx = document.getElementById('cashStatusChart').getContext('2d');
    new Chart(cashStatusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($cashStatusLabels),
            datasets: [{
                data: @json($cashStatusData),
                backgroundColor: [
                    '#ffc107',
                    '#28a745',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Handle reminder type change
    document.querySelector('select[name="reminder_type"]').addEventListener('change', function() {
        const daysContainer = document.getElementById('days_before_container');
        if (this.value === 'due_soon') {
            daysContainer.style.display = 'block';
        } else {
            daysContainer.style.display = 'none';
        }
    });

    const allStudentsRadio = document.getElementById('all_students');
    const selectedStudentsRadio = document.getElementById('selected_students');
    const studentSelectWrapper = document.getElementById('studentSelectWrapper');
    const studentSelect = document.getElementById('student_ids');
    const studentSearchInput = document.getElementById('studentSearchInput');
    const selectVisibleStudents = document.getElementById('selectVisibleStudents');
    const clearSelectedStudents = document.getElementById('clearSelectedStudents');
    const summarySelectedStudents = document.getElementById('summarySelectedStudents');
    const summaryEstimatedTotal = document.getElementById('summaryEstimatedTotal');
    const feeStructureSelect = document.getElementById('fee_structure_id');
    const feeStructureMeta = document.getElementById('feeStructureMeta');
    const dueDateInput = document.getElementById('invoice_due_date');
    const semesterFilterSelect = document.getElementById('invoice_semester_id');
    const courseFilterSelect = document.getElementById('invoice_course_id');
    const invoiceGenerationForm = document.getElementById('invoiceGenerationForm');
    const generateInvoicesSubmitBtn = document.getElementById('generateInvoicesSubmitBtn');

    function toggleStudentSelection() {
        const showSelect = selectedStudentsRadio.checked;
        studentSelectWrapper.classList.toggle('d-none', !showSelect);
        if (!showSelect) {
            Array.from(studentSelect.options).forEach((opt) => {
                opt.selected = false;
            });
        }
        updateInvoiceSummary();
    }

    function selectedCount() {
        return Array.from(studentSelect.options).filter((opt) => opt.selected).length;
    }

    function getFeeAmount() {
        const selected = feeStructureSelect.options[feeStructureSelect.selectedIndex];
        return selected ? parseFloat(selected.dataset.amount || '0') : 0;
    }

    function updateFeeStructureMeta() {
        const selected = feeStructureSelect.options[feeStructureSelect.selectedIndex];
        if (!selected || !selected.value) {
            feeStructureMeta.textContent = 'Select a fee structure to see amount and default due date.';
            dueDateInput.value = '';
            return;
        }
        const amount = parseFloat(selected.dataset.amount || '0');
        const dueDate = selected.dataset.dueDate || '';
        feeStructureMeta.textContent = `Default amount: ${currencyCode} ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` + (dueDate ? ` | Default due date: ${dueDate}` : '');
        if (!dueDateInput.value && dueDate) {
            dueDateInput.value = dueDate;
        }
        updateInvoiceSummary();
    }

    function updateInvoiceSummary() {
        const count = selectedStudentsRadio.checked ? selectedCount() : parseInt(document.getElementById('summaryTotalStudents').textContent, 10);
        const amount = getFeeAmount();
        summarySelectedStudents.textContent = selectedStudentsRadio.checked ? count : 'All';
        summaryEstimatedTotal.textContent = `${currencyCode} ${(count * amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        generateInvoicesSubmitBtn.disabled = !feeStructureSelect.value || (selectedStudentsRadio.checked && count === 0);
    }

    allStudentsRadio.addEventListener('change', toggleStudentSelection);
    selectedStudentsRadio.addEventListener('change', toggleStudentSelection);
    studentSelect.addEventListener('change', updateInvoiceSummary);
    feeStructureSelect.addEventListener('change', updateFeeStructureMeta);

    studentSearchInput.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        Array.from(studentSelect.options).forEach((opt) => {
            const matches = !keyword || opt.dataset.search.includes(keyword);
            opt.hidden = !matches;
        });
    });

    selectVisibleStudents.addEventListener('click', function () {
        Array.from(studentSelect.options).forEach((opt) => {
            if (!opt.hidden) {
                opt.selected = true;
            }
        });
        updateInvoiceSummary();
    });

    clearSelectedStudents.addEventListener('click', function () {
        Array.from(studentSelect.options).forEach((opt) => {
            opt.selected = false;
        });
        updateInvoiceSummary();
    });

    semesterFilterSelect.addEventListener('change', function () {
        const semesterId = this.value;
        Array.from(courseFilterSelect.options).forEach((opt, index) => {
            if (index === 0) {
                opt.hidden = false;
                return;
            }

            const matchesSemester = !semesterId || opt.dataset.semesterId === semesterId;
            opt.hidden = !matchesSemester;

            if (!matchesSemester && opt.selected) {
                opt.selected = false;
            }
        });

        if (!courseFilterSelect.value || courseFilterSelect.selectedOptions[0]?.hidden) {
            courseFilterSelect.value = '';
        }
    });

    invoiceGenerationForm.addEventListener('submit', function (event) {
        const selectedStructure = feeStructureSelect.value;
        const count = selectedStudentsRadio.checked ? selectedCount() : parseInt(document.getElementById('summaryTotalStudents').textContent, 10);

        if (!selectedStructure) {
            event.preventDefault();
            alert('Please select a fee structure before generating invoices.');
            return;
        }

        if (selectedStudentsRadio.checked && count === 0) {
            event.preventDefault();
            alert('Please select at least one student.');
            return;
        }

        const targetText = selectedStudentsRadio.checked ? `${count} selected student(s)` : `all active students (${count})`;
        if (!confirm(`Generate invoices for ${targetText}?`)) {
            event.preventDefault();
        }
    });

    toggleStudentSelection();
    updateFeeStructureMeta();
    updateInvoiceSummary();
});
</script>
@endpush
