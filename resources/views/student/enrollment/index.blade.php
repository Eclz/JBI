@extends('layouts.app')

@section('title', 'Semester Enrollment & Course Registration Wizard')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-journal-check text-primary me-2"></i>SEMESTER ENROLLMENT & COURSE REGISTRATION WIZARD
            </h5>
            <p class="text-muted small mb-0">Register for academic year, semester, and select courses (Normal, Retake, or Missed Paper)</p>
        </div>
        <a href="{{ route('student.my-programme') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to My Programme
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-4" id="enrollmentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2 fw-bold me-2" id="wizard-tab" data-bs-toggle="pill" data-bs-target="#wizard-pane" type="button" role="tab">
                <i class="bi bi-magic me-2"></i>Enrollment Wizard & Course Selection
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2 fw-bold" id="current-tab" data-bs-toggle="pill" data-bs-target="#current-pane" type="button" role="tab">
                <i class="bi bi-list-check me-2"></i>My Currently Enrolled Courses ({{ $currentEnrollments->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="enrollmentTabsContent">
        <!-- TAB 1: ENROLLMENT WIZARD -->
        <div class="tab-pane fade show active" id="wizard-pane" role="tabpanel">
            <form action="{{ route('student.enrollment.store') }}" method="POST" id="enrollmentWizardForm">
                @csrf

                <!-- Wizard Progress Bar Header -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 bg-light rounded">
                        <div class="row text-center g-2 small fw-bold">
                            <div class="col-4">
                                <span class="badge bg-primary w-100 py-2 text-uppercase"><i class="bi bi-1-circle me-1"></i>Step 1: Academic Period</span>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-primary w-100 py-2 text-uppercase"><i class="bi bi-2-circle me-1"></i>Step 2: Course Selection</span>
                            </div>
                            <div class="col-4">
                                <span class="badge bg-primary w-100 py-2 text-uppercase"><i class="bi bi-3-circle me-1"></i>Step 3: Review & Invoices</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 1: Academic Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom border-primary border-2">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i>STEP 1: SELECT ACADEMIC YEAR & SEMESTER</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" class="form-select" required>
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->name ?? $ay->year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Year of Study <span class="text-danger">*</span></label>
                                <select name="year_of_study" class="form-select" required>
                                    @for($y = 1; $y <= 4; $y++)
                                        <option value="{{ $y }}" {{ ($studentProfile?->year_of_study ?? 1) == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Current Semester <span class="text-danger">*</span></label>
                                <select name="current_semester" class="form-select" required>
                                    <option value="1" {{ ($studentProfile?->current_semester ?? 1) == 1 ? 'selected' : '' }}>Semester I</option>
                                    <option value="2" {{ ($studentProfile?->current_semester ?? 1) == 2 ? 'selected' : '' }}>Semester II</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Course Selection & Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-book me-2"></i>STEP 2: SELECT COURSES & ENROLLMENT TYPE</h6>
                        <small class="text-muted">Choose course status: Normal (First Time), Retake, or Missed Paper</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">SELECT</th>
                                        <th>CODE & TITLE</th>
                                        <th>CREDITS</th>
                                        <th>LECTURER</th>
                                        <th style="width: 260px;">ENROLLMENT STATUS / TYPE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($availableCourses as $c)
                                        @php
                                            $isEnrolled = $currentEnrollments->pluck('course_id')->contains($c->id);
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input course-checkbox" type="checkbox" name="course_ids[]" value="{{ $c->id }}" id="courseCheck{{ $c->id }}" {{ $isEnrolled ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <label for="courseCheck{{ $c->id }}" class="fw-bold text-primary mb-0 d-block cursor-pointer">{{ $c->code }}</label>
                                                <small class="text-dark fw-semibold">{{ $c->title }}</small>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $c->credits ?? 4 }} CU</span></td>
                                            <td>{{ $c->faculty?->full_name ?? 'Assigned Lecturer' }}</td>
                                            <td>
                                                <select name="enrollment_types[{{ $c->id }}]" class="form-select form-select-sm">
                                                    <option value="normal" selected>Normal (First Time - Free)</option>
                                                    <option value="retake">Retake Course (UGX 150,000 Invoice)</option>
                                                    <option value="missed_paper">Missed Paper Exam (UGX 100,000 Invoice)</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No available courses found for your programme.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Summary & Confirmation -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom border-primary border-2">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-check me-2"></i>STEP 3: CONFIRM ENROLLMENT</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Retake or Missed Paper selections will automatically generate an itemized invoice attached to your student billing account.
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirmTermsCheck" required>
                            <label class="form-check-label small fw-semibold" for="confirmTermsCheck">
                                I confirm that I am registering for the selected academic semester and courses. I agree to university guidelines.
                            </label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i>COMPLETE ENROLLMENT & REGISTER
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 2: CURRENT ENROLLED COURSES -->
        <div class="tab-pane fade" id="current-pane" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom border-primary border-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-journal-text me-2"></i>REGISTERED COURSES LIST</h6>
                    <span class="badge bg-primary px-3 py-2">TOTAL ENROLLED: {{ $currentEnrollments->count() }} COURSES</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th>COURSE CODE & TITLE</th>
                                    <th>CREDITS</th>
                                    <th>LECTURER</th>
                                    <th>ENROLLMENT TYPE</th>
                                    <th>ENROLLMENT DATE</th>
                                    <th class="text-end">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentEnrollments as $item)
                                    @php
                                        $c = $item->course;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-primary">{{ $c?->code }}</span><br>
                                            <span class="fw-semibold text-dark">{{ $c?->title }}</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $c?->credits ?? 4 }} CU</span></td>
                                        <td>{{ $c?->faculty?->full_name ?? 'Assigned Lecturer' }}</td>
                                        <td>
                                            @if($item->enrollment_type === 'retake')
                                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-arrow-repeat me-1"></i>RETAKE</span>
                                            @elseif($item->enrollment_type === 'missed_paper')
                                                <span class="badge bg-danger px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>MISSED PAPER</span>
                                            @else
                                                <span class="badge bg-primary px-2 py-1">NORMAL</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->enrollment_date ? $item->enrollment_date->format('M d, Y') : $item->created_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('student.enrollment.unenroll', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to unenroll from {{ $c->code }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-dash-circle me-1"></i>Unenroll
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">You are not enrolled in any courses for this semester yet. Use the Enrollment Wizard above.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
