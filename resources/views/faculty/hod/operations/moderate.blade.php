@extends('layouts.app')

@section('title', 'Result Moderation: ' . $title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Breadcrumb & Top Navigation -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('faculty.hod.operations.index') }}" class="text-decoration-none">HOD Operations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Result Moderation</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-1.5 fw-semibold font-monospace">
                    {{ $course->code }}
                </span>
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-1.5">
                    {{ $course->name }}
                </span>
            </div>
            <h1 class="h3 mb-0 text-dark fw-bold mt-2">
                <i class="bi bi-sliders text-success me-2"></i>Result Moderation & Endorsement: {{ $title }}
            </h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('faculty.hod.operations.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Operations Hub
            </a>
            @if(!$isFullyPublished)
                <form action="{{ route('faculty.hod.operations.results.approve', ['type' => $type, 'id' => $id]) }}" method="POST" onsubmit="return confirm('Endorse and publish all results for this assessment? All marks will become immediately visible on student portals and transcripts.');">
                    @csrf
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i>Approve & Publish Results
                    </button>
                </form>
            @else
                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-1"></i>Officially Moderated & Published
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Academic Metadata & Summary Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-3 col-md-6 border-end-md">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Course & Assessment</span>
                    <h5 class="fw-bold text-dark mb-0 mt-1">{{ $course->code }}: {{ $title }}</h5>
                    <small class="text-muted">Type: <span class="fw-semibold text-capitalize">{{ $type }}</span> &bull; Max Marks: <strong>{{ number_format($maxPoints, 1) }}</strong></small>
                </div>
                <div class="col-lg-3 col-md-6 border-end-lg">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Course Lecturer / Assessor</span>
                    <div class="d-flex align-items-center mt-1">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 32px; height: 32px;">
                            {{ substr($course->instructor->name ?? 'L', 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0">{{ $course->instructor->name ?? 'Course Lecturer' }}</div>
                            <small class="text-muted">{{ $course->instructor->email ?? 'N/A' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 border-end-md">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Class Performance</span>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <h4 class="fw-bold text-dark mb-0">{{ $averagePercentage }}%</h4>
                        <span class="small text-muted">avg score ({{ round(($averagePercentage / 100) * $maxPoints, 1) }} / {{ $maxPoints }})</span>
                    </div>
                    <small class="text-muted">High: <strong>{{ $highestPercentage }}%</strong> &bull; Low: <strong>{{ $lowestPercentage }}%</strong></small>
                </div>
                <div class="col-lg-3 col-md-6">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Pass Rate & Moderation</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <h4 class="fw-bold {{ $passRate >= 75 ? 'text-success' : ($passRate >= 50 ? 'text-warning' : 'text-danger') }} mb-0">
                            {{ $passRate }}%
                        </h4>
                        <span class="badge bg-light text-secondary border">{{ $publishedCount }} of {{ $totalCandidates }} Published</span>
                    </div>
                    <!-- Grade distribution pills -->
                    <div class="d-flex gap-1 mt-1 small">
                        <span class="badge bg-success bg-opacity-75">A: {{ $gradeDistribution['A'] }}</span>
                        <span class="badge bg-primary bg-opacity-75">B: {{ $gradeDistribution['B'] }}</span>
                        <span class="badge bg-info bg-opacity-75 text-dark">C: {{ $gradeDistribution['C'] }}</span>
                        <span class="badge bg-warning bg-opacity-75 text-dark">D: {{ $gradeDistribution['D'] }}</span>
                        <span class="badge bg-danger bg-opacity-75">F: {{ $gradeDistribution['F'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calibration / Score Scaling Toolbar (Collapsible) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up text-primary me-2"></i>Departmental Grade Calibration & Uniform Curve</h6>
            <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#scalingCollapse">
                <i class="bi bi-chevron-expand me-1"></i>Toggle Uniform Scaling Tool
            </button>
        </div>
        <div class="collapse" id="scalingCollapse">
            <div class="card-body bg-light p-4">
                <form action="{{ route('faculty.hod.operations.results.adjust', ['type' => $type, 'id' => $id]) }}" method="POST" class="row g-3 align-items-center" onsubmit="return confirm('Apply uniform mark adjustment across all candidates? Individual scores will be bounded between 0 and the max points.');">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold text-dark">Uniform Mark Curve / Adjustment</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-plus-slash-minus"></i></span>
                            <input type="number" step="0.5" name="scale_points" class="form-control" placeholder="e.g. +3.0 or -2.0 points" required>
                            <span class="input-group-text">marks</span>
                        </div>
                        <small class="text-muted">Applies to all candidate scores up to the maximum score of {{ $maxPoints }}.</small>
                    </div>
                    <div class="col-md-4 pt-md-3">
                        <button type="submit" class="btn btn-primary fw-bold px-3">
                            <i class="bi bi-arrow-repeat me-1"></i>Apply Uniform Curve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Candidates Roster & Moderation Form -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i>Candidate Marks & Moderation Roster</h5>
                <small class="text-muted">Review raw scores submitted by the lecturer and calibrate individual moderated scores before official endorsement.</small>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1.5">{{ $totalCandidates }} Candidates Evaluated</span>
        </div>

        <form action="{{ route('faculty.hod.operations.results.adjust', ['type' => $type, 'id' => $id]) }}" method="POST">
            @csrf
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Candidate / Student</th>
                                <th class="py-3">Admission Number</th>
                                <th class="py-3 text-center">Raw Lecturer Score</th>
                                <th class="py-3 text-center" style="width: 160px;">Moderated Score</th>
                                <th class="py-3 text-center">Percentage</th>
                                <th class="py-3 text-center">Letter Grade</th>
                                <th class="py-3">Lecturer Remarks / Feedback</th>
                                <th class="text-end pe-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $index => $c)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $c->student_name }}</div>
                                        <small class="text-muted">{{ $c->student_email }}</small>
                                    </td>
                                    <td>
                                        <span class="font-monospace text-primary fw-semibold">{{ $c->admission_number }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-secondary">{{ number_format($c->raw_score, 1) }}</span>
                                        <span class="text-muted small">/ {{ $maxPoints }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   step="0.1" 
                                                   min="0" 
                                                   max="{{ $maxPoints }}" 
                                                   name="scores[{{ $c->record_id }}]" 
                                                   value="{{ $c->moderated_score }}" 
                                                   class="form-control text-center fw-bold border-primary text-primary" 
                                                   required>
                                            <span class="input-group-text small">/{{ $maxPoints }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold {{ $c->percentage >= 70 ? 'text-success' : ($c->percentage >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ $c->percentage }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($c->letter_grade === 'A')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">A</span>
                                        @elseif($c->letter_grade === 'B')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold">B</span>
                                        @elseif($c->letter_grade === 'C')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold">C</span>
                                        @elseif($c->letter_grade === 'D')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold">D</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold">F</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted d-inline-block text-truncate" style="max-width: 220px;">
                                            {{ $c->feedback ?: 'No remarks' }}
                                        </small>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($c->is_published)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                                                <i class="bi bi-check-circle-fill me-1"></i>Published
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 small text-dark">
                                                <i class="bi bi-hourglass me-1"></i>Pending Endorsement
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        No graded student submissions found for this assessment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($candidates->isNotEmpty())
                <div class="card-footer bg-light p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <button type="submit" class="btn btn-outline-primary fw-bold">
                            <i class="bi bi-save me-1"></i>Save Score Adjustments
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#returnRevisionModal">
                            <i class="bi bi-arrow-return-left me-1"></i>Return to Lecturer for Revision
                        </button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Modal: Return to Lecturer for Revision -->
<div class="modal fade" id="returnRevisionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('faculty.hod.operations.results.return', ['type' => $type, 'id' => $id]) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-arrow-return-left me-2"></i>Request Grading Revision</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Provide specific feedback or moderation instructions to the course lecturer (<strong>{{ $course->instructor->name ?? 'Lecturer' }}</strong>). The assessment results will be returned for correction prior to departmental publishing.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">HOD Moderation Feedback / Revision Instructions <span class="text-danger">*</span></label>
                        <textarea name="moderation_feedback" class="form-control" rows="4" placeholder="Detail any grading anomalies, question weighting issues, or required mark recalibrations..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        <i class="bi bi-send me-1"></i>Send Feedback & Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
