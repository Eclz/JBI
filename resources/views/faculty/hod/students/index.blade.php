@extends('layouts.app')

@section('title', 'Department Students - ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users-class text-primary mr-2"></i>Department Students
            </h1>
            <p class="text-muted mb-0">Manage students in {{ $department->name }}</p>
        </div>
        <div class="col-md-6 text-right">
            <form action="{{ route('faculty.hod.students.index') }}" method="GET" class="d-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" name="search" class="form-control bg-white border-0 small shadow-sm" placeholder="Search students..." aria-label="Search" aria-describedby="basic-addon2" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Programme</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>
                                <span class="badge badge-light px-2 py-1 text-dark border">{{ $student->admission_number ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-primary text-white rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ substr($student->user->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $student->user->first_name }} {{ $student->user->last_name }}</div>
                                        <div class="small text-muted">{{ $student->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->program->name ?? 'N/A' }}</td>
                            <td>
                                @if($student->status === 'active')
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ ucfirst($student->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('faculty.hod.students.show', $student->id) }}" class="btn btn-sm btn-outline-primary btn-icon">
                                    <i class="fas fa-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3 text-gray-300"></i>
                                    <h5>No students found</h5>
                                    <p>There are no students enrolled in this department matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $students->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
