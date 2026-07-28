@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Create Custom Role</h4>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @include('admin.roles._form')
            </form>
        </div>
    </div>
</div>
@endsection
