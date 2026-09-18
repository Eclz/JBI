@extends('layouts.app')

@section('title', 'Survey Results - ' . $survey->title)

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Navigation Breadcrumb -->
    <div class="mb-3">
        <a href="{{ route('admin.evaluation-surveys.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Back to Lecturer Evaluation Surveys
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header & Action Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h2 class="h3 mb-0 text-dark fw-bold">{{ $survey->title }}</h2>
                        <span class="badge bg-{{ $survey->is_active ? 'success' : 'secondary' }} fs-6 px-3 py-2 rounded-pill">
                            <i class="bi bi-circle-fill me-1 small"></i>{{ $survey->is_active ? 'ACTIVE' : 'CLOSED' }}
                        </span>
                    </div>
                    <p class="text-muted mb-2">{{ $survey->description ?? 'No detailed description provided for this evaluation survey.' }}</p>
                    <div class="d-flex gap-3 text-muted small flex-wrap">
                        <span><i class="bi bi-calendar3 me-1 text-primary"></i><strong>Semester:</strong> Semester {{ $survey->semester_number }}</span>
                        @if($survey->academicYear)
                            <span><i class="bi bi-bookmark me-1 text-primary"></i><strong>Academic Year:</strong> {{ $survey->academicYear->name ?? $survey->academicYear->year }}</span>
                        @endif
                        <span><i class="bi bi-clock me-1 text-primary"></i><strong>Created:</strong> {{ $survey->created_at ? $survey->created_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ route('admin.evaluation-surveys.toggle-status', $survey) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $survey->is_active ? 'warning' : 'success' }}">
                            <i class="bi bi-power me-1"></i>{{ $survey->is_active ? 'Close Survey' : 'Activate Survey' }}
                        </button>
                    </form>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSurveyModal">
                        <i class="bi bi-pencil me-1"></i>Edit Survey
                    </button>
                    <form action="{{ route('admin.evaluation-surveys.destroy', $survey) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this evaluation survey? All submitted responses will be removed.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1 small fw-semibold">Total Responses</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalResponsesCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning me-3">
                        <i class="bi bi-star-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1 small fw-semibold">Overall Rating Avg</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $overallAverage }} <span class="fs-6 text-muted">/ 5.0</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                        <i class="bi bi-patch-question-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1 small fw-semibold">Total Questions</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $survey->questions->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                        <i class="bi bi-journal-check fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1 small fw-semibold">Target Semester</h6>
                        <h3 class="fw-bold mb-0 text-dark">Semester {{ $survey->semester_number }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs border-bottom-0 mb-4" id="surveyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold text-dark px-4 py-3" id="results-tab" data-bs-toggle="tab" data-bs-target="#results-content" type="button" role="tab">
                <i class="bi bi-bar-chart-line me-2 text-primary"></i>Question Analysis & Ratings
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold text-dark px-4 py-3" id="responses-tab" data-bs-toggle="tab" data-bs-target="#responses-content" type="button" role="tab">
                <i class="bi bi-chat-left-text me-2 text-primary"></i>Student Submissions ({{ $totalResponsesCount }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold text-dark px-4 py-3" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-content" type="button" role="tab">
                <i class="bi bi-gear me-2 text-primary"></i>Manage Survey Questions ({{ $survey->questions->count() }})
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="surveyTabsContent">
        
        <!-- TAB 1: QUESTION ANALYSIS -->
        <div class="tab-pane fade show active" id="results-content" role="tabpanel">
            @if($survey->questions->count() == 0)
                <div class="card border-0 shadow-sm p-5 text-center">
                    <i class="bi bi-clipboard-x display-4 text-muted mb-3"></i>
                    <h5>No Questions Configured</h5>
                    <p class="text-muted">This evaluation survey does not have any questions yet.</p>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="bi bi-plus-lg me-1"></i>Add First Question
                        </button>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($survey->questions as $index => $q)
                        @php
                            $stats = $questionStats[$q->id] ?? ['avg' => 0, 'count' => 0, 'starCounts' => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0]];
                            $avg = $stats['avg'];
                            $qCount = $stats['count'];
                            $starCounts = $stats['starCounts'];
                        @endphp
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2 fw-semibold px-2.5 py-1">
                                            {{ $q->category }}
                                        </span>
                                        <h6 class="fw-bold text-dark mb-0">
                                            Q{{ $index + 1 }}. {{ $q->question_text }}
                                        </h6>
                                    </div>
                                    <div class="text-end ms-3">
                                        <div class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-star-fill me-1"></i>{{ number_format($avg, 2) }}
                                        </div>
                                        <div class="small text-muted mt-1">{{ $qCount }} responses</div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <hr class="mt-0 mb-3 text-muted">
                                    <!-- Star Breakdown Bars -->
                                    @for($star = 5; $star >= 1; $star--)
                                        @php
                                            $cnt = $starCounts[$star] ?? 0;
                                            $pct = $qCount > 0 ? round(($cnt / $qCount) * 100) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="small fw-semibold text-muted" style="width: 55px;">
                                                {{ $star }} <i class="bi bi-star-fill text-warning"></i>
                                            </div>
                                            <div class="progress flex-grow-1 mx-2" style="height: 10px;">
                                                <div class="progress-bar bg-{{ match($star) { 5 => 'success', 4 => 'primary', 3 => 'info', 2 => 'warning', 1 => 'danger' } }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $pct }}%" 
                                                     aria-valuenow="{{ $pct }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <div class="small text-muted text-end" style="width: 70px;">
                                                {{ $cnt }} ({{ $pct }}%)
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 2: SUBMISSIONS & RESPONSES -->
        <div class="tab-pane fade" id="responses-content" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-card-list me-2 text-primary"></i>Submitted Lecturer Evaluation Responses</h6>
                    <span class="badge bg-light text-dark border">{{ $totalResponsesCount }} Total Submissions</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Lecturer</th>
                                    <th class="text-center">Avg Rating</th>
                                    <th>Submitted At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($survey->responses as $respIndex => $resp)
                                    @php
                                        $respRatings = is_array($resp->answers) ? array_values(array_filter($resp->answers, 'is_numeric')) : [];
                                        $respAvg = count($respRatings) > 0 ? round(array_sum($respRatings) / count($respRatings), 2) : 0;
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-muted">{{ $respIndex + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $resp->student?->name ?? 'Anonymous / Removed Student' }}</div>
                                            <small class="text-muted">{{ $resp->student?->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-bold">{{ strtoupper($resp->course?->code ?? $resp->course?->course_code ?? 'N/A') }}</span><br>
                                            <small class="text-muted">{{ $resp->course?->name }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $resp->lecturer?->name ?? 'Not Assigned' }}</div>
                                            <small class="text-muted">{{ $resp->lecturer?->email }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $respAvg >= 4 ? 'success' : ($respAvg >= 3 ? 'warning' : 'danger') }} fs-6">
                                                <i class="bi bi-star-fill me-1"></i>{{ $respAvg }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $resp->submitted_at ? $resp->submitted_at->format('M d, Y H:i') : ($resp->created_at ? $resp->created_at->format('M d, Y H:i') : 'N/A') }}</small>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#responseModal_{{ $resp->id }}">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Response Detail Modal -->
                                    <div class="modal fade" id="responseModal_{{ $resp->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold text-primary">
                                                        <i class="bi bi-person-bounding-box me-2"></i>Evaluation Response Details
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3 mb-4 p-3 bg-light rounded">
                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Student</small>
                                                            <strong>{{ $resp->student?->name ?? 'N/A' }}</strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Course</small>
                                                            <strong>{{ $resp->course?->code }} - {{ $resp->course?->name }}</strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Lecturer</small>
                                                            <strong>{{ $resp->lecturer?->name ?? 'Unassigned' }}</strong>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold mb-3 text-dark">Question Ratings:</h6>
                                                    <div class="list-group mb-4">
                                                        @foreach($survey->questions as $q)
                                                            @php
                                                                $ratingVal = $resp->answers[$q->id] ?? 'N/A';
                                                            @endphp
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <small class="badge bg-secondary me-2">{{ $q->category }}</small>
                                                                    <span>{{ $q->question_text }}</span>
                                                                </div>
                                                                <div>
                                                                    @if(is_numeric($ratingVal))
                                                                        <span class="badge bg-warning text-dark">
                                                                            <i class="bi bi-star-fill me-1"></i>{{ $ratingVal }} / 5
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-light text-dark border">{{ $ratingVal }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if($resp->comments)
                                                        <div class="p-3 bg-white border rounded">
                                                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-chat-quote me-2 text-primary"></i>Student Comments & Feedback:</h6>
                                                            <p class="text-muted mb-0 fst-italic">"{{ $resp->comments }}"</p>
                                                        </div>
                                                    @else
                                                        <p class="text-muted small fst-italic mb-0">No written comments provided by student.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No student evaluation submissions received for this survey yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: MANAGE QUESTIONS -->
        <div class="tab-pane fade" id="questions-content" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-primary"></i>Survey Questions Configuration</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="bi bi-plus-lg me-1"></i>Add New Question
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 70px;">Order</th>
                                    <th>Category</th>
                                    <th>Question Text</th>
                                    <th>Type</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($survey->questions as $q)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $q->display_order }}</td>
                                        <td><span class="badge bg-secondary">{{ $q->category }}</span></td>
                                        <td class="fw-medium text-dark">{{ $q->question_text }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ strtoupper($q->question_type) }}</span></td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.evaluation-surveys.questions.destroy', [$survey, $q]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No questions added yet. Click "Add New Question" above to configure survey questions.</td>
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

<!-- Edit Survey Modal -->
<div class="modal fade" id="editSurveyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.evaluation-surveys.update', $survey) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">Edit Evaluation Survey</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Survey Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $survey->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Semester <span class="text-danger">*</span></label>
                        <select name="semester_number" class="form-select" required>
                            <option value="1" {{ $survey->semester_number == 1 ? 'selected' : '' }}>Semester 1 (End of Semester I)</option>
                            <option value="2" {{ $survey->semester_number == 2 ? 'selected' : '' }}>Semester 2 (End of Semester II)</option>
                        </select>
                    </div>
                    @if(isset($academicYears) && $academicYears->count() > 0)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $survey->academic_year_id == $ay->id ? 'selected' : '' }}>
                                        {{ $ay->name ?? $ay->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $survey->description }}</textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="surveyActiveSwitch" value="1" {{ $survey->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="surveyActiveSwitch">Survey Active / Open for Submissions</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.evaluation-surveys.questions.store', $survey) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">Add New Survey Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="2" placeholder="e.g. Lecturer effectively explains complex concepts and answers questions." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Teaching Quality, Punctuality, Engagement" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Question Type <span class="text-danger">*</span></label>
                        <select name="question_type" class="form-select" required>
                            <option value="rating" selected>Rating Scale (1 - 5 Stars)</option>
                            <option value="text">Text Response</option>
                            <option value="boolean">Yes / No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
