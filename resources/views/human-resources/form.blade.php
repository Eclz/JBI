<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Linked user</label>
        <input type="text" readonly class="form-control" value="{{ $employee->full_name }} ({{ $employee->email }})">
    </div>
    <div class="col-md-6">
        <label class="form-label">Employee number</label>
        <input name="employee_number" required class="form-control" value="{{ old('employee_number', $hrProfile?->employee_number ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Job title</label>
        <input name="job_title" required class="form-control" value="{{ old('job_title', $hrProfile?->job_title ?? $employee->role_name) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Department</label>
        <input name="department" class="form-control" value="{{ old('department', $hrProfile?->department ?? $employee->facultyProfile?->department?->name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Employment type</label>
        <select name="employment_type" class="form-select">
            @foreach(['Full-time','Part-time','Contract','Intern'] as $type)
                <option @selected(old('employment_type', $hrProfile?->employment_type ?? 'Full-time') === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Salary band</label>
        <input name="salary_band" class="form-control" value="{{ old('salary_band', $hrProfile?->salary_band ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['Active','On Leave','Inactive'] as $status)
                <option @selected(old('status', $hrProfile?->status ?? ($employee->is_active ? 'Active' : 'Inactive')) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Emergency contact</label>
        <input name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $hrProfile?->emergency_contact ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $hrProfile?->notes ?? '') }}</textarea>
    </div>
</div>
