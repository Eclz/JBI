@extends('layouts.app')

@section('title', 'Lecturer Rankings')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="bi bi-bar-chart-fill text-primary me-2"></i>Lecturer Rankings
            </h1>
            <p class="text-muted mb-0">Aggregate performance rankings based on student evaluation surveys.</p>
        </div>
        <a href="{{ route('admin.evaluation-surveys.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Surveys
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.evaluation-surveys.rankings') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Filter by Survey</label>
                    <select name="survey_id" class="form-select">
                        <option value="">All Time (Overall Ranks)</option>
                        @foreach($surveys as $survey)
                            <option value="{{ $survey->id }}" {{ $surveyId == $survey->id ? 'selected' : '' }}>
                                {{ $survey->title }} ({{ $survey->semester_number == 1 ? 'Semester I' : 'Semester II' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.evaluation-surveys.rankings') }}" class="btn btn-light w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rankings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Rank</th>
                            <th>Lecturer</th>
                            <th>Department</th>
                            <th>Courses Evaluated</th>
                            <th class="text-center">Responses</th>
                            <th class="text-center">Average Score</th>
                            <th class="text-center">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lecturerStats as $index => $stat)
                            <tr>
                                <td class="text-center">
                                    @if($index === 0)
                                        <div class="badge bg-warning text-dark fs-6 rounded-circle p-2 shadow-sm"><i class="bi bi-trophy-fill"></i></div>
                                    @elseif($index === 1)
                                        <div class="badge bg-secondary fs-6 rounded-circle p-2 shadow-sm"><i class="bi bi-award-fill"></i></div>
                                    @elseif($index === 2)
                                        <div class="badge fs-6 rounded-circle p-2 shadow-sm" style="background-color: #cd7f32;"><i class="bi bi-award-fill"></i></div>
                                    @else
                                        <span class="fw-bold fs-5 text-muted">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $stat['lecturer']->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($stat['lecturer']->name).'&background=random' }}" 
                                             class="rounded-circle me-3 border shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $stat['lecturer']->name }}</div>
                                            <div class="small text-muted">{{ $stat['lecturer']->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $stat['lecturer']->hrProfile->department ?? 'General' }}</span>
                                </td>
                                <td>
                                    @if(!empty($stat['courses']))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($stat['courses'], 0, 2) as $c)
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">{{ $c }}</span>
                                            @endforeach
                                            @if(count($stat['courses']) > 2)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">+{{ count($stat['courses']) - 2 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ number_format($stat['responses_count']) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="h5 mb-0 fw-bold {{ $stat['average'] >= 4.5 ? 'text-success' : ($stat['average'] >= 3 ? 'text-primary' : 'text-danger') }}">
                                        {{ number_format($stat['average'], 2) }}
                                    </div>
                                </td>
                                <td class="text-center" style="min-width: 120px;">
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($stat['average']))
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star text-light"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="bi bi-clipboard-x fs-1"></i></div>
                                    <h5 class="fw-bold">No Rankings Available</h5>
                                    <p class="text-muted">There are no evaluation survey responses to generate rankings yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
