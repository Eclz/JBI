@extends('layouts.app')

@section('title', 'Admin Course Assignment')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0 text-primary fw-bold"><i class="bi bi-journal-plus me-2"></i>Admin Course Assignment & Seat Management</h2>
                    <p class="text-muted mb-0">Select and assign courses for this student while monitoring real-time course slot availability.</p>
                </div>
                <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Student Profile
                </a>
            </div>

            <!-- Student Profile Header Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 60px; height: 60px;">
                            {{ substr($student->first_name ?? 'S', 0, 1) }}{{ substr($student->last_name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h4>
                            <div class="d-flex align-items-center gap-3 small text-muted flex-wrap">
                                <span><i class="bi bi-envelope me-1"></i>{{ $student->email }}</span>
                                <span><i class="bi bi-person-badge me-1"></i>Student ID: <strong>{{ $student->studentProfile->student_id ?? 'ADM-' . $student->id }}</strong></span>
                                <span><i class="bi bi-journal-bookmark me-1"></i>Programme: <strong>{{ $student->studentProfile->program ?? 'Software Engineering' }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Enrollment Error:</strong> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Currently Enrolled Courses Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-journal-check me-2 text-primary"></i>Currently Enrolled Courses</h5>
                    <span class="badge bg-primary px-3 py-1.5 fw-bold">{{ $student->enrolledCourses ? $student->enrolledCourses->count() : 0 }} Courses</span>
                </div>
                <div class="card-body p-0">
                    @if($student->enrolledCourses && $student->enrolledCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Code</th>
                                        <th>Course Title</th>
                                        <th>Credits</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->enrolledCourses as $course)
                                        <tr>
                                            <td class="ps-3"><span class="badge bg-secondary font-monospace">{{ $course->course_code ?? $course->code }}</span></td>
                                            <td class="fw-bold text-dark">{{ $course->name }}</td>
                                            <td>{{ $course->credits }} Units</td>
                                            <td><span class="badge bg-success px-2.5 py-1.5 text-uppercase">Enrolled</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                            Student is not currently enrolled in any courses for this semester.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Course Assignment Form & Live Slots Table -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-ui-checks me-2"></i>Select & Assign Course (With Available Slots)</h5>
                </div>
                <div class="card-body p-4">
                    @if($availableCourses->count() > 0)
                        <form action="{{ route('admin.students.enroll-course.store', $student->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-7">
                                    <label for="course_id" class="form-label fw-bold">Select Active Course & Check Available Slots <span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-select select2 form-select-lg @error('course_id') is-invalid @enderror" required>
                                        <option value="">-- Choose Course (Slots Available) --</option>
                                        @foreach($availableCourses as $course)
                                            @php
                                                $slots = $course->available_slots;
                                                $max = $course->max_capacity;
                                                $isFull = $slots <= 0;
                                            @endphp
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                                {{ $course->course_code ?? $course->code }} - {{ $course->name }} [{{ $slots }} / {{ $max }} SLOTS AVAILABLE] {{ $isFull ? '(FULL)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="enrollment_date" class="form-label fw-bold">Enrollment Date</label>
                                    <input type="date" name="enrollment_date" id="enrollment_date" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                        <i class="bi bi-person-plus me-1"></i>Enroll
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-4">

                        <!-- Interactive Course Slot Overview Table -->
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-stars me-2 text-primary"></i>All Available Courses & Live Capacity Tracker</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Code</th>
                                        <th>Course Name</th>
                                        <th>Instructor</th>
                                        <th>Credits</th>
                                        <th>Slots Available</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableCourses as $course)
                                        @php
                                            $slots = $course->available_slots;
                                            $max = $course->max_capacity;
                                            $isFull = $slots <= 0;
                                        @endphp
                                        <tr>
                                            <td class="ps-3"><span class="badge bg-secondary font-monospace">{{ $course->course_code ?? $course->code }}</span></td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $course->name }}</div>
                                                <small class="text-muted">{{ $course->department->name ?? 'Department' }}</small>
                                            </td>
                                            <td class="small">{{ $course->instructor ? $course->instructor->first_name . ' ' . $course->instructor->last_name : 'Faculty' }}</td>
                                            <td>{{ $course->credits }} Units</td>
                                            <td>
                                                @if($slots > 5)
                                                    <span class="badge bg-success px-3 py-2 fs-6 fw-bold"><i class="bi bi-check-circle me-1"></i>{{ $slots }} / {{ $max }} Slots Open</span>
                                                @elseif($slots > 0)
                                                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Only {{ $slots }} Slots Left</span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-2 fs-6 fw-bold"><i class="bi bi-x-circle me-1"></i>FULL (0 / {{ $max }})</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                @if(!$isFull)
                                                    <form action="{{ route('admin.students.enroll-course.store', $student->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                                        <button type="submit" class="btn btn-sm btn-primary fw-bold">
                                                            <i class="bi bi-plus-circle me-1"></i>Assign Course
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>Full Capacity</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info border-0 shadow-sm rounded-3">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>No courses available for assignment. The student is already enrolled in all active courses.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
