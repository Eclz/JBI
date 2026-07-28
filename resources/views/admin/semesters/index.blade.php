@extends('layouts.app')

@section('title', 'Semesters')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Semesters</h1>
            <p class="text-muted">Manage semesters and registration windows</p>
        </div>
        <a href="{{ route('admin.semesters.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Semester
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($semesters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Academic Year</th>
                                <th>Dates</th>
                                <th>Registration</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semesters as $semester)
                                <tr>
                                    <td>{{ $semester->name }}</td>
                                    <td>{{ $semester->academicYear->name ?? 'N/A' }}</td>
                                    <td>{{ $semester->start_date->format('M d, Y') }} - {{ $semester->end_date->format('M d, Y') }}</td>
                                    <td>{{ $semester->registration_start->format('M d, Y') }} - {{ $semester->registration_end->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $semester->is_current ? 'success' : ($semester->is_active ? 'info' : 'secondary') }}">
                                            {{ $semester->is_current ? 'Current' : ($semester->is_active ? 'Active' : 'Inactive') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.semesters.destroy', $semester) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this semester?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar2 display-1 text-muted"></i>
                    <h5 class="mt-3">No semesters</h5>
                    <p class="text-muted">Create semesters to open enrollment windows.</p>
                </div>
            @endif
        </div>
        @if($semesters->hasPages())
            <div class="card-footer">
                {{ $semesters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
