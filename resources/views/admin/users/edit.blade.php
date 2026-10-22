@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit User: {{ $user->name }}</h4>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="role_id" class="form-label">Role <small class="text-muted fw-normal">(Optional)</small></label>
                                    <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                        <option value="">Select Role (Leave unchanged)</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ (int) old('role_id', $user->role_id) === $role->id ? 'selected' : '' }}>
                                                {{ $role->name }} ({{ ucfirst($role->guard_role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $user->is_active ? 'active' : 'inactive') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $user->is_active ? 'active' : 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold"><i class="bi bi-people-fill text-primary me-1"></i>Concurrent / Multiple Assigned Roles</label>
                                <div class="p-3 border rounded bg-light">
                                    <input type="hidden" name="role_ids_present" value="1">
                                    <small class="text-muted d-block mb-2">Check any additional roles this user holds concurrently (e.g., Lecturer who is also Dean or HOD, Finance Officer who is also Lecturer, Estates Manager who is also Dean):</small>
                                    <div class="row g-2">
                                        @foreach($roles as $r)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $r->id }}" id="role_opt_{{ $r->id }}"
                                                        {{ (is_array(old('role_ids')) && in_array($r->id, old('role_ids'))) || $user->roles->contains('id', $r->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="role_opt_{{ $r->id }}">
                                                        <strong>{{ $r->name }}</strong> <span class="text-muted">({{ ucfirst($r->guard_role) }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center mb-3" role="alert">
                                    <i class="bi bi-shield-lock text-primary me-2 fs-5"></i>
                                    <div>
                                        <strong>Password Management:</strong> Passwords cannot be directly viewed or edited by administrators. The user can reset their own password via the "Forgot Password" link on the login page, or you can send them a reset link from their profile page.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="profile_picture" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                           id="profile_picture" name="profile_picture" accept="image/*">
                                    @if($user->profile_picture)
                                        <small class="text-muted">Current: {{ basename($user->profile_picture) }}</small>
                                    @endif
                                    @error('profile_picture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update User
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
