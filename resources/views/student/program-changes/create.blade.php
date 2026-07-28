@extends('layouts.app')

@section('title', 'Request Program Change')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="bi bi-arrow-repeat me-2"></i>Request Program Change
            </h1>
            <p class="text-muted mb-0">Submit a request to change your program</p>
        </div>
        <a href="{{ route('student.program-changes.index') }}" class="btn btn-outline-secondary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('student.program-changes.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Current Program</label>
                    <input type="text" class="form-control" value="{{ $profile->program->name ?? $profile->program ?? 'Not set' }}" disabled>
                </div>

                <div class="mb-3">
                    <label for="requested_program_id" class="form-label">Requested Program <span class="text-danger">*</span></label>
                    <select class="form-select @error('requested_program_id') is-invalid @enderror" id="requested_program_id" name="requested_program_id" required>
                        <option value="">Select Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('requested_program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif - {{ $program->department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('requested_program_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('student.program-changes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
