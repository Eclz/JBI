@extends('layouts.app')

@section('title', 'Library Catalogue')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Library Catalogue</h2>
            <p class="text-muted mb-0">Search books, journals, and learning material.</p>
        </div>
        <a href="{{ route('library.index') }}" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-4">
                <form method="GET" class="row g-3 align-items-center flex-grow-1 me-3">
                    <div class="col-md-8">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title, author, ISBN...">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-primary">Search catalogue</button>
                    </div>
                </form>
                @if(auth()->user()->hasPermission('library_catalogue', 'create'))
                    <a href="{{ route('library.catalogue.create') }}" class="btn btn-primary">Add item</a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Available</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->author ?: '—' }}</td>
                                <td>{{ $item->category }}</td>
                                <td><span class="badge text-bg-{{ $item->available_copies > 0 ? 'success' : 'warning' }}">{{ $item->available_copies > 0 ? 'Available' : 'On loan' }}</span></td>
                                <td>{{ $item->available_copies }} / {{ $item->total_copies }}</td>
                                <td class="text-end">
                                    <a href="{{ route('library.catalogue.show', $item) }}" class="btn btn-sm btn-outline-info" title="View Details"><i class="bi bi-eye"></i> View</a>
                                    @if(auth()->user()->hasPermission('library_catalogue', 'edit'))
                                        <a href="{{ route('library.catalogue.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('library_catalogue', 'delete'))
                                        <form method="POST" action="{{ route('library.catalogue.destroy', $item) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this catalogue item?')">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No catalogue items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
