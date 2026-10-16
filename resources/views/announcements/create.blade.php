@extends('layouts.app')

@section('title', 'New Announcement')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0 text-primary"><i class="bi bi-megaphone me-2"></i>New Announcement</h1>
    <p class="text-muted mb-0">Publish information to students, faculty and staff.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger">Please correct the highlighted fields and try again.</div>
@endif

<form method="POST" action="{{ route('announcements.store') }}">
    @csrf
    @include('announcements._form')
</form>
@endsection
