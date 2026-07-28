@extends('layouts.app')

@section('title', $program->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $program->name }}</h1>
            <p class="text-muted">{{ $program->code }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $program->department->name ?? '—' }}</dd>

                <dt class="col-sm-3">Level</dt>
                <dd class="col-sm-9">{{ $program->level->name ?? '—' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $program->is_active ? 'success' : 'secondary' }}">
                        {{ $program->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $program->description ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Courses ({{ $program->courses->count() }})</h5>
        </div>
        <div class="card-body">
            @if($program->courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($program->courses as $course)
                                <tr>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->semester->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No courses assigned yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
