@extends('layouts.app')

@section('title', 'Department Courses - ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-book text-warning mr-2"></i>Courses
            </h1>
            <p class="text-muted mb-0">Courses offered by {{ $department->name }}</p>
        </div>
        <a href="{{ route('faculty.hod.department.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Name</th>
                            <th>Credits</th>
                            <th>Assigned Lecturer</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr>
                            <td><span class="badge badge-light px-2 py-1 border text-dark">{{ $course->code ?? 'N/A' }}</span></td>
                            <td class="font-weight-bold">
                                <a href="{{ route('faculty.courses.show', $course) }}" class="text-decoration-none">
                                    {{ $course->name }}
                                </a>
                            </td>
                            <td>{{ $course->credits ?? 'N/A' }}</td>
                            <td>
                                @if($course->instructor)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-2" style="width: 30px; height: 30px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 12px; font-weight: bold; color: #6c757d;">{{ substr($course->instructor->name, 0, 2) }}</span>
                                        </div>
                                        <span>{{ $course->instructor->name }}</span>
                                    </div>
                                @else
                                    <span class="text-danger small"><i class="fas fa-exclamation-triangle mr-1"></i>Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($course->is_active ?? true)
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal{{ $course->id }}">
                                    <i class="fas fa-user-plus mr-1"></i> Assign
                                </button>
                            </td>
                        </tr>

                        <!-- Assign Lecturer Modal -->
                        <div class="modal fade" id="assignModal{{ $course->id }}" tabindex="-1" aria-labelledby="assignModalLabel{{ $course->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title" id="assignModalLabel{{ $course->id }}">Assign Lecturer: {{ $course->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('faculty.hod.department.courses.assign', $course) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p class="text-muted small mb-3">Select a faculty member from your department to assign to this course.</p>
                                            <div class="form-group mb-3">
                                                <label for="instructor_id" class="form-label font-weight-bold">Select Lecturer</label>
                                                <select name="instructor_id" id="instructor_id" class="form-select @error('instructor_id') is-invalid @enderror" required>
                                                    <option value="">-- Choose Lecturer --</option>
                                                    @foreach($lecturers as $lecturer)
                                                        <option value="{{ $lecturer->id }}" {{ $course->instructor_id == $lecturer->id ? 'selected' : '' }}>
                                                            {{ $lecturer->name }} 
                                                            @if($lecturer->facultyProfile && $lecturer->facultyProfile->designation)
                                                                ({{ $lecturer->facultyProfile->designation }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('instructor_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Assignment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-book-open fa-3x mb-3 text-gray-300"></i>
                                <h5>No courses found</h5>
                                <p>There are no courses currently listed under this department.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
