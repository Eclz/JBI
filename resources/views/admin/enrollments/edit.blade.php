@extends('layouts.app')

@section('title', 'Edit Enrollment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">Edit Enrollment</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.index') }}">Enrollments</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Enrollment Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Student <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id', $enrollment->user_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->student_id ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                                <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $enrollment->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_code }} - {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="enrollment_date" class="form-label">Enrollment Date <span class="text-danger">*</span></label>
                                <input type="date" name="enrollment_date" id="enrollment_date"
                                       class="form-control @error('enrollment_date') is-invalid @enderror"
                                       value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}" required>
                                @error('enrollment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $enrollment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="enrolled" {{ old('status', $enrollment->status) == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="completed" {{ old('status', $enrollment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="dropped" {{ old('status', $enrollment->status) == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                    <option value="failed" {{ old('status', $enrollment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="final_grade" class="form-label">Final Grade (%)</label>
                                <input type="number" name="final_grade" id="final_grade"
                                       class="form-control @error('final_grade') is-invalid @enderror"
                                       value="{{ old('final_grade', $enrollment->final_grade) }}"
                                       min="0" max="100" step="0.01">
                                @error('final_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="letter_grade" class="form-label">Letter Grade</label>
                                <select name="letter_grade" id="letter_grade" class="form-select @error('letter_grade') is-invalid @enderror">
                                    <option value="">Select Grade</option>
                                    <option value="A+" {{ old('letter_grade', $enrollment->letter_grade) == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A" {{ old('letter_grade', $enrollment->letter_grade) == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="A-" {{ old('letter_grade', $enrollment->letter_grade) == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('letter_grade', $enrollment->letter_grade) == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B" {{ old('letter_grade', $enrollment->letter_grade) == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="B-" {{ old('letter_grade', $enrollment->letter_grade) == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="C+" {{ old('letter_grade', $enrollment->letter_grade) == 'C+' ? 'selected' : '' }}>C+</option>
                                    <option value="C" {{ old('letter_grade', $enrollment->letter_grade) == 'C' ? 'selected' : '' }}>C</option>
                                    <option value="C-" {{ old('letter_grade', $enrollment->letter_grade) == 'C-' ? 'selected' : '' }}>C-</option>
                                    <option value="D+" {{ old('letter_grade', $enrollment->letter_grade) == 'D+' ? 'selected' : '' }}>D+</option>
                                    <option value="D" {{ old('letter_grade', $enrollment->letter_grade) == 'D' ? 'selected' : '' }}>D</option>
                                    <option value="F" {{ old('letter_grade', $enrollment->letter_grade) == 'F' ? 'selected' : '' }}>F</option>
                                </select>
                                @error('letter_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="grade_points" class="form-label">Grade Points</label>
                                <input type="number" name="grade_points" id="grade_points"
                                       class="form-control @error('grade_points') is-invalid @enderror"
                                       value="{{ old('grade_points', $enrollment->grade_points) }}"
                                       min="0" max="4" step="0.01">
                                @error('grade_points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="completion_date" class="form-label">Completion Date</label>
                            <input type="date" name="completion_date" id="completion_date"
                                   class="form-control @error('completion_date') is-invalid @enderror"
                                   value="{{ old('completion_date', $enrollment->completion_date?->format('Y-m-d')) }}">
                            @error('completion_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Set this date when the enrollment is completed.</small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $enrollment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Enrollment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Help</h5>
                </div>
                <div class="card-body">
                    <p><strong>Status Options:</strong></p>
                    <ul>
                        <li><strong>Pending:</strong> Awaiting approval</li>
                        <li><strong>Enrolled:</strong> Currently active</li>
                        <li><strong>Completed:</strong> Successfully finished</li>
                        <li><strong>Dropped:</strong> Withdrawn from course</li>
                        <li><strong>Failed:</strong> Did not meet requirements</li>
                    </ul>

                    <p class="mt-3"><strong>Grading:</strong></p>
                    <p class="small text-muted">Final grades and letter grades should be updated when the course is completed.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
