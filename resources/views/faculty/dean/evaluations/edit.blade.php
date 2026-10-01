@extends('layouts.app')

@section('title', 'Edit Faculty Evaluation')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Faculty Evaluation</h5>
            <a href="{{ route('faculty.dean.evaluations.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('faculty.dean.evaluations.update', $evaluation) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="faculty_id" class="form-label">Faculty Member <span class="text-danger">*</span></label>
                        <select class="form-select @error('faculty_id') is-invalid @enderror" id="faculty_id" name="faculty_id" required>
                            <option value="">Select a Faculty Member</option>
                            @foreach($facultyMembers as $member)
                                <option value="{{ $member->id }}" {{ old('faculty_id', $evaluation->faculty_id) == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ $member->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('faculty_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', $evaluation->academic_year) }}" placeholder="e.g. 2026/2027" required>
                        @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="performance_score" class="form-label">Performance Score (0-100) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('performance_score') is-invalid @enderror" id="performance_score" name="performance_score" min="0" max="100" value="{{ old('performance_score', $evaluation->performance_score) }}" required>
                    @error('performance_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="comments" class="form-label">Evaluation Comments <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('comments') is-invalid @enderror" id="comments" name="comments" rows="6" required>{{ old('comments', $evaluation->comments) }}</textarea>
                    @error('comments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Update Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
