<div class="row g-3 mb-4">
    <div class="col-md-6"><label class="form-label">Room name</label><input name="name" required class="form-control" value="{{ old('name', $room->name ?? '') }}"></div>
    <div class="col-md-6"><label class="form-label">Building / branch</label><input name="building" class="form-control" value="{{ old('building', $room->building ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Room type</label><input name="room_type" required class="form-control" value="{{ old('room_type', $room->room_type ?? 'General') }}"></div>
    <div class="col-md-4"><label class="form-label">Capacity</label><input type="number" min="1" name="capacity" required class="form-control" value="{{ old('capacity', $room->capacity ?? 1) }}"></div>
    <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['Available','Booked','Maintenance','Retired'] as $status)<option @selected(old('status', $room->status ?? 'Available') === $status)>{{ $status }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="4">{{ old('notes', $room->notes ?? '') }}</textarea></div>
</div>
