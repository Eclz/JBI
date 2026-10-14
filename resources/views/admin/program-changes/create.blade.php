@extends('layouts.app')

@section('title', 'Initiate Program Change')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Initiate Program Change</h1>
            <p class="text-muted mb-0">Create a program change request on behalf of a student.</p>
        </div>
        <div>
            <a href="{{ route('admin.program-changes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Requests
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.program-changes.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Select Student</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose a student...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->studentProfile->admission_number ?? 'No ID' }}) - Current: {{ $student->studentProfile->program ?? 'None' }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Program</label>
                        <select name="requested_program_id" class="form-select" required>
                            <option value="">Choose the new program...</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }} ({{ $program->department->name ?? 'No Dept' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Reason for Change</label>
                        <textarea name="reason" rows="3" class="form-control" required placeholder="Provide a reason for the administrative program change..."></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch fs-5">
                            <input class="form-check-input" type="checkbox" name="auto_approve" id="auto_approve" value="1" checked>
                            <label class="form-check-label fw-semibold fs-6" for="auto_approve">Automatically approve this request</label>
                        </div>
                        <small class="text-muted">If checked, the program change will be executed immediately.</small>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Submit Request</button>
                    <a href="{{ route('admin.program-changes.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
