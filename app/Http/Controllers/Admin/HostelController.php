<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelAllocation;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = Hostel::withCount('rooms')->get();
        $allocations = HostelAllocation::with(['user', 'room.hostel', 'semester'])->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.hostel.index', compact('hostels', 'allocations'));
    }

    public function storeHostel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:male,female,mixed',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string'
        ]);

        Hostel::create($request->all());
        return back()->with('success', 'Hall of Residence created successfully.');
    }

    public function storeRoom(Request $request, Hostel $hostel)
    {
        $request->validate([
            'room_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'fee_per_semester' => 'required|numeric|min:0'
        ]);

        $hostel->rooms()->create($request->all());
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
