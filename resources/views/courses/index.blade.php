@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-primary">
            <i class="bi bi-book me-2"></i>Courses
        </h1>
        <p class="text-muted mb-0">Manage course catalog and schedules</p>
    </div>
    @can('create', App\Models\Course::class)
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Add Course
        </a>
    @endcan
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('courses.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Course name or code">
            </div>
            <div class="col-md-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department" name="department">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" 
                                {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="semester" class="form-label">Semester</label>
                <select class="form-select" id="semester" name="semester">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" 
                                {{ request('semester') == $semester->id ? 'selected' : '' }}>
                            {{ $semester->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Courses Grid -->
<div class="row">
    @forelse($courses as $course)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-primary fw-bold">{{ $course->code }}</h6>
                        <small class="text-muted">{{ $course->department->name }}</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('courses.show', $course) }}">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a></li>
                            @can('update', $course)
                                <li><a class="dropdown-item" href="{{ route('courses.edit', $course) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a></li>
                            @endcan
                            @can('delete', $course)
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $course->id }})">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </a></li>
                            @endcan
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $course->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Credits</small>
                            <div class="fw-bold">{{ $course->credits }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Capacity</small>
                            <div class="fw-bold">{{ $course->enrollments_count ?? 0 }}/{{ $course->capacity }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Instructor</small>
                        <div>{{ $course->instructor->first_name ?? 'TBA' }} {{ $course->instructor->last_name ?? '' }}</div>
                    </div>
                    
                    @if($course->schedule_days && $course->schedule_time)
                        <div class="mb-3">
                            <small class="text-muted">Schedule</small>
                            <div>{{ $course->schedule_days }} {{ $course->schedule_time }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $course->status === 'active' ? 'success' : ($course->status === 'inactive' ? 'secondary' : 'primary') }}">
                            {{ ucfirst($course->status) }}
                        </span>
                        <small class="text-muted">{{ $course->semester->name ?? 'No Semester' }}</small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-book display-1 text-muted"></i>
                <h4 class="mt-3">No courses found</h4>
                <p class="text-muted">No courses match your current filters.</p>
                @can('create', App\Models\Course::class)
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Add First Course
                    </a>
                @endcan
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($courses->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $courses->links() }}
    </div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(courseId) {
    const form = document.getElementById('deleteForm');
    form.action = `/courses/${courseId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
