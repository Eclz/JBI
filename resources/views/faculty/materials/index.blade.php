@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1" style="color: #1a202c; font-weight: 600;">Course Materials</h2>
            <p class="text-muted mb-0">Manage learning resources and materials for all your courses</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Courses</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $courses->count() }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-journal-text" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Total Materials</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $totalMaterials }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-earmark-text" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.875rem;">Recent Uploads</p>
                            <h3 class="mb-0" style="color: white; font-weight: 700;">{{ $recentMaterials->count() }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-clock-history" style="font-size: 24px; color: white;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="card-title mb-4" style="color: #1a202c; font-weight: 600;">Course Materials Overview</h5>

            @if($courses->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <p class="mt-3 text-muted">No courses assigned yet</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f7fafc;">
                            <tr>
                                <th style="color: #4a5568; font-weight: 600;">Course</th>
                                <th style="color: #4a5568; font-weight: 600;">Code</th>
                                <th style="color: #4a5568; font-weight: 600;">Materials Count</th>
                                <th style="color: #4a5568; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td style="color: #2d3748; font-weight: 500;">{{ $course->name }}</td>
                                    <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                    <td style="color: #4a5568;">{{ $course->materials_count }}</td>
                                    <td>
                                        <a href="{{ route('faculty.courses.materials.index', $course) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-folder me-1"></i>Manage
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

    <!-- Recent Materials -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title mb-4" style="color: #1a202c; font-weight: 600;">Recent Materials</h5>

            @if($recentMaterials->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <p class="mt-3 text-muted">No materials uploaded yet</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentMaterials as $material)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="me-3">
                                        <i class="bi bi-file-earmark-pdf" style="font-size: 2rem; color: #667eea;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1" style="color: #2d3748; font-weight: 500;">{{ $material->title }}</h6>
                                        <p class="mb-0 text-muted small">
                                            {{ $material->course->name }} • {{ $material->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($material->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
