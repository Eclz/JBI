@extends('layouts.app')

@section('title', 'Edit Job Role - ' . $jobRole->title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Job Role</h2>
            <p class="text-muted mb-0">Update role settings, department scope, and salary band bounds.</p>
        </div>
        <a href="{{ route('human-resources.job-roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Please resolve the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="max-width: 900px;">
        <div class="card-body p-4">
            <form action="{{ route('human-resources.job-roles.update', $jobRole) }}" method="POST" id="jobRoleForm">
                @csrf
                @method('PUT')

                <!-- Link to System Role -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-shield-check text-primary me-1"></i>Associated System Role
                    </label>
                    <select name="role_id" id="role_id_select" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="" data-title="" data-description="">-- Custom Role (Not directly linked to system permission role) --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" 
                                    data-title="{{ $role->name }}" 
                                    data-description="{{ $role->description }}"
                                    @selected(old('role_id', $jobRole->role_id) == $role->id)>
                                {{ $role->name }} (System Role - {{ ucfirst($role->guard_role ?? 'General') }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">
                        Connecting with a system role lets staff members with this system permission role inherit this salary band in payroll.
                    </div>
                    @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-4">
                    <!-- Role Title -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Role Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="role_title" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               value="{{ old('title', $jobRole->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Departments Multi-select Dropdown -->
                <div class="mb-4">
                    <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-building text-primary me-1"></i>Applicable Departments</span>
                        <span class="badge bg-light text-secondary border fw-normal" id="deptCountBadge">0 Selected</span>
                    </label>

                    <div class="dropdown" id="departmentDropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center bg-white py-2" 
                                type="button" id="departmentDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <span id="selectedDepartmentsSummary" class="text-muted">Click to select departments (or leave empty for institution-wide)...</span>
                        </button>
                        <div class="dropdown-menu p-3 w-100 shadow border-0" aria-labelledby="departmentDropdownBtn" style="max-height: 340px; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                <span class="small fw-semibold text-muted">Select applicable departments</span>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary py-0 px-2" id="selectAllDepts">Select All</button>
                                    <button type="button" class="btn btn-outline-secondary py-0 px-2" id="clearAllDepts">Clear All (None)</button>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="searchDeptInput" placeholder="Filter departments...">
                            </div>

                            <div class="department-checkbox-list" id="deptCheckboxList">
                                @php 
                                    $currentDepts = (array) old('departments', $jobRole->departments ?? []); 
                                @endphp
                                @foreach($departments as $dept)
                                    <div class="form-check py-1 dept-item">
                                        <input class="form-check-input dept-checkbox" type="checkbox" name="departments[]" 
                                               value="{{ $dept }}" id="dept_{{ Str::slug($dept) }}"
                                               @checked(in_array($dept, $currentDepts))>
                                        <label class="form-check-label user-select-none" for="dept_{{ Str::slug($dept) }}">
                                            {{ $dept }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-text text-muted mt-1">
                        <i class="bi bi-info-circle me-1"></i>Some roles apply to multiple departments, others apply across all departments, and some apply to no specific department (institution-wide).
                    </div>

                    <!-- Selected Badges Display -->
                    <div id="selectedDepartmentsBadges" class="d-flex flex-wrap gap-1 mt-2"></div>
                </div>

                <!-- Salary Bands -->
                <div class="card bg-light border-0 p-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-cash-stack text-success me-1"></i>Salary Band Configuration
                    </h6>
                    <p class="small text-muted mb-3">
                        These salary bands directly reflect in the payroll module when computing monthly payroll and editing payroll records.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Minimum Salary Band</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">$</span>
                                <input type="number" step="0.01" min="0" name="salary_band_min" id="salary_band_min" 
                                       class="form-control @error('salary_band_min') is-invalid @enderror" 
                                       value="{{ old('salary_band_min', $jobRole->salary_band_min) }}" placeholder="0.00">
                            </div>
                            @error('salary_band_min') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Salary Band</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">$</span>
                                <input type="number" step="0.01" min="0" name="salary_band_max" id="salary_band_max" 
                                       class="form-control @error('salary_band_max') is-invalid @enderror" 
                                       value="{{ old('salary_band_max', $jobRole->salary_band_max) }}" placeholder="0.00">
                            </div>
                            @error('salary_band_max') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Description & Responsibilities</label>
                    <textarea name="description" id="role_description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Brief summary of duties, requirements, or institutional role...">{{ old('description', $jobRole->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Role Status -->
                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $jobRole->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Role is Active</label>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('human-resources.job-roles.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i>Update Job Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Multi-Department Dropdown & Badges logic
    const deptCheckboxes = document.querySelectorAll('.dept-checkbox');
    const summarySpan = document.getElementById('selectedDepartmentsSummary');
    const countBadge = document.getElementById('deptCountBadge');
    const badgesContainer = document.getElementById('selectedDepartmentsBadges');
    const selectAllBtn = document.getElementById('selectAllDepts');
    const clearAllBtn = document.getElementById('clearAllDepts');
    const searchInput = document.getElementById('searchDeptInput');

    function updateDepartmentDisplay() {
        const selected = [];
        deptCheckboxes.forEach(cb => {
            if (cb.checked) {
                selected.push(cb.value);
            }
        });

        countBadge.textContent = selected.length + ' Selected';
        badgesContainer.innerHTML = '';

        if (selected.length === 0) {
            summarySpan.textContent = 'None selected (Applies to all / Institution-wide)';
            summarySpan.className = 'text-muted fst-italic';
            const noneBadge = document.createElement('span');
            noneBadge.className = 'badge bg-secondary bg-opacity-10 text-secondary border py-1 px-2';
            noneBadge.innerHTML = '<i class="bi bi-globe me-1"></i>Institution-wide / No specific department';
            badgesContainer.appendChild(noneBadge);
        } else {
            summarySpan.className = 'text-dark fw-medium text-truncate';
            if (selected.length === deptCheckboxes.length) {
                summarySpan.textContent = 'All Departments Selected (' + selected.length + ')';
            } else if (selected.length <= 3) {
                summarySpan.textContent = selected.join(', ');
            } else {
                summarySpan.textContent = selected.slice(0, 3).join(', ') + ' +' + (selected.length - 3) + ' more';
            }

            selected.forEach(deptName => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-1 px-2 d-inline-flex align-items-center gap-1';
                badge.innerHTML = `<span>${deptName}</span><i class="bi bi-x-circle-fill cursor-pointer text-danger ms-1" style="cursor:pointer;" title="Remove"></i>`;
                
                badge.querySelector('i').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const targetCb = Array.from(deptCheckboxes).find(c => c.value === deptName);
                    if (targetCb) {
                        targetCb.checked = false;
                        updateDepartmentDisplay();
                    }
                });
                badgesContainer.appendChild(badge);
            });
        }
    }

    deptCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateDepartmentDisplay);
    });

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            deptCheckboxes.forEach(cb => {
                cb.checked = true;
            });
            updateDepartmentDisplay();
        });
    }

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            deptCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            updateDepartmentDisplay();
        });
    }

    // Filter departments in dropdown
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.dept-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Initialize display on load
    updateDepartmentDisplay();
});
</script>
@endsection
