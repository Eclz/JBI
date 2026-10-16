@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="mb-1 fw-bold text-dark">Add Employee</h2><p class="text-muted mb-0">Create a core HR employee record.</p></div>
        <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary">Back to staff</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('human-resources.staff.store') }}">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label" for="user_id">Linked user</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">— No linked user account —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>
                                {{ $user->full_name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="employee_number">Employee number</label>
                    <div class="input-group">
                        <input name="employee_number" id="employee_number" required class="form-control" value="{{ old('employee_number') }}">
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('employee_number').value = 'EMP-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 9999) + 1).padStart(4, '0')">Generate</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="job_title">Job title</label>
                    <input name="job_title" id="job_title" required class="form-control" value="{{ old('job_title') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="department">Department</label>
                    <input name="department" id="department" class="form-control" value="{{ old('department') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="employment_type">Employment type</label>
                    <select name="employment_type" id="employment_type" class="form-select">
                        @foreach(['Full-time','Part-time','Contract','Intern'] as $type)
                            <option @selected(old('employment_type', 'Full-time') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="salary_band">Salary band</label>
                    <input name="salary_band" id="salary_band" class="form-control" value="{{ old('salary_band') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        @foreach(['Active','On Leave','Inactive'] as $status)
                            <option @selected(old('status', 'Active') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_contact">Emergency contact</label>
                    <input name="emergency_contact" id="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary">Save employee</button>
        </form>
    </div></div>
</div>
@endsection
