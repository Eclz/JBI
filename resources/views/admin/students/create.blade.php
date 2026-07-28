@extends('layouts.app')

@section('title', 'Create Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Student</h3>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Students
                    </a>
                </div>

                <form action="{{ route('admin.students.store') }}" method="POST" class="card-body">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Personal Information</h5>

                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Academic Information</h5>

                            <div class="mb-3">
                                <label for="admission_number" class="form-label">Admission Number *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('admission_number') is-invalid @enderror"
                                           id="admission_number" name="admission_number"
                                           value="{{ old('admission_number', $nextAdmissionNumber) }}"
                                           readonly required>
                                    <button type="button" class="btn btn-outline-secondary" id="generateAdmissionNumber"
                                            title="Generate New Admission Number">
                                        <i class="fa fa-sync-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="editAdmissionNumber"
                                            title="Edit Admission Number">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </div>
                                @error('admission_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Format: JBI + Year + 4-digit sequence (e.g., JBI20240001)
                                    <br><small class="text-muted">Click the refresh button to generate a new number or edit button to modify manually.</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department *</label>
                                <select class="form-select @error('department_id') is-invalid @enderror"
                                        id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="program_id" class="form-label">Program *</label>
                                <select class="form-select @error('program_id') is-invalid @enderror"
                                        id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @forelse($programs as $program)
                                        <option value="{{ $program->id }}"
                                                data-department="{{ $program->department_id }}"
                                                {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }} @if($program->level) ({{ $program->level->name }}) @endif
                                        </option>
                                    @empty
                                        <option value="" data-department="all" disabled>No programs available</option>
                                    @endforelse
                                </select>
                                @error('program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($programs->isEmpty())
                                    <div class="form-text text-muted">Create programs in Admin &gt; Program Management.</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <select class="form-select @error('specialization') is-invalid @enderror"
                                        id="specialization" name="specialization">
                                    <option value="">Select Specialization (Optional)</option>
                                    <option value="Biblical Exegesis" {{ old('specialization') == 'Biblical Exegesis' ? 'selected' : '' }}>Biblical Exegesis</option>
                                    <option value="Systematic Theology" {{ old('specialization') == 'Systematic Theology' ? 'selected' : '' }}>Systematic Theology</option>
                                    <option value="Pastoral Ministry" {{ old('specialization') == 'Pastoral Ministry' ? 'selected' : '' }}>Pastoral Ministry</option>
                                    <option value="Youth Ministry" {{ old('specialization') == 'Youth Ministry' ? 'selected' : '' }}>Youth Ministry</option>
                                    <option value="Missions" {{ old('specialization') == 'Missions' ? 'selected' : '' }}>Missions</option>
                                    <option value="Church History" {{ old('specialization') == 'Church History' ? 'selected' : '' }}>Church History</option>
                                    <option value="Christian Education" {{ old('specialization') == 'Christian Education' ? 'selected' : '' }}>Christian Education</option>
                                    <option value="Worship Leadership" {{ old('specialization') == 'Worship Leadership' ? 'selected' : '' }}>Worship Leadership</option>
                                    <option value="Biblical Languages" {{ old('specialization') == 'Biblical Languages' ? 'selected' : '' }}>Biblical Languages</option>
                                    <option value="Apologetics" {{ old('specialization') == 'Apologetics' ? 'selected' : '' }}>Apologetics</option>
                                    <option value="Counseling" {{ old('specialization') == 'Counseling' ? 'selected' : '' }}>Counseling</option>
                                    <option value="Church Planting" {{ old('specialization') == 'Church Planting' ? 'selected' : '' }}>Church Planting</option>
                                </select>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="admission_date" class="form-label">Admission Date *</label>
                                <input type="date" class="form-control @error('admission_date') is-invalid @enderror"
                                       id="admission_date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Student
                            </button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary ms-2">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const admissionNumberInput = document.getElementById('admission_number');
    const generateBtn = document.getElementById('generateAdmissionNumber');
    const editBtn = document.getElementById('editAdmissionNumber');

    // Generate new admission number
    generateBtn.addEventListener('click', function() {
        this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        fetch('{{ route("admin.students.next-admission-number") }}')
            .then(response => response.json())
            .then(data => {
                admissionNumberInput.value = data.admission_number;
                this.innerHTML = '<i class="fa fa-sync-alt"></i>';
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = '<i class="fa fa-sync-alt"></i>';
                alert('Failed to generate admission number. Please try again.');
            });
    });

    // Toggle edit mode for admission number
    editBtn.addEventListener('click', function() {
        if (admissionNumberInput.readOnly) {
            admissionNumberInput.readOnly = false;
            admissionNumberInput.focus();
            admissionNumberInput.select();
            this.innerHTML = '<i class="fa fa-lock"></i>';
            this.title = 'Lock Admission Number';
            admissionNumberInput.classList.add('border-warning');
        } else {
            admissionNumberInput.readOnly = true;
            this.innerHTML = '<i class="fa fa-edit"></i>';
            this.title = 'Edit Admission Number';
            admissionNumberInput.classList.remove('border-warning');
        }
    });
});
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const departmentSelect = document.getElementById('department_id');
    const programSelect = document.getElementById('program_id');

    const updatePrograms = () => {
        const departmentId = departmentSelect.value;
        let hasVisible = false;

        Array.from(programSelect.options).forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const optionDepartment = option.getAttribute('data-department');
            const matches = !departmentId || optionDepartment === departmentId || optionDepartment === 'all';
            option.hidden = !matches;
            option.disabled = !matches;
            if (matches) {
                hasVisible = true;
            }
        });

        if (!hasVisible && departmentId) {
            Array.from(programSelect.options).forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                option.hidden = false;
                option.disabled = false;
            });
            hasVisible = programSelect.options.length > 1;
        }

        if (!hasVisible) {
            programSelect.value = '';
        }
    };

    departmentSelect.addEventListener('change', updatePrograms);
    updatePrograms();
});
</script>
@endpush
