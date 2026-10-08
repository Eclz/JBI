<?php

namespace App\Http\Controllers;

use App\Models\FacilityRoom;
use App\Models\CampusFacility;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FacilitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isStudent()) {
                abort(403, 'Students do not have access to the Estates & Facilities module.');
            }
            return $next($request);
        });
    }
    public function index()
    {
        $user = Auth::user();

        $upcomingBookings = \App\Models\FacilityBooking::with(['facilityRoom', 'user'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        return view('facilities.index', [
            'rooms' => FacilityRoom::where('status', '!=', 'Retired')->count(),
            'bookings' => \App\Models\FacilityBooking::where('start_time', '>=', now())->count(),
            'maintenance' => FacilityRoom::where('status', 'Maintenance')->count(),
            'canManage' => $user ? ($user->isFacilitiesStaff() || $user->isAdmin() || $user->hasPermission('facilities', 'view')) : false,
            'upcomingBookings' => $upcomingBookings,
        ]);
    }

    public function roomsIndex()
    {
        $rooms = FacilityRoom::with('campusFacility')->latest()->get();
        return view('facilities.rooms', compact('rooms'));
    }

    public function facilitiesIndex()
    {
        $facilities = CampusFacility::withCount(['rooms', 'hostelRooms'])->orderBy('name')->get();

        return view('facilities.facilities.index', compact('facilities'));
    }

    public function createFacility()
    {
        return view('facilities.facilities.create');
    }

    public function storeFacility(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('campus_facilities', 'name')->where('type', 'facility')],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        CampusFacility::create($data + ['type' => 'facility']);

        return redirect()->route('facilities.buildings.index')->with('success', 'Facility created successfully.');
    }

    public function editFacility(CampusFacility $facility)
    {
        abort_unless($facility->type === 'facility', 404);

        return view('facilities.facilities.edit', compact('facility'));
    }

    public function updateFacility(Request $request, CampusFacility $facility)
    {
        abort_unless($facility->type === 'facility', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('campus_facilities', 'name')->where('type', 'facility')->ignore($facility->id)],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $facility->update($data);

        return redirect()->route('facilities.buildings.index')->with('success', 'Facility updated successfully.');
    }

    public function bookingsIndex()
    {
        $user = Auth::user();
        $query = \App\Models\FacilityBooking::with(['facilityRoom', 'user'])->latest();
        
        if (!($user->hasPermission('facilities', 'view') || $user->isFacilitiesStaff() || $user->isAdmin())) {
            $query->where('user_id', $user->id);
        }

        $bookings = $query->paginate(15);
        $rooms = FacilityRoom::where('status', '!=', 'Retired')->get();
        return view('facilities.bookings', compact('bookings', 'rooms'));
    }

    public function showBooking(\App\Models\FacilityBooking $booking)
    {
        $booking->load(['facilityRoom', 'user']);
        return response()->json($booking);
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'facility_room_id' => 'required|exists:facility_rooms,id',
            'title' => 'required|string|max:150',
            'purpose' => 'required|string',
            'booking_type' => 'required|in:Room Booking,Maintenance',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string',
        ]);
        
        $data['user_id'] = Auth::id();
        $data['status'] = 'Pending';
        
        \App\Models\FacilityBooking::create($data);
        
        $this->notify('Facility booking requested', "Your booking request for {$data['title']} has been submitted.", 'facilities.bookings.index');
        
        return redirect()->route('facilities.bookings.index')->with('success', 'Booking requested successfully.');
    }

    public function updateBooking(Request $request, \App\Models\FacilityBooking $booking)
    {
        $user = Auth::user();
        if (!($user->hasPermission('facilities', 'view') || $user->isFacilitiesStaff() || $user->isAdmin())) {
            if ($booking->user_id !== $user->id) {
                abort(403);
            }
            $data = $request->validate([
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'notes' => 'nullable|string',
            ]);
        } else {
            $data = $request->validate([
                'status' => 'required|in:Pending,Approved,Assigned,In Progress,Completed,Cancelled',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'notes' => 'nullable|string',
            ]);
        }
        
        $booking->update($data);
        
        return redirect()->route('facilities.bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroyBooking(\App\Models\FacilityBooking $booking)
    {
        $user = Auth::user();
        if (!($user->hasPermission('facilities', 'view') || $user->isFacilitiesStaff() || $user->isAdmin())) {
            if ($booking->user_id !== $user->id) {
                abort(403);
            }
        }
        $booking->delete();
        return redirect()->route('facilities.bookings.index')->with('success', 'Booking deleted successfully.');
    }

    public function create()
    {
        $facilities = CampusFacility::where('is_active', true)->orderBy('name')->get();
        return view('facilities.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'campus_facility_id' => 'nullable|exists:campus_facilities,id',
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
        $facilities = CampusFacility::where('is_active', true)
            ->when($room->campus_facility_id, function ($query) use ($room) {
                $query->orWhere('id', $room->campus_facility_id);
            })
            ->orderBy('name')
            ->get();
        return view('facilities.edit', compact('room', 'facilities'));
    }

    public function update(Request $request, FacilityRoom $room)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'campus_facility_id' => 'nullable|exists:campus_facilities,id',
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
