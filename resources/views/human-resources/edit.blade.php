@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="mb-1 fw-bold text-dark">Edit Employee</h2><p class="text-muted mb-0">Update the employee record and HR profile.</p></div>
        <a href="{{ route('human-resources.staff.index') }}" class="btn btn-outline-secondary">Back to staff</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('human-resources.staff.update', $employee) }}">
            @csrf
            @method('PUT')
            @include('human-resources.form', ['employee' => $employee, 'hrProfile' => $hrProfile, 'jobRoles' => $jobRoles ?? []])
            <button class="btn btn-primary">Update employee</button>
        </form>
    </div></div>
</div>
@endsection
