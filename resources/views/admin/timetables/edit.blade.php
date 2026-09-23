@extends('layouts.app')

@section('title', 'Edit Timetable Entry')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Edit Timetable Entry</h2>
            <p class="text-muted mb-0">Modify the scheduled class or exam session.</p>
        </div>
        <a href="{{ route('admin.timetables.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.timetables.update', $timetable) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-select select2 @error('course_id') is-invalid @enderror" data-placeholder="-- Select Course --" required>
                                    <option value=""></option>
                                    @foreach($courses as $c)
                                        <option value="{{ $c->id }}" {{ old('course_id', $timetable->course_id) == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Programme</label>
                                <select name="program_id" class="form-select @error('program_id') is-invalid @enderror">
                                    <option value="">-- All Programmes --</option>
                                    @foreach($programs as $p)
                                        <option value="{{ $p->id }}" {{ old('program_id', $timetable->program_id) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Timetable Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="teaching" {{ old('type', $timetable->type) == 'teaching' ? 'selected' : '' }}>Teaching Timetable</option>
                                    <option value="tests" {{ old('type', $timetable->type) == 'tests' ? 'selected' : '' }}>Tests Timetable</option>
                                    <option value="exams" {{ old('type', $timetable->type) == 'exams' ? 'selected' : '' }}>Exams Timetable</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Year of Study <span class="text-danger">*</span></label>
                                <select name="year_of_study" class="form-select" required>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('year_of_study', $timetable->year_of_study) == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Semester Number <span class="text-danger">*</span></label>
                                <select name="semester_number" class="form-select" required>
                                    <option value="1" {{ old('semester_number', $timetable->semester_number) == 1 ? 'selected' : '' }}>Semester I</option>
                                    <option value="2" {{ old('semester_number', $timetable->semester_number) == 2 ? 'selected' : '' }}>Semester II</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Day of Week <span class="text-danger">*</span></label>
                                <select name="day_of_week" class="form-select" required>
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <option value="{{ $day }}" {{ old('day_of_week', $timetable->day_of_week) == $day ? 'selected' : '' }}>{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                                <input type="text" name="start_time" class="form-control" placeholder="e.g. 08:00" value="{{ old('start_time', $timetable->start_time) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                                <input type="text" name="end_time" class="form-control" placeholder="e.g. 10:00" value="{{ old('end_time', $timetable->end_time) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Room / Venue <span class="text-danger">*</span></label>
                                <select name="room_venue" class="form-select select2" data-placeholder="-- Select Room/Venue --" required>
                                    <option value=""></option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->name }}" {{ old('room_venue', $timetable->room_venue) == $room->name ? 'selected' : '' }}>{{ $room->name }} ({{ $room->building }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lecturer / Faculty Member</label>
                                <select name="faculty_id" class="form-select select2" data-placeholder="-- Unassigned --">
                                    <option value=""></option>
                                    @foreach($facultyMembers as $f)
                                        <option value="{{ $f->id }}" {{ old('faculty_id', $timetable->faculty_id) == $f->id ? 'selected' : '' }}>{{ $f->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes / Instructions</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes for students">{{ old('notes', $timetable->notes) }}</textarea>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <a href="{{ route('admin.timetables.index') }}" class="btn btn-light px-4 me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-5 fw-bold">Update Timetable</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
