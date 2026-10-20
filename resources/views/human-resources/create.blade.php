@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Add Employee</h2>
            <p class="text-muted mb-0">Create a core HR employee record and assign departmental roles.</p>
        </div>
        <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to staff
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        <form method="POST" action="{{ route('human-resources.staff.store') }}" id="staffCreateForm">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="user_id">Linked user</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">— No linked user account —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>
                                    {{ $user->full_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="employee_number">Employee number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-card-heading"></i></span>
                        <input name="employee_number" id="employee_number" required class="form-control" value="{{ old('employee_number') }}">
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('employee_number').value = 'EMP-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9999) + 1).padStart(4, '0')">
                            <i class="bi bi-magic me-1"></i>Generate
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="job_title">Job title <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-briefcase"></i></span>
                        <input name="job_title" id="job_title" required class="form-control" value="{{ old('job_title') }}" placeholder="e.g. Senior Lecturer, HR Officer">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="create_department_select">
                        <i class="bi bi-building text-primary me-1"></i>Department
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-diagram-3"></i></span>
                        <select name="department" id="create_department_select" class="form-select @error('department') is-invalid @enderror">
                            <option value="" data-department-id="">— Select Department —</option>

                            @if(isset($departments) && $departments->isNotEmpty())
                                <optgroup label="Academic Departments">
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" 
                                                data-department-id="{{ $dept->id }}"
                                                @selected(old('department') === $dept->name || (string)old('department_id') === (string)$dept->id)>
                                            {{ $dept->name }} {{ $dept->code ? '('.$dept->code.')' : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(isset($adminDepartments) && count($adminDepartments) > 0)
                                <optgroup label="Administrative & Operations">
                                    @foreach($adminDepartments as $adminDept)
                                        <option value="{{ $adminDept }}" 
                                                data-department-id=""
                                                @selected(old('department') === $adminDept)>
                                            {{ $adminDept }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            <option value="__custom__" data-department-id="">+ Enter Other / Custom Department...</option>
                        </select>
                    </div>
                    <input type="hidden" name="department_id" id="create_department_id_hidden" value="{{ old('department_id') }}">

                    <div id="create_custom_department_wrapper" class="mt-2 d-none">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-pencil-square"></i></span>
                            <input type="text" id="create_custom_department_input" class="form-control" placeholder="Specify custom department name...">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="employment_type">Employment type</label>
                    <select name="employment_type" id="employment_type" class="form-select">
                        @foreach(['Full-time','Part-time','Contract','Intern'] as $type)
                            <option @selected(old('employment_type', 'Full-time') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="salary_band">Salary band (Job Role)</label>
                    <select name="salary_band" id="salary_band" class="form-select">
                        <option value="">Select Band/Role</option>
                        @if(isset($jobRoles))
                            @foreach($jobRoles as $role)
                                <option value="{{ $role->title }}" @selected(old('salary_band') == $role->title)>
                                    {{ $role->title }} ({{ $role->salary_band_min ? '$'.number_format($role->salary_band_min,2) : 'N/A' }} - {{ $role->salary_band_max ? '$'.number_format($role->salary_band_max,2) : 'N/A' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        @foreach(['Active','On Leave','Inactive'] as $status)
                            <option @selected(old('status', 'Active') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="emergency_contact">Emergency contact</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                        <input name="emergency_contact" id="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}" placeholder="Emergency contact name & phone">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes about employee duties or role...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Save Employee Record
                </button>
            </div>
        </form>
    </div></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deptSelect = document.getElementById('create_department_select');
        const customWrapper = document.getElementById('create_custom_department_wrapper');
        const customInput = document.getElementById('create_custom_department_input');
        const hiddenDeptId = document.getElementById('create_department_id_hidden');
        const form = document.getElementById('staffCreateForm');

        if (deptSelect) {
            deptSelect.addEventListener('change', function () {
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
            });
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
    });
</script>
@endsection
