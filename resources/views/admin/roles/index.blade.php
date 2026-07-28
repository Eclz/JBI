@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Roles & Permissions</h1>
                    <p class="text-muted">Manage university roles and module-level actions.</p>
                </div>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Custom Role
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>University Area</th>
                            <th>Users</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $role->name }}</div>
                                    <small class="text-muted">{{ $role->description }}</small>
                                </td>
                                <td>{{ ucfirst($role->guard_role) }}</td>
                                <td>{{ $role->users_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $role->is_active ? 'success' : 'secondary' }}">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($role->is_system)
                                        <span class="badge bg-info ms-1">Default</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @unless($role->is_system)
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No roles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($roles->hasPages())
            <div class="card-footer">{{ $roles->links() }}</div>
        @endif
    </div>
</div>
@endsection
