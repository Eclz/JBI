@extends('layouts.app')

@section('title', 'Edit Department - ' . $department->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Department</h1>
            <p class="mb-0 text-muted">Update department information</p>
        </div>
        <div>
            <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-info">
                <i class="fa fa-eye"></i> View Department
            </a>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name" class="required">Department Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $department->name) }}"
                                           placeholder="e.g., Computer Science" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code" class="required">Department Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code', $department->code) }}"
                                           placeholder="e.g., CS" maxlength="10" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 10 characters</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Brief description of the department">{{ old('description', $department->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="head_of_department_id">Head of Department</label>
                            <select class="form-control @error('head_of_department_id') is-invalid @enderror"
                                    id="head_of_department_id" name="head_of_department_id">
                                <option value="">Select Head of Department</option>
                                @foreach($potentialHeads as $faculty)
                                    <option value="{{ $faculty->id }}"
                                            {{ old('head_of_department_id', $department->head_of_department_id) == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }} ({{ $faculty->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('head_of_department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Optional - Can be left unassigned</small>
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location', $department->location) }}"
                                   placeholder="e.g., Building A, Floor 3">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', $department->phone) }}"
                                           placeholder="e.g., +1234567890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $department->email) }}"
                                           placeholder="e.g., cs@university.edu">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active Department</label>
                            </div>
                            <small class="form-text text-muted">Active departments are visible to students and faculty</small>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Department
                            </button>
                            <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-secondary ml-2">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Current Name:</strong><br>
                        {{ $department->name }}
                    </div>

                    <div class="mb-3">
                        <strong>Current Code:</strong><br>
                        {{ $department->code }}
                    </div>

                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}">
                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <small class="text-muted">{{ $department->created_at->format('M d, Y') }}</small>
                    </div>

                    <div class="mb-0">
                        <strong>Last Updated:</strong><br>
                        <small class="text-muted">{{ $department->updated_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Important Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fa fa-exclamation-triangle text-warning mr-2"></i>
                            Changing the department code may affect course codes
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-info-circle text-info mr-2"></i>
                            Deactivating will hide the department from students
                        </li>
                        <li class="mb-0">
                            <i class="fa fa-users text-primary mr-2"></i>
                            This department has {{ $department->facultyMembers->count() }} faculty members
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Character counter for description
    $('#description').on('input', function() {
        const maxLength = 1000;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;

        let counterText = `${currentLength}/${maxLength} characters`;
        if (remaining < 100) {
            counterText = `<span class="text-warning">${counterText}</span>`;
        }
        if (remaining < 0) {
            counterText = `<span class="text-danger">${counterText}</span>`;
        }

        $(this).siblings('.form-text').html(counterText);
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;

        // Check required fields
        $('input[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    // Confirm changes if department has dependencies
    const facultyCount = {{ $department->facultyMembers->count() }};
    const courseCount = {{ $department->courses->count() }};

    if (facultyCount > 0 || courseCount > 0) {
        $('form').on('submit', function(e) {
            const nameChanged = $('#name').val() !== '{{ $department->name }}';
            const codeChanged = $('#code').val() !== '{{ $department->code }}';
            const statusChanged = $('#is_active').prop('checked') !== {{ $department->is_active ? 'true' : 'false' }};

            if ((nameChanged || codeChanged || statusChanged) && !confirm('This department has ' + facultyCount + ' faculty members and ' + courseCount + ' courses. Are you sure you want to make these changes?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
