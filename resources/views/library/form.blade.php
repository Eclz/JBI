<div class="row g-3 mb-4">
    <div class="col-md-8"><label class="form-label">Title</label><input name="title" required class="form-control" value="{{ old('title', $item->title ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Category</label><input name="category" required class="form-control" value="{{ old('category', $item->category ?? 'Textbook') }}"></div>
    <div class="col-md-6"><label class="form-label">Author</label><input name="author" class="form-control" value="{{ old('author', $item->author ?? '') }}"></div>
    <div class="col-md-6"><label class="form-label">ISBN</label><input name="isbn" class="form-control" value="{{ old('isbn', $item->isbn ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Total copies</label><input type="number" min="1" name="total_copies" required class="form-control" value="{{ old('total_copies', $item->total_copies ?? 1) }}"></div>
    @if(isset($item))
        <div class="col-md-4"><label class="form-label">Available copies</label><input type="number" min="0" name="available_copies" required class="form-control" value="{{ old('available_copies', $item->available_copies) }}"></div>
        <div class="col-md-4"><label class="form-label">Status</label><select name="is_active" class="form-select"><option value="1" @selected(old('is_active', $item->is_active))>Active</option><option value="0" @selected(!old('is_active', $item->is_active))>Archived</option></select></div>
    @endif
</div>
