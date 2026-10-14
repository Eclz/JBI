<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Facility name <span class="text-danger">*</span></label>
        <input name="name" required maxlength="150" class="form-control" value="{{ old('name', $facility->name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Location</label>
        <input name="location" maxlength="150" class="form-control" value="{{ old('location', $facility->location ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" maxlength="2000" class="form-control" rows="4">{{ old('description', $facility->description ?? '') }}</textarea>
    </div>
    @isset($facility)
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-select" required>
                <option value="1" @selected((string) old('is_active', (int) $facility->is_active) === '1')>Active</option>
                <option value="0" @selected((string) old('is_active', (int) $facility->is_active) === '0')>Inactive</option>
            </select>
        </div>
    @endisset
</div>
