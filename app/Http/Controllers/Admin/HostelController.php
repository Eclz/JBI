<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampusFacility;
use App\Models\HostelAllocation;
use Illuminate\Validation\Rule;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = CampusFacility::where('type', 'hall')->withCount('hostelRooms as rooms_count')->get();
        $allocations = HostelAllocation::with(['user', 'room.hostel', 'semester'])->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.hostel.index', compact('hostels', 'allocations'));
    }

    public function storeHostel(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('campus_facilities', 'name')->where('type', 'hall')],
            'type' => 'required|in:male,female,mixed',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        CampusFacility::create([
            'type' => 'hall',
            'hall_type' => $request->input('type'),
            'name' => $request->input('name'),
            'capacity' => $request->input('capacity'),
            'location' => $request->input('location'),
            'description' => $request->input('description'),
            'is_active' => true,
        ]);
        return back()->with('success', 'Hall of Residence created successfully.');
    }

    public function updateHostel(Request $request, CampusFacility $hostel)
    {
        abort_unless($hostel->type === 'hall', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('campus_facilities', 'name')->where('type', 'hall')->ignore($hostel->id)],
            'type' => ['required', 'in:male,female,mixed'],
            'capacity' => ['required', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $hostel->update([
            'name' => $data['name'],
            'hall_type' => $data['type'],
            'capacity' => $data['capacity'],
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        return back()->with('success', 'Hall of Residence updated successfully.');
    }

    public function editHostel(CampusFacility $hostel)
    {
        abort_unless($hostel->type === 'hall', 404);

        return view('admin.hostel.edit', compact('hostel'));
    }

    public function storeRoom(Request $request, CampusFacility $hostel)
    {
        abort_unless($hostel->type === 'hall', 404);

        $request->validate([
            'room_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'fee_per_semester' => 'required|numeric|min:0'
        ]);

        $hostel->hostelRooms()->create($request->only([
            'room_number',
            'capacity',
            'fee_per_semester',
        ]));
        return back()->with('success', 'Room added to hall successfully.');
    }

    public function approveAllocation(HostelAllocation $allocation)
    {
        if ($allocation->status !== 'pending') {
            return back()->with('error', 'Allocation is not pending.');
        }

        $room = $allocation->room;
        if ($room->occupancy >= $room->capacity) {
            return back()->with('error', 'Room is full.');
        }

        $allocation->update(['status' => 'approved']);
        $room->increment('occupancy');

        return back()->with('success', 'Allocation approved successfully.');
    }

    public function rejectAllocation(HostelAllocation $allocation)
    {
        $allocation->update(['status' => 'rejected']);
        return back()->with('success', 'Allocation rejected successfully.');
    }
}
