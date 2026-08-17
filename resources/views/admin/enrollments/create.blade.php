@extends('layouts.app')

@section('title', 'Create New Student Course Enrollment')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0 text-primary fw-bold"><i class="bi bi-journal-plus me-2"></i>Admin Student Course Enrollment</h2>
                    <p class="text-muted mb-0">Assign courses to students while monitoring real-time seat slot capacity.</p>
                </div>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Enrollments
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Enrollment Form</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.enrollments.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="student_id" class="form-label fw-bold">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="student_id" class="form-select form-select-lg @error('student_id') is-invalid @enderror" required>
                                <option value="">-- Choose Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="course_id" class="form-label fw-bold">Select Course (Available Slots Displayed) <span class="text-danger">*</span></label>
                            <select name="course_id" id="course_id" class="form-select form-select-lg @error('course_id') is-invalid @enderror" required>
                                <option value="">-- Choose Course --</option>
                                @foreach($courses as $course)
                                    @php
                                        $slots = $course->available_slots;
                                        $max = $course->max_capacity;
                                        $isFull = $slots <= 0;
                                    @endphp
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                        {{ $course->course_code ?? $course->code }} - {{ $course->name }} [{{ $slots }} / {{ $max }} SLOTS AVAILABLE] {{ $isFull ? '(FULL CAPACITY)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="enrollment_date" class="form-label fw-bold">Enrollment Date <span class="text-danger">*</span></label>
                                <input type="date" name="enrollment_date" id="enrollment_date" class="form-control" value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="enrolled" {{ old('status', 'enrolled') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="bi bi-person-check me-1"></i>Create Enrollment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
