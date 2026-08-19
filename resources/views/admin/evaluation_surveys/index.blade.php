@extends('layouts.app')

@section('title', 'Lecturer Evaluation Surveys')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary"><i class="bi bi-clipboard2-check me-2"></i>Lecturer Evaluation Surveys</h1>
            <p class="text-muted mb-0">Manage semester lecturer performance evaluations and review feedback</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSurveyModal">
            <i class="bi bi-plus-lg me-2"></i>Create New Survey
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Survey Title</th>
                            <th>Semester</th>
                            <th>Questions</th>
                            <th>Responses Submitted</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surveys as $survey)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $survey->title }}</span><br>
                                    <small class="text-muted">{{ $survey->description }}</small>
                                </td>
                                <td>Semester {{ $survey->semester_number }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $survey->questions->count() }} Questions</span></td>
                                <td><span class="badge bg-info">{{ $survey->responses_count }} Responses</span></td>
                                <td>
                                    <span class="badge bg-{{ $survey->is_active ? 'success' : 'secondary' }}">
                                        {{ $survey->is_active ? 'ACTIVE' : 'CLOSED' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.evaluation-surveys.show', $survey) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View Results
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No evaluation surveys created yet. Click "Create New Survey" to add one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Survey Modal -->
<div class="modal fade" id="createSurveyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.evaluation-surveys.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Lecturer Evaluation Survey</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Survey Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. End of Semester I Lecturer Evaluation Survey" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester_number" class="form-select" required>
                            <option value="1">Semester 1 (End of Semester I)</option>
                            <option value="2">Semester 2 (End of Semester II)</option>
                        </select>
                    </div>
                    @if(isset($academicYears) && $academicYears->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Academic Year (Optional)</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}">{{ $ay->name ?? $ay->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="End of semester student evaluation of course lecturers..."></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="surveyActiveCheck" value="1" checked>
                        <label class="form-check-label" for="surveyActiveCheck">Activate survey immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Survey</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
