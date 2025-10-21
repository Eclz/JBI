@extends('layouts.app')

@section('title', 'Course Materials - ' . $course->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Course Materials</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">My Courses</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.courses.show', $course) }}">{{ $course->name }}</a></li>
                            <li class="breadcrumb-item active">Materials</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('student.courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-2"></i>Back to Course
                    </a>
                </div>
            </div>

            <!-- Course Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $course->name }}</h5>
                            <p class="text-muted mb-0">{{ $course->code }} - {{ $course->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary">{{ $course->credits }} Credits</span>
                            <span class="badge bg-success">{{ $course->semester->name ?? 'Current Semester' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" id="materialSearch" placeholder="Search materials...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="lecture">Lecture Notes</option>
                                <option value="assignment">Assignment</option>
                                <option value="reading">Reading Material</option>
                                <option value="video">Video</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="sortBy">
                                <option value="order">Default Order</option>
                                <option value="title">Title (A-Z)</option>
                                <option value="date_desc">Newest First</option>
                                <option value="date_asc">Oldest First</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materials List -->
            <div class="row" id="materialsContainer">
                @forelse($materials as $material)
                <div class="col-md-6 col-lg-4 mb-4 material-item"
                     data-title="{{ strtolower($material->title) }}"
                     data-type="{{ $material->type }}"
                     data-date="{{ $material->created_at->timestamp }}">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @switch($material->type)
                                    @case('lecture')
                                        <i class="fa fa-chalkboard-teacher text-primary me-2"></i>
                                        @break
                                    @case('assignment')
                                        <i class="fa fa-tasks text-warning me-2"></i>
                                        @break
                                    @case('reading')
                                        <i class="fa fa-book text-info me-2"></i>
                                        @break
                                    @case('video')
                                        <i class="fa fa-video text-danger me-2"></i>
                                        @break
                                    @default
                                        <i class="fa fa-file-alt text-secondary me-2"></i>
                                @endswitch
                                <span class="badge bg-light text-dark">{{ ucfirst($material->type) }}</span>
                            </div>
                            <small class="text-muted">{{ $material->created_at->format('M d') }}</small>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $material->title }}</h6>
                            @if($material->description)
                            <p class="card-text text-muted">{{ Str::limit($material->description, 100) }}</p>
                            @endif

                            <div class="mt-3">
                                @if($material->file_path)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa fa-paperclip text-muted me-2"></i>
                                    <small class="text-muted">
                                        File: {{ basename($material->file_path) }}
                                        @if($material->file_size)
                                        ({{ number_format($material->file_size / 1024, 1) }} KB)
                                        @endif
                                    </small>
                                </div>
                                @endif

                                @if($material->url)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa fa-link text-muted me-2"></i>
                                    <small class="text-muted">External Link</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                @if($material->file_path)
                                <a href="{{ Storage::url($material->file_path) }}"
                                   class="btn btn-primary btn-sm flex-fill"
                                   target="_blank">
                                    <i class="fa fa-download me-1"></i>Download
                                </a>
                                @endif

                                @if($material->url)
                                <a href="{{ $material->url }}"
                                   class="btn btn-info btn-sm flex-fill"
                                   target="_blank">
                                    <i class="fa fa-external-link-alt me-1"></i>Open Link
                                </a>
                                @endif

                                @if(!$material->file_path && !$material->url)
                                <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                    <i class="fa fa-info-circle me-1"></i>Info Only
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Materials Available</h5>
                            <p class="text-muted">Your instructor hasn't uploaded any materials for this course yet.</p>
                            <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary">
                                <i class="fa fa-arrow-left me-2"></i>Back to Course
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($materials->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $materials->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('materialSearch');
    const typeFilter = document.getElementById('typeFilter');
    const sortBy = document.getElementById('sortBy');
    const materialsContainer = document.getElementById('materialsContainer');
    const materialItems = document.querySelectorAll('.material-item');

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        const sortOption = sortBy.value;

        // Convert NodeList to Array for sorting
        let itemsArray = Array.from(materialItems);

        // Filter items
        itemsArray.forEach(item => {
            const title = item.dataset.title;
            const type = item.dataset.type;

            const matchesSearch = title.includes(searchTerm);
            const matchesType = !selectedType || type === selectedType;

            if (matchesSearch && matchesType) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Sort visible items
        const visibleItems = itemsArray.filter(item => item.style.display !== 'none');

        visibleItems.sort((a, b) => {
            switch(sortOption) {
                case 'title':
                    return a.dataset.title.localeCompare(b.dataset.title);
                case 'date_desc':
                    return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                case 'date_asc':
                    return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                default: // 'order'
                    return 0; // Keep original order
            }
        });

        // Reorder DOM elements
        if (sortOption !== 'order') {
            visibleItems.forEach(item => {
                materialsContainer.appendChild(item);
            });
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterAndSort);
    typeFilter.addEventListener('change', filterAndSort);
    sortBy.addEventListener('change', filterAndSort);

    // Download tracking (optional)
    document.querySelectorAll('a[href*="storage"]').forEach(link => {
        link.addEventListener('click', function() {
            // You can add download tracking here if needed
            console.log('Material downloaded:', this.href);
        });
    });
});
</script>
@endpush
