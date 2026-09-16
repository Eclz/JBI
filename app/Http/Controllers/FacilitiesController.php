<?php

namespace App\Http\Controllers;

use App\Models\FacilityRoom;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitiesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('facilities.index', [
            'rooms' => FacilityRoom::where('status', '!=', 'Retired')->count(),
            'bookings' => 0,
            'maintenance' => FacilityRoom::where('status', 'Maintenance')->count(),
            'canManage' => $user ? ($user->isFacilitiesStaff() || $user->isAdmin()) : false,
        ]);
    }

    public function roomsIndex()
    {
        $rooms = FacilityRoom::latest()->get();
        return view('facilities.rooms', compact('rooms'));
    }

    public function bookingsIndex()
    {
        return view('facilities.bookings');
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'building' => 'nullable|string|max:150',
            'room_type' => 'required|string|max:80',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:Available,Booked,Maintenance,Retired',
            'notes' => 'nullable|string|max:2000',
        ]);
        $room = FacilityRoom::create($data);
        $this->notify('Facility room created', "{$room->name} was added to the facilities register.", 'facilities.rooms.index');

        return redirect()->route('facilities.rooms.index')->with('success', 'Facility room created successfully.');
    }

    public function edit(FacilityRoom $room)
    {
        return view('facilities.edit', compact('room'));
    }

    public function update(Request $request, FacilityRoom $room)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'building' => 'nullable|string|max:150',
            'room_type' => 'required|string|max:80',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:Available,Booked,Maintenance,Retired',
            'notes' => 'nullable|string|max:2000',
        ]);
        $room->update($data);
        $this->notify('Facility room updated', "{$room->name} was updated in the facilities register.", 'facilities.rooms.index');

        return redirect()->route('facilities.rooms.index')->with('success', 'Facility room updated successfully.');
    }

    public function destroy(FacilityRoom $room)
    {
        $name = $room->name;
        $room->delete();
        $this->notify('Facility room removed', "{$name} was removed from the facilities register.", 'facilities.rooms.index');

        return redirect()->route('facilities.rooms.index')->with('success', 'Facility room deleted successfully.');
    }

    private function notify(string $title, string $message, string $route): void
    {
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'facilities',
            'title' => $title,
            'message' => $message,
            'action_url' => route($route),
            'priority' => 'normal',
        ]);
    }
}
