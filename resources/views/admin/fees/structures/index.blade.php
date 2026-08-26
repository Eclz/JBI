@extends('layouts.app')

@section('title', 'Fee Structures')

@push('styles')
<style>
    .level-chip { display: inline-flex; align-items: center; color: #172033 !important; font-weight: 700; border: 1px solid transparent; white-space: nowrap; }
    .level-chip-CERT { background: #e8f0ff !important; border-color: #8db1ff; color: #123b8f !important; }
    .level-chip-ADVDIP { background: #e2f7fb !important; border-color: #7ccbd8; color: #155b66 !important; }
    .level-chip-DIP { background: #e7f7ed !important; border-color: #82cea0; color: #176038 !important; }
    .level-chip-BACH { background: #fff4cf !important; border-color: #e4c45c; color: #684f00 !important; }
    .level-chip-MASTER { background: #fde9ed !important; border-color: #e497a5; color: #84243a !important; }
    .level-chip-PHD { background: #e9eaf0 !important; border-color: #9da1af; color: #242733 !important; }
    .level-chip-GENERAL { background: #eef0f3 !important; border-color: #b8bec8; color: #394150 !important; }
</style>
@endpush

@section('content')
@php
    $levelColours = ['CERT' => 'primary', 'ADVDIP' => 'info', 'DIP' => 'success', 'BACH' => 'warning', 'MASTER' => 'danger', 'PHD' => 'dark'];
@endphp
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

            <div class="card mb-4">
                <div class="card-header bg-white"><h5 class="card-title mb-0"><i class="bi bi-funnel me-2"></i>Find a fee structure</h5></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.fees.structures.index') }}" class="row g-3">
                        <div class="col-lg-4"><label class="form-label">Search programme or code</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="e.g. Theology or FTM-CERT"></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Level</label><select class="form-select" name="level"><option value="">All levels</option>@foreach($levels as $level)<option value="{{ $level->id }}" @selected(request('level') == $level->id)>{{ $level->name }}</option>@endforeach</select></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Currency</label><select class="form-select" name="currency"><option value="">All currencies</option>@foreach($currencies as $currency)<option value="{{ $currency }}" @selected(request('currency') === $currency)>{{ $currency }}</option>@endforeach</select></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Student region</label><select class="form-select" name="region"><option value="">All regions</option><option value="local" @selected(request('region') === 'local')>Local</option><option value="international" @selected(request('region') === 'international')>International</option></select></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Fee type</label><select class="form-select" name="type"><option value="">All types</option><option value="tuition" @selected(request('type') === 'tuition')>Tuition</option><option value="registration" @selected(request('type') === 'registration')>Admission & registration</option><option value="other" @selected(request('type') === 'other')>Other</option></select></div>
                        <div class="col-lg-8"><label class="form-label">Programme</label><select class="form-select" name="program"><option value="">All programmes</option>@foreach($programs as $program)<option value="{{ $program->id }}" @selected(request('program') == $program->id)>{{ $program->level->name ?? 'General' }} — {{ $program->name }}</option>@endforeach</select></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></div>
                        <div class="col-md-8 col-lg-2 d-flex align-items-end gap-2"><button class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i>Filter</button><a class="btn btn-outline-secondary" href="{{ route('admin.fees.structures.index') }}" title="Clear filters"><i class="bi bi-x-lg"></i></a></div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Fee Structures <span class="badge bg-secondary ms-1">{{ $feeStructures->total() }}</span></h5>
                </div>
                <div class="card-body">
                    @if($feeStructures->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Programme</th>
                                        <th>Fee &amp; Level</th>
                                        <th>Per Semester / Once-off</th>
                                        <th>Published Total</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeStructures as $structure)
                                        @php($levelColour = $levelColours[$structure->programLevel->code ?? ''] ?? 'secondary')
                                        <tr class="border-start border-3 border-{{ $levelColour }}">
                                            <td>
                                                <strong>{{ $structure->program->name ?? 'All programmes' }}</strong>
                                                @if($structure->program)<br><small class="text-muted"><code>{{ $structure->program->code }}</code> · {{ $structure->program->department->name ?? '' }}</small>@endif
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge level-chip level-chip-{{ $structure->programLevel->code ?? 'GENERAL' }}">{{ $structure->programLevel->name ?? 'General' }}</span>
                                                    <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $structure->type)) }} · {{ ucfirst($structure->student_region ?? 'all regions') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php($currency = $structure->currency ?? $currencyCode)
                                                <span class="badge {{ $currency === 'USD' ? 'bg-success' : 'bg-primary' }} me-1">{{ $currency }}</span><strong>{{ number_format($structure->amount, 2) }}</strong>
                                                <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $structure->frequency)) }}</small>
                                            </td>
                                            <td>
                                                @if($structure->total_amount)<strong>{{ $currency }} {{ number_format($structure->total_amount, 2) }}@if($structure->total_amount_max && $structure->total_amount_max != $structure->total_amount) – {{ number_format($structure->total_amount_max, 2) }}@endif</strong>@else<span class="text-muted">—</span>@endif
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
