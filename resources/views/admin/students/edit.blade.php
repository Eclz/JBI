@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Student: {{ $student->first_name }} {{ $student->last_name }}</h3>
                    <div>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-info">
                            <i class="fa fa-eye"></i> View Student
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Students
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.students.update', $student) }}" method="POST" class="card-body">
                    @csrf
                    @method('PUT')

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
                                       id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $student->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
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
                                <input type="text" class="form-control @error('admission_number') is-invalid @enderror"
                                       id="admission_number" name="admission_number"
                                       value="{{ old('admission_number', $student->studentProfile?->admission_number ?? '') }}" required>
                                @error('admission_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department *</label>
                                <select class="form-select @error('department_id') is-invalid @enderror"
                                        id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                {{ old('department_id', $student->studentProfile?->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="program" class="form-label">Program *</label>
                                <select class="form-select @error('program') is-invalid @enderror"
                                        id="program" name="program" required>
                                    <option value="">Select Program</option>
                                    <option value="Bachelor of Arts in Biblical Studies" {{ old('program', $student->studentProfile?->program) == 'Bachelor of Arts in Biblical Studies' ? 'selected' : '' }}>Bachelor of Arts in Biblical Studies</option>
                                    <option value="Bachelor of Arts in Theology" {{ old('program', $student->studentProfile?->program) == 'Bachelor of Arts in Theology' ? 'selected' : '' }}>Bachelor of Arts in Theology</option>
                                    <option value="Bachelor of Arts in Christian Ministry" {{ old('program', $student->studentProfile?->program) == 'Bachelor of Arts in Christian Ministry' ? 'selected' : '' }}>Bachelor of Arts in Christian Ministry</option>
                                    <option value="Bachelor of Science in Christian Education" {{ old('program', $student->studentProfile?->program) == 'Bachelor of Science in Christian Education' ? 'selected' : '' }}>Bachelor of Science in Christian Education</option>
                                    <option value="Master of Divinity" {{ old('program', $student->studentProfile?->program) == 'Master of Divinity' ? 'selected' : '' }}>Master of Divinity</option>
                                    <option value="Master of Arts in Biblical Studies" {{ old('program', $student->studentProfile?->program) == 'Master of Arts in Biblical Studies' ? 'selected' : '' }}>Master of Arts in Biblical Studies</option>
                                    <option value="Master of Arts in Theology" {{ old('program', $student->studentProfile?->program) == 'Master of Arts in Theology' ? 'selected' : '' }}>Master of Arts in Theology</option>
                                    <option value="Master of Arts in Christian Ministry" {{ old('program', $student->studentProfile?->program) == 'Master of Arts in Christian Ministry' ? 'selected' : '' }}>Master of Arts in Christian Ministry</option>
                                    <option value="Doctor of Ministry" {{ old('program', $student->studentProfile?->program) == 'Doctor of Ministry' ? 'selected' : '' }}>Doctor of Ministry</option>
                                    <option value="Certificate in Biblical Studies" {{ old('program', $student->studentProfile?->program) == 'Certificate in Biblical Studies' ? 'selected' : '' }}>Certificate in Biblical Studies</option>
                                    <option value="Certificate in Christian Ministry" {{ old('program', $student->studentProfile?->program) == 'Certificate in Christian Ministry' ? 'selected' : '' }}>Certificate in Christian Ministry</option>
                                </select>
                                @error('program')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <select class="form-select @error('specialization') is-invalid @enderror"
                                        id="specialization" name="specialization">
                                    <option value="">Select Specialization (Optional)</option>
                                    <option value="Biblical Exegesis" {{ old('specialization', $student->studentProfile?->specialization) == 'Biblical Exegesis' ? 'selected' : '' }}>Biblical Exegesis</option>
                                    <option value="Systematic Theology" {{ old('specialization', $student->studentProfile?->specialization) == 'Systematic Theology' ? 'selected' : '' }}>Systematic Theology</option>
                                    <option value="Pastoral Ministry" {{ old('specialization', $student->studentProfile?->specialization) == 'Pastoral Ministry' ? 'selected' : '' }}>Pastoral Ministry</option>
                                    <option value="Youth Ministry" {{ old('specialization', $student->studentProfile?->specialization) == 'Youth Ministry' ? 'selected' : '' }}>Youth Ministry</option>
                                    <option value="Missions" {{ old('specialization', $student->studentProfile?->specialization) == 'Missions' ? 'selected' : '' }}>Missions</option>
                                    <option value="Church History" {{ old('specialization', $student->studentProfile?->specialization) == 'Church History' ? 'selected' : '' }}>Church History</option>
                                    <option value="Christian Education" {{ old('specialization', $student->studentProfile?->specialization) == 'Christian Education' ? 'selected' : '' }}>Christian Education</option>
                                    <option value="Worship Leadership" {{ old('specialization', $student->studentProfile?->specialization) == 'Worship Leadership' ? 'selected' : '' }}>Worship Leadership</option>
                                    <option value="Biblical Languages" {{ old('specialization', $student->studentProfile?->specialization) == 'Biblical Languages' ? 'selected' : '' }}>Biblical Languages</option>
                                    <option value="Apologetics" {{ old('specialization', $student->studentProfile?->specialization) == 'Apologetics' ? 'selected' : '' }}>Apologetics</option>
                                    <option value="Counseling" {{ old('specialization', $student->studentProfile?->specialization) == 'Counseling' ? 'selected' : '' }}>Counseling</option>
                                    <option value="Church Planting" {{ old('specialization', $student->studentProfile?->specialization) == 'Church Planting' ? 'selected' : '' }}>Church Planting</option>
                                </select>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="admission_date" class="form-label">Admission Date *</label>
                                <input type="date" class="form-control @error('admission_date') is-invalid @enderror"
                                       id="admission_date" name="admission_date"
                                       value="{{ old('admission_date', $student->studentProfile?->admission_date?->format('Y-m-d') ?? '') }}" required>
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $student->studentProfile?->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $student->studentProfile?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="graduated" {{ old('status', $student->studentProfile?->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="suspended" {{ old('status', $student->studentProfile?->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="dropped" {{ old('status', $student->studentProfile?->status) == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <strong>Current Academic Info:</strong><br>
                                Current Semester: {{ $student->studentProfile?->current_semester ?? 'N/A' }}<br>
                                Total Credits Earned: {{ $student->studentProfile?->total_credits_earned ?? 0 }}<br>
                                Current GPA: {{ number_format($student->studentProfile?->current_gpa ?? 0, 2) }}<br>
                                Cumulative GPA: {{ number_format($student->studentProfile?->cumulative_gpa ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Student
                            </button>
                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary ms-2">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
