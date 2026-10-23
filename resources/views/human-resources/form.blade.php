<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Linked user</label>
        <input type="text" readonly class="form-control bg-light" value="{{ $employee->full_name }} ({{ $employee->email }})">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Employee number <small class="text-muted fw-normal">(Optional - generated when onboarding completes)</small></label>
        <div class="input-group">
            <input name="employee_number" id="employee_number" class="form-control" placeholder="Pending onboarding completion" value="{{ old('employee_number', $hrProfile?->employee_number ?? '') }}">
            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('employee_number').value = 'EMP-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9999) + 1).padStart(4, '0')">Generate</button>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Job title</label>
        <input name="job_title" required class="form-control" value="{{ old('job_title', $hrProfile?->job_title ?? $employee->role_name) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="department_id">Department</label>
        <select name="department_id" id="department_id" class="form-select">
            <option value="">Select a department</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected((string) old('department_id', $selectedDepartmentId ?? '') === (string) $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Employment type</label>
        <select name="employment_type" class="form-select">
            @foreach(['Full-time','Part-time','Contract','Intern'] as $type)
                <option @selected(old('employment_type', $hrProfile?->employment_type ?? 'Full-time') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Salary band (Job Role)</label>
        <select name="salary_band" id="salary_band" class="form-select">
            <option value="">Select Band/Role</option>
            @foreach($jobRoles as $role)
                <option value="{{ $role->title }}" @selected(old('salary_band', $hrProfile?->salary_band ?? '') == $role->title)>
                    {{ $role->title }} ({{ $role->salary_band_min ? '$'.number_format($role->salary_band_min,2) : 'N/A' }} - {{ $role->salary_band_max ? '$'.number_format($role->salary_band_max,2) : 'N/A' }})
                </option>
            @endforeach
        </select>
        <small class="text-muted d-block mt-1">Attaching a salary band auto-completes the payroll task.</small>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            @foreach(['Active','On Leave','Inactive'] as $status)
                <option @selected(old('status', $hrProfile?->status ?? ($employee->is_active ? 'Active' : 'Inactive')) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="workspace">Workspace / office</label>
        <input id="workspace" name="workspace" class="form-control" maxlength="150" placeholder="e.g. Administration Block, Room 104" value="{{ old('workspace', $hrProfile?->workspace) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Emergency contact</label>
        <input name="emergency_contact" class="form-control" placeholder="e.g. Contact Person & Phone" value="{{ old('emergency_contact', $hrProfile?->emergency_contact ?? '') }}">
    </div>

    <!-- Bank Details Section -->
    <div class="col-12 mt-4">
        <div class="border rounded-3 p-3 bg-light">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-bank fs-5 text-primary me-2"></i>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Bank Details (Payroll Remittance)</h6>
                    <small class="text-muted">Entering bank details auto-completes the onboarding bank details task.</small>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium" for="bank_name">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="e.g. Stanbic Bank Uganda / Centenary Bank" value="{{ old('bank_name', $hrProfile?->bank_name ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium" for="bank_account_name">Account Name</label>
                    <input type="text" name="bank_account_name" id="bank_account_name" class="form-control" placeholder="e.g. {{ $employee->full_name }}" value="{{ old('bank_account_name', $hrProfile?->bank_account_name ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium" for="bank_account_number">Account Number</label>
                    <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" placeholder="e.g. 9030012345678" value="{{ old('bank_account_number', $hrProfile?->bank_account_number ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium" for="bank_branch">Branch Name / SWIFT</label>
                    <input type="text" name="bank_branch" id="bank_branch" class="form-control" placeholder="e.g. Main Branch, Kampala" value="{{ old('bank_branch', $hrProfile?->bank_branch ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3">
        <label class="form-label fw-semibold">Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Additional remarks">{{ old('notes', $hrProfile?->notes ?? '') }}</textarea>
    </div>
</div>
