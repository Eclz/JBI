@extends('layouts.app')

@section('title', 'Lecturer Evaluation Surveys')

@section('content')
<div class="container-fluid px-4 py-4">
    @include('partials.student-header-bar')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark text-uppercase mb-0">
                <i class="bi bi-clipboard2-check text-primary me-2"></i>LECTURER EVALUATION SURVEYS
            </h5>
            <p class="text-muted small mb-0">Evaluate your course lecturers at the end of every semester</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @forelse($surveys as $survey)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom border-primary border-2">
                <h6 class="fw-bold mb-1 text-primary">{{ strtoupper($survey->title) }}</h6>
                <small class="text-muted">{{ $survey->description }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th>COURSE CODE & TITLE</th>
                                <th>LECTURER</th>
                                <th>STATUS</th>
                                <th class="text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrolledCourses as $course)
                                @php
                                    $key = $survey->id . '_' . $course->id;
                                    $isCompleted = isset($completedResponses[$key]);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $course->code }}</span><br>
                                        <span class="fw-semibold text-dark">{{ $course->title }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $course->faculty?->full_name ?? 'Assigned Lecturer' }}</span>
                                    </td>
                                    <td>
                                        @if($isCompleted)
                                            <span class="badge bg-primary px-2 py-1"><i class="bi bi-check-circle me-1"></i>EVALUATED</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock me-1"></i>PENDING EVALUATION</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('student.evaluation-surveys.show', [$survey, $course]) }}" class="btn btn-sm {{ $isCompleted ? 'btn-outline-secondary' : 'btn-primary fw-bold' }}">
                                            {{ $isCompleted ? 'Review Submission' : 'Start Evaluation' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No enrolled courses found for evaluation.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-3"></i>
                <h6 class="fw-bold text-dark">NO ACTIVE EVALUATION SURVEYS</h6>
                <p class="text-muted small mb-0">Lecturer evaluation surveys open at the end of each semester.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
