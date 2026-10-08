@extends('layouts.app')

@section('title', 'Add Facility')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Add Facility</h2>
            <p class="text-muted mb-0">Create a campus facility that rooms can be assigned to.</p>
        </div>
        <a href="{{ route('facilities.buildings.index') }}" class="btn btn-outline-secondary">Back to facilities</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('facilities.buildings.store') }}">
                @csrf
                @include('facilities.facilities.form')
                <button class="btn btn-primary">Save facility</button>
            </form>
        </div>
    </div>
</div>
@endsection
