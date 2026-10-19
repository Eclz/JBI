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
                    <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.show', $enrollment) }}">#{{ $enrollment->id }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i>Enrollment Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label fw-bold">Student <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select select2 @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id', $enrollment->user_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->student_number ?? $student->student_id ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <select name="course_id" id="course_id" class="form-select select2 @error('course_id') is-invalid @enderror" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $enrollment->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_code ?? $course->code }} - {{ $course->title ?? $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="enrollment_date" class="form-label fw-bold">Enrollment Date <span class="text-danger">*</span></label>
                                <input type="date" name="enrollment_date" id="enrollment_date"
                                       class="form-control @error('enrollment_date') is-invalid @enderror"
                                       value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}" required>
                                @error('enrollment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="enrollment_type" class="form-label fw-bold">Course Unit Nature / Type <span class="text-danger">*</span></label>
                                <select name="enrollment_type" id="enrollment_type" class="form-select @error('enrollment_type') is-invalid @enderror" required>
                                    <option value="normal" {{ old('enrollment_type', $enrollment->enrollment_type ?? 'normal') == 'normal' ? 'selected' : '' }}>Normal (First Attempt)</option>
                                    <option value="retake" {{ old('enrollment_type', $enrollment->enrollment_type) == 'retake' ? 'selected' : '' }}>Retake of Course Unit</option>
                                    <option value="missed_paper" {{ old('enrollment_type', $enrollment->enrollment_type) == 'missed_paper' ? 'selected' : '' }}>Missed Paper Exam</option>
                                    <option value="redo" {{ old('enrollment_type', $enrollment->enrollment_type) == 'redo' ? 'selected' : '' }}>Redo of Course Unit</option>
                                </select>
                                <small class="text-muted">Normal, retake, missed paper, or redo.</small>
                                @error('enrollment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
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

                        <!-- Academic Registrar Automated Grading Block -->
                        <div class="card border border-primary-subtle bg-light mb-4">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0 fw-bold text-primary">
                                        <i class="fas fa-calculator me-1"></i> Academic Registrar Automated Grading
                                    </h6>
                                    <small class="text-muted">Calculated automatically from student marks and exam results</small>
                                </div>
                                @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'academic_registrar']))
                                    <a href="{{ route('admin.registrar.grading-criteria') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-sliders-h me-1"></i> Grading Criteria
                                    </a>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-white rounded border text-center h-100 shadow-sm">
                                            <span class="text-muted small d-block mb-1 fw-bold">Final Grade (%)</span>
                                            <div class="h3 fw-bold text-dark mb-1">
                                                @if(isset($calculatedResults['final_grade']) && $calculatedResults['final_grade'] !== null)
                                                    {{ number_format($calculatedResults['final_grade'], 1) }}%
                                                @elseif($enrollment->final_grade !== null)
                                                    {{ number_format($enrollment->final_grade, 1) }}%
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                            <span class="badge bg-light text-secondary border">
                                                {{ $calculatedResults['assessment_count'] ?? 0 }} Assessments Found
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-white rounded border text-center h-100 shadow-sm">
                                            <span class="text-muted small d-block mb-1 fw-bold">Letter Grade</span>
                                            <div class="h3 fw-bold text-primary mb-1">
                                                {{ $calculatedResults['letter_grade'] ?? $enrollment->letter_grade ?? 'N/A' }}
                                            </div>
                                            <span class="badge bg-primary-subtle text-primary border">
                                                Official Scale
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="p-3 bg-white rounded border text-center h-100 shadow-sm">
                                            <span class="text-muted small d-block mb-1 fw-bold">Grade Points</span>
                                            <div class="h3 fw-bold text-success mb-1">
                                                @if(isset($calculatedResults['grade_points']) && $calculatedResults['grade_points'] !== null)
                                                    {{ number_format($calculatedResults['grade_points'], 2) }}
                                                @elseif($enrollment->grade_points !== null)
                                                    {{ number_format($enrollment->grade_points, 2) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                            <span class="badge bg-success-subtle text-success border">
                                                GPA Weight
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($calculatedResults['breakdown']))
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-link text-decoration-none p-0 text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#resultsBreakdown" aria-expanded="false" aria-controls="resultsBreakdown">
                                            <i class="fas fa-chevron-down me-1"></i> View Results Breakdown ({{ count($calculatedResults['breakdown']) }} items)
                                        </button>
                                        <div class="collapse mt-2" id="resultsBreakdown">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered bg-white mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Assessment / Examination</th>
                                                            <th>Type</th>
                                                            <th class="text-end">Score / Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($calculatedResults['breakdown'] as $item)
                                                            <tr>
                                                                <td>{{ $item['title'] }}</td>
                                                                <td><span class="badge bg-secondary">{{ ucfirst($item['type']) }}</span></td>
                                                                <td class="text-end fw-bold">{{ number_format($item['percentage'], 1) }}%</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="alert alert-info py-2 px-3 mt-3 mb-0 small d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2 fs-5"></i>
                                    <div>
                                        <strong>Automatic Grading:</strong> Grade points, final grade percentage, and letter grade are automatically computed from student results according to the grading criteria set by the Academic Registrar. Manual editing is restricted to uphold academic integrity.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completion Date (Automatically Semester End Date) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Completion Date <span class="badge bg-info text-white ms-1">Auto-Synced</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-check"></i></span>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ $semesterEndDate ? \Carbon\Carbon::parse($semesterEndDate)->format('M d, Y') : ($enrollment->completion_date ? $enrollment->completion_date->format('M d, Y') : 'Semester closure date (applied on completion)') }}" 
                                       readonly disabled>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-lock text-muted me-1"></i>
                                When the status is set to <strong>Completed</strong>, the completion date is automatically set to the semester closure date 
                                @if($semesterEndDate)
                                    (<strong>{{ \Carbon\Carbon::parse($semesterEndDate)->format('F d, Y') }}</strong>).
                                @else
                                    for this course's semester.
                                @endif
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Optional remarks regarding this enrollment...">{{ old('notes', $enrollment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Academic Registrar Grading Criteria -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="fas fa-graduation-cap me-1 text-primary"></i> Registrar Grading Criteria
                    </h5>
                    @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'academic_registrar']))
                        <a href="{{ route('admin.registrar.grading-criteria') }}" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                            Edit Criteria
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0 text-center" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Score Range</th>
                                    <th>Letter</th>
                                    <th>Grade Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gradingCriteria as $tier)
                                    @php
                                        $tierMin = $tier['min'] ?? $tier['min_score'] ?? 0;
                                        $tierMax = $tier['max'] ?? $tier['max_score'] ?? 100;
                                        $tierLetter = $tier['letter'] ?? $tier['letter_grade'] ?? '';
                                        $tierGp = $tier['grade_points'] ?? $tier['grade_point'] ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $tierMin }}% - {{ $tierMax }}%</td>
                                        <td><span class="badge bg-primary">{{ $tierLetter }}</span></td>
                                        <td class="fw-bold">{{ number_format((float)$tierGp, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white small text-muted">
                    <i class="fas fa-shield-alt text-primary me-1"></i> Configured by Academic Registrar.
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="fas fa-question-circle me-1 text-muted"></i> Enrollment Policies
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-1 fw-bold small">Status Rules:</p>
                    <ul class="small mb-3 ps-3 text-muted">
                        <li><strong>Pending:</strong> Awaiting administrative approval.</li>
                        <li><strong>Enrolled:</strong> Student actively participating.</li>
                        <li><strong>Completed:</strong> Course finished; locks completion date to semester end.</li>
                        <li><strong>Dropped:</strong> Withdrawn; no final completion date.</li>
                        <li><strong>Failed:</strong> Completed without passing mark.</li>
                    </ul>

                    <p class="mb-1 fw-bold small">Automated Calculations:</p>
                    <p class="small text-muted mb-0">
                        Final score, letter grade, and grade points are calculated directly from registered grades, assignment submissions, and exam attempts.
                    </p>
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
            $('#user_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            $('#course_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>
@endpush
