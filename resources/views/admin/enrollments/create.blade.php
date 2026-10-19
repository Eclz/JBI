@extends('layouts.app')

@section('title', 'Create New Student Course Enrollment')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
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
                            <label for="course_id" class="form-label fw-bold">Select Course (Available Slots Displayed) <span class="text-danger">*</span></label>
                            <select name="course_id" id="course_id" class="form-select select2 @error('course_id') is-invalid @enderror" data-placeholder="-- Search or choose a course (by code or name) --" style="width: 100%;" required>
                                <option value="">-- Choose Course First --</option>
                                @foreach($courses as $course)
                                    @php
                                        $slots = $course->available_slots;
                                        $max = $course->max_capacity;
                                        $isFull = $slots <= 0;
                                        $facultyId = $course->getFacultyId();
                                        $facultyName = $course->getFacultyName() ?? 'General';
                                    @endphp
                                    <option value="{{ $course->id }}" 
                                            data-faculty-id="{{ $facultyId }}"
                                            data-faculty-name="{{ $facultyName }}"
                                            {{ (old('course_id') == $course->id || (isset($selectedCourse) && $selectedCourse->id == $course->id)) ? 'selected' : '' }} 
                                            {{ $isFull ? 'disabled' : '' }}>
                                        {{ $course->course_code ?? $course->code }} - {{ $course->name }} [Faculty: {{ $facultyName }}] [{{ $slots }} / {{ $max }} SLOTS AVAILABLE] {{ $isFull ? '(FULL CAPACITY)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="course-faculty-badge" class="mt-1 small text-primary" style="display: none;">
                                <i class="bi bi-building me-1"></i><span id="course-faculty-text"></span>
                            </div>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="student_id" class="form-label fw-bold">Select Student <span class="text-danger">*</span></label>
                            <div class="small text-muted mb-2">
                                <i class="bi bi-shield-check text-success me-1"></i>Students are strictly filtered to the faculty offering the selected course unit.
                            </div>
                            <select name="student_id" id="student_id" class="form-select select2 @error('student_id') is-invalid @enderror" data-placeholder="-- Search or choose a student (by name, student number, or email) --" style="width: 100%;" required>
                                <option value="">-- Choose Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" 
                                            data-faculty-id="{{ $student->getFacultyId() }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} [{{ $student->student_number ?? $student->student_id ?? 'N/A' }}] ({{ $student->email }}) - {{ $student->getFacultyName() ?? 'Faculty' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="student-filter-status" class="mt-1 small text-muted"></div>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="enrollment_date" class="form-label fw-bold">Enrollment Date <span class="text-danger">*</span></label>
                                <input type="date" name="enrollment_date" id="enrollment_date" class="form-control" value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="enrollment_type" class="form-label fw-bold">Course Unit Nature / Type <span class="text-danger">*</span></label>
                                <select name="enrollment_type" id="enrollment_type" class="form-select @error('enrollment_type') is-invalid @enderror" required>
                                    <option value="normal" {{ old('enrollment_type', 'normal') == 'normal' ? 'selected' : '' }}>Normal (First Attempt)</option>
                                    <option value="retake" {{ old('enrollment_type') == 'retake' ? 'selected' : '' }}>Retake of Course Unit</option>
                                    <option value="missed_paper" {{ old('enrollment_type') == 'missed_paper' ? 'selected' : '' }}>Missed Paper Exam</option>
                                    <option value="redo" {{ old('enrollment_type') == 'redo' ? 'selected' : '' }}>Redo of Course Unit</option>
                                </select>
                                <small class="text-muted">Indicate if student is taking normally, retaking, sitting missed paper, or redoing.</small>
                                @error('enrollment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="enrolled" {{ old('status', 'enrolled') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
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

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('#student_id').select2({
                theme: 'bootstrap-5',
                placeholder: $('#student_id').data('placeholder') || 'Search or select student...',
                allowClear: true,
                width: '100%'
            });

            $('#course_id').select2({
                theme: 'bootstrap-5',
                placeholder: $('#course_id').data('placeholder') || 'Search or select course...',
                allowClear: true,
                width: '100%'
            });
        }

        const facultyStudentsUrl = '{{ route("admin.enrollments.faculty-students") }}';
        let initialStudentId = '{{ old("student_id") }}';

        function filterStudentsForCourse(courseId, keepSelected = null) {
            if (!courseId) {
                $('#course-faculty-badge').hide();
                $('#student-filter-status').html('<span class="text-muted">Please select a course unit first to view eligible students.</span>');
                return;
            }

            const selectedOption = $('#course_id option:selected');
            const facultyName = selectedOption.data('faculty-name') || 'Course Faculty';
            
            $('#course-faculty-text').text('Course belongs to: ' + facultyName);
            $('#course-faculty-badge').fadeIn();
            $('#student-filter-status').html('<span class="text-primary"><i class="spinner-border spinner-border-sm me-1"></i>Loading students from ' + facultyName + '...</span>');

            $.ajax({
                url: facultyStudentsUrl,
                type: 'GET',
                data: { course_id: courseId },
                dataType: 'json',
                success: function(response) {
                    const students = response.students || [];
                    const currentSelected = keepSelected !== null ? keepSelected : $('#student_id').val();
                    
                    let optionsHtml = '<option value="">-- Choose Student (' + facultyName + ') --</option>';
                    let matchedSelected = false;

                    students.forEach(function(st) {
                        const isSel = (String(st.id) === String(currentSelected));
                        if (isSel) matchedSelected = true;
                        optionsHtml += '<option value="' + st.id + '" data-faculty-id="' + (st.faculty_id || '') + '" ' + (isSel ? 'selected' : '') + '>'
                            + st.name + ' [' + st.student_number + '] (' + st.email + ') - ' + (st.faculty_name || facultyName)
                            + '</option>';
                    });

                    $('#student_id').html(optionsHtml);
                    if ($.fn.select2) {
                        $('#student_id').trigger('change.select2');
                    }

                    if (students.length > 0) {
                        $('#student-filter-status').html('<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>' + students.length + ' eligible student(s) available in ' + facultyName + '.</span>');
                    } else {
                        $('#student-filter-status').html('<span class="text-warning fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i>No students found strictly enrolled under ' + facultyName + '.</span>');
                    }
                },
                error: function() {
                    $('#student-filter-status').html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Error loading faculty students. Please try again.</span>');
                }
            });
        }

        $('#course_id').on('change', function() {
            const courseId = $(this).val();
            filterStudentsForCourse(courseId);
        });

        // Trigger on page load if course is pre-selected
        if ($('#course_id').val()) {
            filterStudentsForCourse($('#course_id').val(), initialStudentId);
        }
    });
</script>
@endpush
