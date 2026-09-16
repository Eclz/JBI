@extends('layouts.app')

@section('title', 'Edit Catalogue Item')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="mb-1 fw-bold text-dark">Edit Catalogue Item</h2><p class="text-muted mb-0">Update the library catalogue record.</p></div>
        <a href="{{ route('library.catalogue.index') }}" class="btn btn-outline-secondary">Back to catalogue</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('library.catalogue.update', $item) }}">
            @csrf
            @method('PUT')
            @include('library.form', ['item' => $item])
            <button class="btn btn-primary">Update item</button>
        </form>
    </div></div>
</div>
@endsection
