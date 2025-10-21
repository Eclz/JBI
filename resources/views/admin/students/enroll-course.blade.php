@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Enroll Student in Course</h5>
                        <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-light">
                            <i class="fa fa-arrow-left"></i> Back to Student
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Student Information -->
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                @if($student->profile_picture)
                                    <img src="{{ $student->profile_picture_url }}" alt="Profile Picture" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <img src="{{ $student->profile_picture_url }}" alt="Profile Picture" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h5>{{ $student->full_name }}</h5>
                                <p class="text-muted mb-1">
                                    <strong>Student ID:</strong> {{ $student->student_id ?? 'Not assigned' }}
                                </p>
                                <p class="text-muted mb-1">
                                    <strong>Department:</strong> {{ $student->studentProfile->department->name ?? 'Not assigned' }}
                                </p>
                                <p class="text-muted mb-0">
                                    <strong>Current Semester:</strong> {{ $student->studentProfile->current_semester ?? 'Not assigned' }}
                                </p>
                            </div>
                        </div>

                        <!-- Currently Enrolled Courses -->
                        @if($student->enrolledCourses && $student->enrolledCourses->count() > 0)
                        <div class="mb-4">
                            <h6>Currently Enrolled Courses</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Credits</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->enrolledCourses as $course)
                                            <tr>
                                                <td>{{ $course->course_code }}</td>
                                                <td>{{ $course->name }}</td>
                                                <td>{{ $course->credits }}</td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        {{ ucfirst(str_replace('_', ' ', $course->pivot->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Enrollment Form -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Enroll in New Course</h6>
                            </div>
                            <div class="card-body">
                                @if($availableCourses->count() > 0)
                                    <form action="{{ route('admin.students.enroll-course.store', $student->id) }}" method="POST">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group mb-3">
                                                    <label for="course_id" class="form-label">Select Course <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                                        <option value="">Choose a course...</option>
                                                        @foreach($availableCourses as $course)
                                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                                {{ $course->course_code }} - {{ $course->name }}
                                                                ({{ $course->credits }} credits)
                                                                @if($course->semester)
                                                                    - {{ $course->semester->name }}
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('course_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="enrollment_date" class="form-label">Enrollment Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('enrollment_date') is-invalid @enderror"
                                                           id="enrollment_date" name="enrollment_date"
                                                           value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                                                    @error('enrollment_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label">Enrollment Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="waitlisted" {{ old('status') == 'waitlisted' ? 'selected' : '' }}>Waitlisted</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="notes" class="form-label">Notes (Optional)</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                                      id="notes" name="notes" rows="3"
                                                      placeholder="Any additional notes about this enrollment...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-user-plus"></i> Enroll Student
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        No available courses for enrollment. The student is either already enrolled in all active courses or there are no active courses available.
                                    </div>
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to Student
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add course selection change handler to show course details
        const courseSelect = document.getElementById('course_id');
        if (courseSelect) {
            courseSelect.addEventListener('change', function() {
                // You can add logic here to show course details when selected
                console.log('Course selected:', this.value);
            });
        }
    });
</script>
@endsection
