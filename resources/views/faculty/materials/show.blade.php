@extends('layouts.app')

@section('title', $course->name . ' - Course Materials')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Course Materials</h2>
            <p class="text-muted mb-0">{{ $course->code }} - {{ $course->name }}</p>
        </div>
        <a href="{{ route('faculty.courses.materials.create', $course) }}" class="btn btn-primary">
            <i class="fas fa-upload me-2"></i>Upload Material
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Materials</p>
                            <h3 class="mb-0">{{ $materials->total() }}</h3>
                        </div>
                        <i class="fas fa-folder-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Lectures</p>
                            <h3 class="mb-0">{{ $materials->where('type', 'lecture')->count() }}</h3>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Readings</p>
                            <h3 class="mb-0">{{ $materials->where('type', 'reading')->count() }}</h3>
                        </div>
                        <i class="fas fa-book-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Videos</p>
                            <h3 class="mb-0">{{ $materials->where('type', 'video')->count() }}</h3>
                        </div>
                        <i class="fas fa-video fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($materials->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th style="color: #1a202c; font-weight: 600;">Title</th>
                                <th style="color: #1a202c; font-weight: 600;">Type</th>
                                <th style="color: #1a202c; font-weight: 600;">File</th>
                                <th style="color: #1a202c; font-weight: 600;">Size</th>
                                <th style="color: #1a202c; font-weight: 600;">Uploaded</th>
                                <th style="color: #1a202c; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materials as $material)
                            <tr>
                                <td>
                                    <div>
                                        <strong style="color: #1a202c;">{{ $material->title }}</strong>
                                        @if($material->description)
                                            <p class="text-muted small mb-0">{{ Str::limit($material->description, 60) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'lecture' => 'primary',
                                            'reading' => 'info',
                                            'assignment' => 'warning',
                                            'video' => 'success',
                                            'other' => 'secondary'
                                        ];
                                        $color = $typeColors[$material->type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($material->type) }}</span>
                                </td>
                                <td>
                                    <small style="color: #4a5568;">{{ $material->file_name }}</small>
                                </td>
                                <td>
                                    <small style="color: #4a5568;">{{ number_format($material->file_size / 1024, 2) }} KB</small>
                                </td>
                                <td>
                                    <small style="color: #4a5568;">{{ $material->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('faculty.courses.materials.destroy', [$course, $material]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this material?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $materials->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No materials uploaded yet</h5>
                    <p class="text-muted mb-3">Upload your first course material to get started.</p>
                    <a href="{{ route('faculty.courses.materials.create', $course) }}" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Material
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
