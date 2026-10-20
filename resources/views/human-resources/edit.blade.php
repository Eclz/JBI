@extends('layouts.app')

@section('title', 'Edit Staff: ' . $employee->full_name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Breadcrumb Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('human-resources.index') }}" class="text-decoration-none text-muted">HR Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('human-resources.staff.index') }}" class="text-decoration-none text-muted">Staff Records</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $employee->full_name }}</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold text-dark mb-0">Edit Staff Record: {{ $employee->full_name }}</h2>
            <p class="text-muted small mb-0">Update personal HR record, assigned department, salary band, and employment status.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('human-resources.staff.show', $employee) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-1"></i> View Profile
            </a>
            <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Staff List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Form Column (Left) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>Employment & HR Profile
                    </h5>
                    @php
                        $status = $hrProfile?->status ?? ($employee->is_active ? 'Active' : 'Inactive');
                        $badgeClass = $status === 'Active' ? 'success' : ($status === 'On Leave' ? 'info' : 'warning');
                    @endphp
                    <span class="badge text-bg-{{ $badgeClass }} px-3 py-2 rounded-pill">{{ $status }}</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('human-resources.staff.update', $employee) }}" id="staffEditForm">
                        @csrf
                        @method('PUT')

                        @include('human-resources.form', [
                            'employee' => $employee,
                            'hrProfile' => $hrProfile,
                            'jobRoles' => $jobRoles ?? [],
                            'departments' => $departments ?? collect(),
                            'adminDepartments' => $adminDepartments ?? []
                        ])

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary px-4 py-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Update Employee Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right): Staff Overview & Departments List -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Staff Summary Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 text-center">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary fw-bold" style="width: 72px; height: 72px; font-size: 1.75rem;">
                            {{ strtoupper(substr($employee->first_name ?: $employee->name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $employee->full_name }}</h5>
                        <p class="text-muted small mb-2">{{ $employee->email }}</p>
                        <div class="d-flex justify-content-center gap-1 mb-3">
                            <span class="badge bg-light text-dark border">{{ ucfirst($employee->role ?? 'Staff') }}</span>
                            @if($hrProfile?->employee_number)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $hrProfile->employee_number }}</span>
                            @endif
                        </div>

                        <div class="text-start bg-light p-3 rounded-3 small">
                            <div class="mb-2">
                                <span class="text-muted fw-semibold">HR Department:</span>
                                <span class="float-end fw-medium text-dark">
                                    {{ $hrProfile?->department ?? 'Not assigned' }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted fw-semibold">Phone:</span>
                                <span class="float-end text-dark">{{ $employee->phone ?? '—' }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted fw-semibold">Salary Band:</span>
                                <span class="float-end text-dark">{{ $hrProfile?->salary_band ?? '—' }}</span>
                            </div>

                            @if($employee->headedDepartments && $employee->headedDepartments->isNotEmpty())
                                <div class="pt-2 mt-2 border-top">
                                    <div class="text-primary fw-bold mb-1"><i class="bi bi-star-fill text-warning me-1"></i>Head of Department:</div>
                                    <ul class="list-unstyled mb-0 ps-1">
                                        @foreach($employee->headedDepartments as $headedDept)
                                            <li class="d-flex justify-content-between align-items-center py-1">
                                                <span>{{ $headedDept->name }}</span>
                                                <span class="badge bg-primary text-white">{{ $headedDept->code }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($employee->facultyProfile && $employee->facultyProfile->department)
                                <div class="pt-2 mt-2 border-top">
                                    <span class="text-muted fw-semibold">Academic Profile Dept:</span>
                                    <div class="fw-medium text-dark mt-1">{{ $employee->facultyProfile->department->name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Departments Directory Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-building text-primary me-1"></i> Institutional Departments
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            {{ isset($departments) ? $departments->count() : 0 }} Academic
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-2">
                            Quick-assign any active university department directly into the employee's HR record:
                        </p>

                        <!-- Search Filter Box -->
                        <div class="mb-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="quickDeptSearch" class="form-control border-start-0 ps-0" placeholder="Filter departments...">
                            </div>
                        </div>

                        <!-- Academic Departments List -->
                        <div class="mb-2 small fw-semibold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            Academic Departments
                        </div>
                        <div class="list-group list-group-flush mb-3" id="academicDeptList" style="max-height: 240px; overflow-y: auto;">
                            @if(isset($departments) && $departments->isNotEmpty())
                                @foreach($departments as $dept)
                                    <div class="list-group-item list-group-item-action px-2 py-2 d-flex justify-content-between align-items-center dept-item" 
                                         data-search="{{ strtolower($dept->name . ' ' . $dept->code . ' ' . ($dept->faculty?->name ?? '')) }}">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $dept->name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <span class="badge bg-light text-secondary border me-1">{{ $dept->code ?? 'N/A' }}</span>
                                                {{ $dept->faculty?->name ?? 'Academic Faculty' }}
                                            </div>
                                        </div>
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-primary py-1 px-2 select-dept-action" 
                                                data-dept-name="{{ $dept->name }}" 
                                                data-dept-id="{{ $dept->id }}"
                                                title="Assign {{ $dept->name }} to this employee">
                                            Select
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted small p-2 text-center">No academic departments registered.</div>
                            @endif
                        </div>

                        <!-- Administrative Units List -->
                        @if(isset($adminDepartments) && count($adminDepartments) > 0)
                            <div class="mb-2 small fw-semibold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                Administrative & Support Units
                            </div>
                            <div class="list-group list-group-flush" id="adminDeptList" style="max-height: 180px; overflow-y: auto;">
                                @foreach($adminDepartments as $adminDept)
                                    <div class="list-group-item list-group-item-action px-2 py-2 d-flex justify-content-between align-items-center dept-item" 
                                         data-search="{{ strtolower($adminDept) }}">
                                        <div class="fw-medium text-dark small">
                                            <i class="bi bi-folder2 text-secondary me-1"></i>{{ $adminDept }}
                                        </div>
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-secondary py-1 px-2 select-dept-action" 
                                                data-dept-name="{{ $adminDept }}" 
                                                data-dept-id=""
                                                title="Assign {{ $adminDept }} to this employee">
                                            Select
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @can('create', App\Models\Department::class)
                            <div class="mt-3 pt-2 border-top text-center">
                                <a href="{{ route('admin.departments.create') }}" target="_blank" class="text-decoration-none small text-primary fw-semibold">
                                    <i class="bi bi-plus-circle me-1"></i> Create New Department
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Real-time quick search filter for the departments list
        const searchInput = document.getElementById('quickDeptSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const items = document.querySelectorAll('.dept-item');

                items.forEach(item => {
                    const searchData = item.getAttribute('data-search') || '';
                    if (query === '' || searchData.includes(query)) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });
            });
        }

        // Quick select buttons from the departments list
        const selectButtons = document.querySelectorAll('.select-dept-action');
        selectButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const name = this.getAttribute('data-dept-name');
                const id = this.getAttribute('data-dept-id');

                if (window.selectStaffDepartment) {
                    window.selectStaffDepartment(name, id);
                }

                // Button click feedback
                const origText = this.innerHTML;
                this.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                this.classList.add('btn-success', 'text-white');
                this.innerHTML = '<i class="bi bi-check"></i> Selected';

                setTimeout(() => {
                    this.classList.remove('btn-success', 'text-white');
                    this.classList.add(id ? 'btn-outline-primary' : 'btn-outline-secondary');
                    this.innerHTML = origText;
                }, 1500);
            });
        });
    });
</script>
@endsection
