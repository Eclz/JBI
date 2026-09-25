@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Recruiting (ATS)</h2>
            <p class="text-muted mb-0">Manage job vacancies, track applicants, and hire new staff.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary">
                <i class="bi bi-funnel me-1"></i> Filters
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createVacancyModal">
                <i class="bi bi-plus-circle me-1"></i> Post Vacancy
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Open Vacancies</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['open_vacancies'] }}</h3>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded text-primary fs-4">
                            <i class="bi bi-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total Applications</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_applications'] }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded text-info fs-4">
                            <i class="bi bi-file-earmark-person"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Shortlisted</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['shortlisted'] }}</h3>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-warning fs-4">
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Hired (YTD)</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['hired'] }}</h3>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded text-success fs-4">
                            <i class="bi bi-person-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vacancies List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($vacancies->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="text-muted mb-3"><i class="bi bi-search" style="font-size: 4rem;"></i></div>
                    <h5 class="text-muted">No active vacancies</h5>
                    <p class="text-muted text-center mb-4">Create a vacancy to begin recruiting new talent.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVacancyModal">
                        Create Vacancy
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0">Position</th>
                                <th class="py-3 px-4 border-0">Department</th>
                                <th class="py-3 px-4 border-0 text-center">Applicants</th>
                                <th class="py-3 px-4 border-0">Hiring Manager</th>
                                <th class="py-3 px-4 border-0">Status</th>
                                <th class="py-3 px-4 border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vacancies as $vacancy)
                                <tr>
                                    <td class="px-4">
                                        <h6 class="mb-0 fw-bold text-primary">{{ $vacancy->position_title }}</h6>
                                        <small class="text-muted">{{ $vacancy->employment_type }} • {{ $vacancy->location ?? 'Remote/Office' }}</small>
                                    </td>
                                    <td class="px-4 text-muted">{{ $vacancy->department ?? 'General' }}</td>
                                    <td class="px-4 text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                                            {{ $vacancy->applicants_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-muted">
                                        {{ $vacancy->hiringManager->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-4">
                                        @if($vacancy->status === 'Open')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($vacancy->status === 'Closed')
                                            <span class="badge bg-secondary">Closed</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-end">
                                        <a href="{{ route('human-resources.recruiting.show', $vacancy->id) }}" class="btn btn-sm btn-outline-primary">
                                            View ATS
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Vacancy Modal -->
<div class="modal fade" id="createVacancyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('human-resources.recruiting.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Post New Vacancy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Position Title</label>
                            <input type="text" name="position_title" class="form-control" required placeholder="e.g. Senior Lecturer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" placeholder="e.g. Faculty of Science">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-select" required>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Temporary">Temporary</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Number of Openings</label>
                            <input type="number" name="num_openings" class="form-control" required value="1" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Closing Date</label>
                            <input type="date" name="closing_date" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Job Description</label>
                            <textarea name="job_description" class="form-control" rows="4" placeholder="Describe the responsibilities..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Publish Vacancy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
