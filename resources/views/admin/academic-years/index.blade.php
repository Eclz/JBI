@extends('layouts.app')

@section('title', 'Academic Years')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Academic Years</h1>
            <p class="text-muted">Manage academic years</p>
        </div>
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Academic Year
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if($academicYears->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Year</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($academicYears as $year)
                                <tr>
                                    <td>{{ $year->name }}</td>
                                    <td>{{ $year->year }}</td>
                                    <td>{{ $year->start_date->format('M d, Y') }} - {{ $year->end_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $year->is_current ? 'success' : ($year->is_active ? 'info' : 'secondary') }}">
                                            {{ $year->is_current ? 'Current' : ($year->is_active ? 'Active' : 'Inactive') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('admin.academic-years.destroy', $year) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this academic year?');">
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
                    <i class="bi bi-calendar3 display-1 text-muted"></i>
                    <h5 class="mt-3">No academic years</h5>
                    <p class="text-muted">Create an academic year to get started.</p>
                </div>
            @endif
        </div>
        @if($academicYears->hasPages())
            <div class="card-footer">
                {{ $academicYears->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
