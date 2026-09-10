@extends('layouts.app')

@section('title', $course->name . ' - Course Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-dark fw-bold">{{ $course->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('faculty.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('faculty.courses.index') }}">My Courses</a></li>
                            <li class="breadcrumb-item active">{{ $course->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('faculty.courses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>
                </div>
            </div>

            <ul class="nav nav-tabs mb-4 border-bottom-0" id="courseTab" role="tablist" style="background: white; border-radius: 8px; padding: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="bi bi-info-circle me-2"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">
                        <i class="bi bi-people me-2"></i> Students
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab">
                        <i class="bi bi-file-earmark-text me-2"></i> Assignments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button" role="tab">
                        <i class="bi bi-clipboard-check me-2"></i> Exams
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" type="button" role="tab">
                        <i class="bi bi-question-circle me-2"></i> Quizzes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                        <i class="bi bi-folder me-2"></i> Materials
                    </button>
                </li>
            </ul>

            <style>
                .nav-tabs .nav-link {
                    transition: all 0.3s ease;
                    color: #6B7280;
                    border: none;
                    font-weight: 500;
                    padding: 12px 20px;
                }
                .nav-tabs .nav-link:hover {
                    background-color: #F3F4F6;
                    border-radius: 6px;
                }
                .nav-tabs .nav-link.active {
                    color: #4F46E5 !important;
                    background-color: #EEF2FF;
                    border-radius: 6px;
                }
            </style>

            <div class="tab-content" id="courseTabContent">
                {{-- Overview Tab --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-3">{{ $course->name }}</h5>
                                    <p class="text-muted mb-2"><strong>Code:</strong> {{ $course->code }}</p>
                                    <p class="text-muted mb-2"><strong>Credits:</strong> {{ $course->credits }}</p>
                                    <p class="text-muted mb-2"><strong>Department:</strong> {{ $course->department->name ?? 'N/A' }}</p>
                                    <p class="text-muted mb-2"><strong>Semester:</strong> {{ $course->semester->name ?? 'N/A' }}</p>
                                    <p class="mt-3">{{ $course->description }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Quick Stats</h6>
                                            <div class="mb-2">
                                                <small class="text-muted">Enrolled Students</small>
                                                <div class="h4 mb-0">{{ $course->enrollments->where('status', 'enrolled')->count() }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Assignments</small>
                                                <div class="h4 mb-0">{{ $course->assignments->count() }}</div>
                                            </div>
                                            <div>
                                                <small class="text-muted">Materials</small>
                                                <div class="h4 mb-0">{{ $course->materials->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Students Tab --}}
                <div class="tab-pane fade" id="students" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Enrolled Students</h5>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                                <i class="bi bi-plus-circle me-1"></i> Enroll Student
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Enrolled Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($course->enrollments->where('status', 'enrolled') as $enrollment)
                                        <tr>
                                            <td>
                                                @php
                                                    $studentNum = $enrollment->student?->student_id 
                                                        ?: ($enrollment->student?->studentProfile?->admission_number 
                                                        ?: ($enrollment->student?->studentProfile?->student_id ?: null));
                                                @endphp
                                                @if($studentNum)
                                                    <span class="badge bg-light text-dark border font-monospace">{{ $studentNum }}</span>
                                                @else
                                                    <span class="text-muted small">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $enrollment->student->name }}</td>
                                            <td>{{ $enrollment->student->email }}</td>
                                            <td><span class="badge bg-success">{{ ucfirst($enrollment->status) }}</span></td>
                                            <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-primary">View Profile</button>
                                                    <form action="{{ route('faculty.courses.drop-student', [$course, $enrollment]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to drop this student from this course?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Drop</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No students enrolled yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assignments Tab --}}
                <div class="tab-pane fade" id="assignments" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Assignments</h5>
                            <a href="{{ route('faculty.assignments.create', $course) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Assignment
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Due Date</th>
                                            <th>Total Points</th>
                                            <th>Submissions</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($course->assignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->title }}</td>
                                            <td>{{ $assignment->due_date->format('M d, Y H:i') }}</td>
                                            <td>{{ $assignment->total_points }}</td>
                                            <td>{{ $assignment->submissions->count() }}</td>
                                            <td>
                                                @if($assignment->due_date->isFuture())
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Closed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('faculty.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No assignments created yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Exams Tab --}}
                <div class="tab-pane fade" id="exams" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Exams</h5>
                            <a href="{{ route('faculty.exams.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Exam
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Manage exams for this course. You can create, edit, and view exam results.</p>
                            <a href="{{ route('faculty.exams.index') }}?course={{ $course->id }}" class="btn btn-outline-primary">
                                View All Exams
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Quizzes Tab --}}
                <div class="tab-pane fade" id="quizzes" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quizzes</h5>
                            <a href="{{ route('faculty.quizzes.create', ['course_id' => $course->id]) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Create Quiz
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Manage quizzes for this course. Create quick assessments to test student understanding.</p>
                            <a href="{{ route('faculty.quizzes.index') }}?course={{ $course->id }}" class="btn btn-outline-primary">
                                View All Quizzes
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Materials Tab --}}
                <div class="tab-pane fade" id="materials" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Course Materials</h5>
                            <a href="{{ route('faculty.courses.materials.create', $course) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Upload Material
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @forelse($course->materials as $material)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $material->title }}</h6>
                                            <p class="card-text text-muted small">{{ $material->description }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $material->created_at->format('M d, Y') }}</small>
                                                <a href="{{ $material->file_path }}" class="btn btn-sm btn-outline-primary" download>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-center text-muted py-4">No materials uploaded yet</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('faculty.courses.enroll-student', $course) }}" method="POST" id="enrollStudentForm">
                @csrf
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="enrollStudentModalLabel">
                            <i class="bi bi-person-plus-fill text-primary me-2"></i>Enroll Students
                        </h5>
                        <div class="text-muted small">Select eligible students to enroll into <strong>{{ $course->code }} - {{ $course->name }}</strong></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filters & Search Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label for="student_search_input" class="form-label text-dark fw-semibold small mb-1">
                                <i class="bi bi-search me-1 text-primary"></i>Search Student
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="student_search_input" class="form-control border-start-0" placeholder="Search by name, student ID, or email..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clear_student_search" style="display: none;" title="Clear search">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label for="program_filter" class="form-label text-dark fw-semibold small mb-1">
                                <i class="bi bi-funnel me-1 text-primary"></i>Filter by Programme
                            </label>
                            <select class="form-select select2" id="program_filter" data-placeholder="All Programmes">
                                <option value="">All Programmes</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- List Header Controls -->
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-dark fw-semibold small">Available Students</span>
                            <span id="visibleStudentBadge" class="badge bg-secondary-subtle text-secondary border small">
                                {{ $availableStudents->count() }} available
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span id="selectedStudentBadge" class="badge bg-primary small">
                                0 selected
                            </span>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-danger small" id="clearAllSelections" style="display: none; font-size: 0.8rem;">
                                Deselect all
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Student List -->
                    <div class="border rounded p-2" style="max-height: 320px; overflow-y: auto; background-color: #fcfcfc;">
                        <div class="form-check p-2 mb-2 bg-light rounded border d-flex justify-content-between align-items-center">
                            <div class="ms-1">
                                <input class="form-check-input" type="checkbox" id="selectAllStudents">
                                <label class="form-check-label fw-bold text-dark cursor-pointer ms-1" for="selectAllStudents">
                                    Select All / None (Visible)
                                </label>
                            </div>
                            <small class="text-muted pe-2" id="visibleCounterHelp">
                                Click to toggle visible
                            </small>
                        </div>

                        <div id="modalStudentsList">
                            @forelse($availableStudents as $student)
                                @php
                                    $studentIdNum = $student->student_id 
                                        ?: ($student->studentProfile?->admission_number 
                                        ?: ($student->studentProfile?->student_id ?: ''));
                                    $programName = $student->studentProfile->program->name ?? '';
                                    $searchString = strtolower($student->name . ' ' . $studentIdNum . ' ' . $student->email . ' ' . $programName);
                                @endphp
                                <div class="student-checkbox-item p-2 rounded mb-1" 
                                     data-program-id="{{ $student->studentProfile->program_id ?? '' }}"
                                     data-search="{{ $searchString }}">
                                    <div class="form-check d-flex align-items-center mb-0">
                                        <input class="form-check-input student-select-checkbox flex-shrink-0" 
                                               type="checkbox" 
                                               name="student_ids[]" 
                                               value="{{ $student->id }}" 
                                               id="student_chk_{{ $student->id }}">
                                        <label class="form-check-label text-dark w-100 ms-2 cursor-pointer d-flex justify-content-between align-items-center flex-wrap gap-1" for="student_chk_{{ $student->id }}">
                                            <div>
                                                <span class="fw-semibold text-dark">{{ $student->name }}</span>
                                                @if(!empty($studentIdNum))
                                                    <span class="badge bg-light text-secondary border ms-1 font-monospace" style="font-size: 0.72rem;">
                                                        {{ $studentIdNum }}
                                                    </span>
                                                @endif
                                                <span class="text-muted small ms-1">({{ $student->email }})</span>
                                            </div>
                                            @if(!empty($programName))
                                                <span class="badge bg-light text-primary border small" style="font-size: 0.7rem;">
                                                    <i class="bi bi-mortarboard me-1"></i>{{ $programName }}
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-people text-secondary" style="font-size: 2rem;"></i>
                                    <div class="mt-2 fw-medium">No available students to enroll</div>
                                    <small>All active students are already enrolled in this course.</small>
                                </div>
                            @endforelse

                            <div id="noStudentsFilteredMessage" class="text-center text-muted py-4" style="display: none;">
                                <i class="bi bi-search text-secondary" style="font-size: 2rem;"></i>
                                <div class="mt-2 fw-medium text-dark">No matching students found</div>
                                <small class="text-muted">No students match your search query or programme filter.</small>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="resetFiltersBtn">
                                        Clear Search & Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div>
                        <span class="text-muted small" id="footerSelectionSummary">0 students ready to enroll</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" id="submitEnrollBtn" disabled>
                            <i class="bi bi-person-plus-fill me-1"></i> Enroll Selected
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.student-checkbox-item {
    transition: background-color 0.15s ease;
    border-bottom: 1px dashed rgba(0,0,0,0.06);
}
.student-checkbox-item:hover {
    background-color: #f1f5f9;
}
.student-checkbox-item.is-selected {
    background-color: #eef2ff;
}
.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggerTabList = document.querySelectorAll('#courseTab button')
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', event => {
            event.preventDefault()
            tabTrigger.show()
        })
    })

    // Filter, search and select logic in the Enroll Student Modal
    const programFilter = document.getElementById('program_filter');
    const searchInput = document.getElementById('student_search_input');
    const clearSearchBtn = document.getElementById('clear_student_search');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const selectAllCheckbox = document.getElementById('selectAllStudents');
    const studentItems = document.querySelectorAll('.student-checkbox-item');
    const noStudentsFilteredMessage = document.getElementById('noStudentsFilteredMessage');
    const visibleStudentBadge = document.getElementById('visibleStudentBadge');
    const selectedStudentBadge = document.getElementById('selectedStudentBadge');
    const clearAllSelectionsBtn = document.getElementById('clearAllSelections');
    const submitEnrollBtn = document.getElementById('submitEnrollBtn');
    const footerSelectionSummary = document.getElementById('footerSelectionSummary');
    const enrollModal = document.getElementById('enrollStudentModal');

    // Auto-focus search input when modal opens
    if (enrollModal) {
        enrollModal.addEventListener('shown.bs.modal', function () {
            if (searchInput) {
                searchInput.focus();
            }
        });
    }

    function updateSelectionCounts() {
        const checkedCheckboxes = document.querySelectorAll('.student-select-checkbox:checked');
        const count = checkedCheckboxes.length;
        
        if (selectedStudentBadge) {
            selectedStudentBadge.textContent = count + ' selected';
        }
        if (footerSelectionSummary) {
            footerSelectionSummary.textContent = count + (count === 1 ? ' student ready to enroll' : ' students ready to enroll');
        }
        if (submitEnrollBtn) {
            submitEnrollBtn.disabled = (count === 0);
        }
        if (clearAllSelectionsBtn) {
            clearAllSelectionsBtn.style.display = count > 0 ? 'inline-block' : 'none';
        }

        // Highlight selected items
        studentItems.forEach(item => {
            const cb = item.querySelector('.student-select-checkbox');
            if (cb && cb.checked) {
                item.classList.add('is-selected');
            } else {
                item.classList.remove('is-selected');
            }
        });

        // Update select all checkbox state based on visible items
        const visibleItems = Array.from(studentItems).filter(item => item.style.display !== 'none');
        if (visibleItems.length > 0 && selectAllCheckbox) {
            const visibleChecked = visibleItems.filter(item => {
                const cb = item.querySelector('.student-select-checkbox');
                return cb && cb.checked;
            });
            selectAllCheckbox.checked = (visibleChecked.length === visibleItems.length);
            selectAllCheckbox.indeterminate = (visibleChecked.length > 0 && visibleChecked.length < visibleItems.length);
        } else if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    function applyStudentFilters() {
        const selectedProgramId = programFilter ? programFilter.value : '';
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        if (clearSearchBtn) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        studentItems.forEach(item => {
            const programId = item.getAttribute('data-program-id') || '';
            const searchData = item.getAttribute('data-search') || '';

            const matchesProgram = (selectedProgramId === "" || programId === selectedProgramId);
            const matchesSearch = (query === "" || searchData.indexOf(query) !== -1);

            if (matchesProgram && matchesSearch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleStudentBadge) {
            visibleStudentBadge.textContent = visibleCount + ' visible';
        }

        if (noStudentsFilteredMessage) {
            if (visibleCount === 0 && studentItems.length > 0) {
                noStudentsFilteredMessage.style.display = 'block';
            } else {
                noStudentsFilteredMessage.style.display = 'none';
            }
        }

        updateSelectionCounts();
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyStudentFilters);
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                applyStudentFilters();
            }
        });
    }

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (programFilter) {
                $(programFilter).val('').trigger('change');
            } else {
                applyStudentFilters();
            }
        });
    }

    if (programFilter) {
        $(programFilter).on('change', applyStudentFilters);
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            studentItems.forEach(item => {
                if (item.style.display !== 'none') {
                    const cb = item.querySelector('.student-select-checkbox');
                    if (cb) {
                        cb.checked = isChecked;
                    }
                }
            });
            updateSelectionCounts();
        });
    }

    studentItems.forEach(item => {
        const cb = item.querySelector('.student-select-checkbox');
        if (cb) {
            cb.addEventListener('change', updateSelectionCounts);
        }
    });

    if (clearAllSelectionsBtn) {
        clearAllSelectionsBtn.addEventListener('click', function() {
            studentItems.forEach(item => {
                const cb = item.querySelector('.student-select-checkbox');
                if (cb) cb.checked = false;
            });
            updateSelectionCounts();
        });
    }

    // Initial count
    updateSelectionCounts();
})
</script>
@endsection
