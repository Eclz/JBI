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
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr>
                            <td><span class="badge badge-light px-2 py-1 border text-dark">{{ $course->code ?? 'N/A' }}</span></td>
                            <td class="font-weight-bold">{{ $course->name }}</td>
                            <td>{{ $course->credits ?? 'N/A' }}</td>
                            <td>
                                @if($course->is_active ?? true)
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
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
