@extends('layouts.app')

@section('title', 'Add Facility Room')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="mb-1 fw-bold text-dark">Add Facility Room</h2><p class="text-muted mb-0">Create a room or campus asset record.</p></div>
        <a href="{{ route('facilities.rooms.index') }}" class="btn btn-outline-secondary">Back to rooms</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('facilities.rooms.store') }}">
            @csrf
            @include('facilities.form')
            <button class="btn btn-primary">Save room</button>
        </form>
    </div></div>
</div>
@endsection
