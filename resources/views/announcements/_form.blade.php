@php
    $a = $announcement ?? null;
    $selectedRoles = old('target_roles', $a?->target_roles ?? []);
@endphp

<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $a?->title) }}" maxlength="255" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label for="content" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea id="content" name="content" rows="7" class="form-control @error('content') is-invalid @enderror"
                          required>{{ old('content', $a?->content) }}</textarea>
                <div class="form-text">Minimum 20 characters (50 for urgent announcements).</div>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                    @foreach(['general' => 'General', 'academic' => 'Academic', 'administrative' => 'Administrative', 'event' => 'Event', 'emergency' => 'Emergency'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $a?->type ?? 'general') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                    @foreach(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', $a?->priority ?? 'normal') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="form-text">Emergency announcements must be Urgent.</div>
                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label for="expires_at" class="form-label">Expires</label>
                <input type="datetime-local" id="expires_at" name="expires_at"
                       class="form-control @error('expires_at') is-invalid @enderror"
                       value="{{ old('expires_at', $a?->expires_at?->format('Y-m-d\TH:i')) }}">
                @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label for="course_id" class="form-label">Course <span class="text-muted">(required for academic type)</span></label>
                <select id="course_id" name="course_id" class="form-select @error('course_id') is-invalid @enderror">
                    <option value="">— None —</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) old('course_id', $a?->course_id) === (string) $course->id)>
                            {{ $course->code }} - {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label for="department_id" class="form-label">Department</label>
                <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                    <option value="">— All departments —</option>
                    @foreach($departmentOptions as $department)
                        <option value="{{ $department->id }}" @selected((string) old('department_id', $a?->department_id) === (string) $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label d-block">Audience</label>
                @foreach($roleOptions as $value => $label)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="target_roles[]" id="role_{{ $value }}"
                               value="{{ $value }}" @checked(in_array($value, $selectedRoles, true))>
                        <label class="form-check-label" for="role_{{ $value }}">{{ $label }}</label>
                    </div>
                @endforeach
                <div class="form-text">Leave all unticked to use the default audience for the chosen type.</div>
                @error('target_roles')<div class="text-danger small">{{ $message }}</div>@enderror
                @error('target_roles.*')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <input type="hidden" name="is_published" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1"
                           @checked(old('is_published', $a?->is_published ?? true))>
                    <label class="form-check-label" for="is_published">Publish immediately</label>
                </div>
                <input type="hidden" name="send_email" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="send_email" name="send_email" value="1"
                           @checked(old('send_email', $a?->send_email ?? false))>
                    <label class="form-check-label" for="send_email">Also send by email</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>{{ $a ? 'Save Changes' : 'Create Announcement' }}
        </button>
    </div>
</div>
