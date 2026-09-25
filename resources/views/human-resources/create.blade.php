@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="mb-1 fw-bold text-dark">Add Employee</h2><p class="text-muted mb-0">Create a core HR employee record.</p></div>
        <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary">Back to staff</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('human-resources.staff.store') }}">
            @csrf
            @include('human-resources.form')
            <button class="btn btn-primary">Save employee</button>
        </form>
    </div></div>
</div>
@endsection
