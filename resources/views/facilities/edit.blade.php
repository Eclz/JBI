@extends('layouts.app')

@section('title', 'Edit Facility Room')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="mb-1 fw-bold text-dark">Edit Facility Room</h2><p class="text-muted mb-0">Update the room or asset record.</p></div>
        <a href="{{ route('facilities.rooms.index') }}" class="btn btn-outline-secondary">Back to rooms</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('facilities.rooms.update', $room) }}">
            @csrf
            @method('PUT')
            @include('facilities.form', ['room' => $room])
            <button class="btn btn-primary">Update room</button>
        </form>
    </div></div>
</div>
@endsection
