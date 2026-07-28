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
                        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#generateInvoicesModal">
                            <i class="fas fa-file-invoice"></i> Generate Invoices
                        </button>
                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#sendRemindersModal">
                            <i class="fas fa-bell"></i> Send Reminders
                        </button>
                        <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-success me-2">
                            <i class="fas fa-plus"></i> Add Fee Structure
                        </a>
                        <a href="{{ route('admin.fees.records.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Fee Record
                        </a>
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
                            <button class="nav-link active" id="records-tab" data-bs-toggle="tab"
                                    data-bs-target="#records" type="button" role="tab">
                                Fee Records
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="structures-tab" data-bs-toggle="tab"
                                    data-bs-target="#structures" type="button" role="tab">
                                Fee Structures
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reports-tab" data-bs-toggle="tab"
                                    data-bs-target="#reports" type="button" role="tab">
                                Reports
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="feesTabContent">
                        <!-- Fee Records Tab -->
                        <div class="tab-pane fade show active" id="records" role="tabpanel">
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
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-success" onclick="exportFeeRecords()">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
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
                                                        @if($record->student->avatar)
                                                            <img src="{{ asset('storage/' . $record->student->avatar) }}"
                                                                 class="rounded-circle me-2" width="32" height="32">
                                                        @else
                                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                 style="width: 32px; height: 32px;">
                                                                <span class="text-white small">{{ substr($record->student->first_name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div>{{ $record->student->first_name }} {{ $record->student->last_name }}</div>
                                                            <small class="text-muted">{{ $record->student->studentProfile->student_id ?? 'N/A' }}</small>
                                                        </div>
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
                                                        @if($record->status != 'paid')
                                                        <a href="{{ route('admin.fees.records.payment', $record) }}"
                                                           class="btn btn-sm btn-outline-success" title="Record Payment">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('admin.fees.records.edit', $record) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @if($record->paid_amount == 0)
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.fees.records.send-reminders') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Send Payment Reminders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reminder_type" class="form-label">Reminder Type</label>
                        <select class="form-control" name="reminder_type" required>
                            <option value="due_soon">Due Soon</option>
                            <option value="overdue">Overdue</option>
                            <option value="all">All Pending</option>
                        </select>
                    </div>
                    <div class="mb-3" id="days_before_container">
                        <label for="days_before_due" class="form-label">Days Before Due Date</label>
                        <input type="number" class="form-control" name="days_before_due" value="7" min="1" max="30">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Reminders</button>
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
