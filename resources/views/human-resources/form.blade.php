@php
    $currentDeptName = old('department', $hrProfile?->department ?? $employee->facultyProfile?->department?->name ?? '');
    $currentDeptId = old('department_id', $hrProfile?->department_id ?? $employee->facultyProfile?->department_id ?? '');

    // Check if current value matches any academic department
    $academicMatch = isset($departments) ? $departments->first(function($d) use ($currentDeptName, $currentDeptId) {
        return ($currentDeptId && (string)$d->id === (string)$currentDeptId) || 
               ($currentDeptName && strcasecmp($d->name, $currentDeptName) === 0);
    }) : null;

    // Check if current value matches an administrative department
    $adminMatch = collect($adminDepartments ?? [])->first(function($name) use ($currentDeptName) {
        return $currentDeptName && strcasecmp($name, $currentDeptName) === 0;
    });

    $isCustomDept = !empty($currentDeptName) && !$academicMatch && !$adminMatch;
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Linked User</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
            <input type="text" readonly class="form-control bg-light" value="{{ $employee->full_name }} ({{ $employee->email }})">
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold" for="employee_number">
            Employee Number <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-card-heading"></i></span>
            <input name="employee_number" id="employee_number" required class="form-control @error('employee_number') is-invalid @enderror" value="{{ old('employee_number', $hrProfile?->employee_number ?? '') }}">
            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('employee_number').value = 'EMP-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9999) + 1).padStart(4, '0')">
                <i class="bi bi-magic me-1"></i>Generate
            </button>
            @error('employee_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold" for="job_title">
            Job Title <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-briefcase"></i></span>
            <input name="job_title" id="job_title" required class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $hrProfile?->job_title ?? $employee->role_name) }}" placeholder="e.g. Senior Lecturer, HR Officer">
            @error('job_title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold d-flex justify-content-between align-items-center" for="department_select">
            <span><i class="bi bi-building text-primary me-1"></i>Department</span>
            @if(!empty($currentDeptName))
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-normal">
                    Current: {{ $currentDeptName }}
                </span>
            @endif
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-diagram-3"></i></span>
            <select name="department" id="department_select" class="form-select @error('department') is-invalid @enderror">
                <option value="" data-department-id="">— Select Department —</option>

                @if(isset($departments) && $departments->isNotEmpty())
                    <optgroup label="Academic Departments">
                        @foreach($departments as $dept)
                            @php
                                $isSelected = ($academicMatch && $academicMatch->id === $dept->id) ||
                                              (!$academicMatch && $currentDeptName === $dept->name);
                            @endphp
                            <option value="{{ $dept->name }}" 
                                    data-department-id="{{ $dept->id }}"
                                    data-code="{{ $dept->code }}"
                                    @selected($isSelected)>
                                {{ $dept->name }} {{ $dept->code ? '('.$dept->code.')' : '' }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif

                @if(isset($adminDepartments) && count($adminDepartments) > 0)
                    <optgroup label="Administrative & Support Units">
                        @foreach($adminDepartments as $adminDept)
                            <option value="{{ $adminDept }}" 
                                    data-department-id=""
                                    @selected($currentDeptName === $adminDept)>
                                {{ $adminDept }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif

                @if($isCustomDept)
                    <optgroup label="Current Assigned Custom Department">
                        <option value="{{ $currentDeptName }}" data-department-id="{{ $currentDeptId }}" selected>
                            {{ $currentDeptName }}
                        </option>
                    </optgroup>
                @endif

                <option value="__custom__" data-department-id="">+ Enter Other / Custom Department...</option>
            </select>
        </div>
        <input type="hidden" name="department_id" id="department_id_hidden" value="{{ $academicMatch?->id ?? $currentDeptId }}">

        <div id="custom_department_wrapper" class="mt-2 {{ $isCustomDept ? '' : 'd-none' }}">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted"><i class="bi bi-pencil-square"></i></span>
                <input type="text" id="custom_department_input" class="form-control" placeholder="Specify custom department name..." value="{{ $isCustomDept ? $currentDeptName : '' }}">
            </div>
            <div class="form-text text-muted">Enter a custom department name if not listed in the options above.</div>
        </div>

        @error('department')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold" for="employment_type">Employment Type</label>
        <select name="employment_type" id="employment_type" class="form-select">
            @foreach(['Full-time','Part-time','Contract','Intern'] as $type)
                <option @selected(old('employment_type', $hrProfile?->employment_type ?? 'Full-time') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold" for="salary_band">Salary Band (Job Role)</label>
        <select name="salary_band" id="salary_band" class="form-select">
            <option value="">Select Band/Role</option>
            @foreach($jobRoles as $role)
                <option value="{{ $role->title }}" @selected(old('salary_band', $hrProfile?->salary_band ?? '') == $role->title)>
                    {{ $role->title }} ({{ $role->salary_band_min ? '$'.number_format($role->salary_band_min,2) : 'N/A' }} - {{ $role->salary_band_max ? '$'.number_format($role->salary_band_max,2) : 'N/A' }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold" for="status">Status</label>
        <select name="status" id="status" class="form-select">
            @foreach(['Active','On Leave','Inactive'] as $status)
                <option @selected(old('status', $hrProfile?->status ?? ($employee->is_active ? 'Active' : 'Inactive')) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold" for="emergency_contact">Emergency Contact</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
            <input name="emergency_contact" id="emergency_contact" class="form-control" value="{{ old('emergency_contact', $hrProfile?->emergency_contact ?? '') }}" placeholder="Emergency contact name & phone">
        </div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold" for="notes">Notes / Remarks</label>
        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes about employee duties, schedule, or qualifications...">{{ old('notes', $hrProfile?->notes ?? '') }}</textarea>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deptSelect = document.getElementById('department_select');
        const customWrapper = document.getElementById('custom_department_wrapper');
        const customInput = document.getElementById('custom_department_input');
        const hiddenDeptId = document.getElementById('department_id_hidden');
        const form = deptSelect ? deptSelect.closest('form') : null;

        function syncDepartmentSelection() {
            if (!deptSelect) return;
            const selectedOpt = deptSelect.options[deptSelect.selectedIndex];
            const deptId = selectedOpt ? selectedOpt.getAttribute('data-department-id') : '';

            if (hiddenDeptId) {
                hiddenDeptId.value = deptId || '';
            }

            if (deptSelect.value === '__custom__') {
                customWrapper.classList.remove('d-none');
                if (customInput) customInput.focus();
            } else {
                customWrapper.classList.add('d-none');
            }
        }

        if (deptSelect) {
            deptSelect.addEventListener('change', syncDepartmentSelection);
        }

        if (form && customInput && deptSelect) {
            form.addEventListener('submit', function () {
                if (deptSelect.value === '__custom__') {
                    const customVal = (customInput.value || '').trim();
                    if (customVal) {
                        let opt = Array.from(deptSelect.options).find(o => o.value === customVal);
                        if (!opt) {
                            opt = new Option(customVal, customVal, true, true);
                            deptSelect.add(opt);
                        }
                        deptSelect.value = customVal;
                    }
                }
            });
        }

        // Global helper for sidebar or external selectors
        window.selectStaffDepartment = function (deptName, deptId) {
            if (!deptSelect) return;

            let found = false;
            for (let i = 0; i < deptSelect.options.length; i++) {
                const opt = deptSelect.options[i];
                if ((deptId && opt.getAttribute('data-department-id') == deptId) || 
                    (deptName && opt.value.toLowerCase() === deptName.toLowerCase())) {
                    deptSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }

            if (!found && deptName) {
                const newOpt = new Option(deptName, deptName, true, true);
                if (deptId) newOpt.setAttribute('data-department-id', deptId);
                deptSelect.add(newOpt);
                deptSelect.value = deptName;
            }

            syncDepartmentSelection();

            // Visual feedback
            deptSelect.classList.add('border-primary', 'shadow-sm');
            setTimeout(() => {
                deptSelect.classList.remove('border-primary', 'shadow-sm');
            }, 1800);

            deptSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        };
    });
</script>
