@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0 text-primary"><i class="bi bi-pencil me-2"></i>Edit Announcement</h1>
    <p class="text-muted mb-0">{{ $announcement->title }}</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">Please correct the highlighted fields and try again.</div>
@endif

<form method="POST" action="{{ route('announcements.update', $announcement) }}">
    @csrf
    @method('PUT')
    @include('announcements._form')
</form>
@endsection
