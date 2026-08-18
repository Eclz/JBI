@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Department</h1>
            <p class="mb-0 text-muted">Add a new department to the university</p>
        </div>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Departments
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name" class="required">Department Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}"
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
                                           id="code" name="code" value="{{ old('code') }}"
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
                                      placeholder="Brief description of the department">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="faculty_id">Faculty</label>
                            <select class="form-control @error('faculty_id') is-invalid @enderror"
                                    id="faculty_id" name="faculty_id">
                                <option value="">Select Faculty (Optional)</option>
                                @foreach($faculties as $fac)
                                    <option value="{{ $fac->id }}" {{ old('faculty_id') == $fac->id ? 'selected' : '' }}>
                                        {{ $fac->name }} ({{ $fac->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Associate this department with an academic Faculty</small>
                        </div>

                        <div class="form-group">
                            <label for="head_of_department_id">Head of Department</label>
                            <select class="form-control @error('head_of_department_id') is-invalid @enderror"
                                    id="head_of_department_id" name="head_of_department_id">
                                <option value="">Select Head of Department</option>
                                @foreach($potentialHeads as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('head_of_department_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }} ({{ $faculty->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('head_of_department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Optional - Can be assigned later</small>
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location') }}"
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
                                           id="phone" name="phone" value="{{ old('phone') }}"
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
                                           id="email" name="email" value="{{ old('email') }}"
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
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active Department</label>
                            </div>
                            <small class="form-text text-muted">Active departments are visible to students and faculty</small>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Department
                            </button>
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary ml-2">
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
                    <h6 class="m-0 font-weight-bold text-primary">Guidelines</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Department Name</h6>
                    <p class="text-sm text-muted mb-3">Use the full, official name of the department as it appears in university documents.</p>

                    <h6 class="font-weight-bold">Department Code</h6>
                    <p class="text-sm text-muted mb-3">Use a short, unique code (2-10 characters) that will be used in course codes and references.</p>

                    <h6 class="font-weight-bold">Head of Department</h6>
                    <p class="text-sm text-muted mb-3">Select from existing faculty members. This can be changed later if needed.</p>

                    <h6 class="font-weight-bold">Contact Information</h6>
                    <p class="text-sm text-muted mb-0">Provide department-specific contact details for students and faculty to reach the department office.</p>
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
                            Department codes must be unique across the university
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-info-circle text-info mr-2"></i>
                            Only faculty members can be assigned as department heads
                        </li>
                        <li class="mb-0">
                            <i class="fa fa-check-circle text-success mr-2"></i>
                            All fields except name and code are optional
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
    // Auto-generate department code from name
    $('#name').on('input', function() {
        const name = $(this).val();
        const code = name.replace(/[^a-zA-Z\s]/g, '') // Remove non-letters
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase())
                        .join('')
                        .substring(0, 10);

        if ($('#code').val() === '') {
            $('#code').val(code);
        }
    });

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
});
</script>
@endpush
