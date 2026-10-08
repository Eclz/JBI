<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampusFacility;
use App\Models\HostelRoom;
use App\Models\HostelAllocation;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class HostelController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $currentSemester = Semester::where('is_current', true)->first();
        
        $allocation = HostelAllocation::where('user_id', $student->id)
            ->with(['room.hostel', 'semester'])
            ->orderBy('created_at', 'desc')
            ->first();

        $hostels = CampusFacility::where('type', 'hall')
            ->where('is_active', true)
            ->with(['hostelRooms' => function ($q) {
                $q->where('status', 'available')->whereColumn('occupancy', '<', 'capacity');
            }])
            ->get();

        return view('student.hostel.index', compact('student', 'allocation', 'hostels', 'currentSemester'));
    }

    public function requestRoom(Request $request)
    {
        $request->validate([
            'hostel_room_id' => 'required|exists:hostel_rooms,id'
        ]);

        $student = Auth::user();
        $currentSemester = Semester::where('is_current', true)->first();
        
        if (!$currentSemester) {
            return back()->with('error', 'No active semester found. Cannot allocate room.');
        }

        $existingAllocation = HostelAllocation::where('user_id', $student->id)
            ->where('semester_id', $currentSemester->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingAllocation) {
            return back()->with('error', 'You already have a pending or approved allocation for this semester.');
        }

        $room = HostelRoom::findOrFail($request->hostel_room_id);
        if ($room->occupancy >= $room->capacity) {
            return back()->with('error', 'This room is currently full.');
        }

        HostelAllocation::create([
            'user_id' => $student->id,
            'hostel_room_id' => $room->id,
            'semester_id' => $currentSemester->id,
            'allocation_date' => now(),
            'status' => 'pending'
        ]);

        return redirect()->route('student.hostel.index')->with('success', 'Room request submitted successfully. Awaiting administration approval.');
    }
}
