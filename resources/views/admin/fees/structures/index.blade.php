@extends('layouts.app')

@section('title', 'Fee Structures')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Fee Structures</h1>
                    <p class="text-muted">Manage fee structures and pricing</p>
                </div>
                <div>
                    <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Fee Structure
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Fee Structures</h5>
                </div>
                <div class="card-body">
                    @if($feeStructures->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Frequency</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeStructures as $structure)
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
                                            <td>
                                                <strong>{{ $currencyCode }} {{ number_format($structure->amount, 2) }}</strong>
                                                @if($structure->late_fee_amount > 0)
                                                    <br><small class="text-warning">Late Fee: {{ $currencyCode }} {{ number_format($structure->late_fee_amount, 2) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $structure->frequency)) }}</span>
                                            </td>
                                            <td>{{ $structure->academicYear->name ?? 'N/A' }}</td>
                                            <td>{{ $structure->semester->name ?? 'All Semesters' }}</td>
                                            <td>
                                                @if($structure->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                                @if($structure->is_mandatory)
                                                    <span class="badge bg-warning">Mandatory</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($structure->due_date)
                                                    {{ $structure->due_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.fees.structures.show', $structure) }}"
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.fees.structures.edit', $structure) }}"
                                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete({{ $structure->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $feeStructures->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5>No Fee Structures Found</h5>
                            <p class="text-muted">Create your first fee structure to get started.</p>
                            <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Fee Structure
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this fee structure? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> You can only delete fee structures that are not being used by any fee records.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(structureId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/fees/structures/${structureId}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
