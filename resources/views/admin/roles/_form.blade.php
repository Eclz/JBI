@csrf

@if($role->exists)
    @method('PUT')
@endif

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Role Name *</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name', $role->name) }}" {{ $role->is_system ? 'readonly' : '' }} required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="guard_role" class="form-label">University Area *</label>
            <select class="form-select @error('guard_role') is-invalid @enderror" id="guard_role" name="guard_role" {{ $role->is_system ? 'disabled' : '' }} required>
                @foreach(['admin' => 'Administration', 'faculty' => 'Faculty', 'student' => 'Student', 'parent' => 'Parent / Guardian'] as $value => $label)
                    <option value="{{ $value }}" {{ old('guard_role', $role->guard_role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @if($role->is_system)
                <input type="hidden" name="guard_role" value="{{ $role->guard_role }}">
            @endif
            @error('guard_role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="is_active" class="form-label">Status</label>
            <select class="form-select" id="is_active" name="is_active">
                <option value="1" {{ old('is_active', $role->is_active) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !old('is_active', $role->is_active) ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
</div>

<div class="mb-4">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Module Permissions</h5>
        <small class="text-muted">Choose exactly what this role can do in each university module.</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Module</th>
                        @foreach($actions as $actionLabel)
                            <th class="text-center">{{ $actionLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $moduleKey => $moduleLabel)
                        <tr>
                            <td class="fw-medium">{{ $moduleLabel }}</td>
                            @foreach($actions as $actionKey => $actionLabel)
                                @php
                                    $checked = (bool) old("permissions.{$moduleKey}.{$actionKey}", data_get($role->permissions, "{$moduleKey}.{$actionKey}", false));
                                @endphp
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[{{ $moduleKey }}][{{ $actionKey }}]"
                                           value="1" {{ $checked ? 'checked' : '' }}
                                           aria-label="{{ $moduleLabel }} {{ $actionLabel }}">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> {{ $role->exists ? 'Update Role' : 'Create Role' }}
    </button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
