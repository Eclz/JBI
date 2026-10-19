<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampusFacility;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\FinanceRevenue;
use App\Models\Notification;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\SystemSetting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = CampusFacility::where('type', 'hall')->withCount('hostelRooms as rooms_count')->get();
        $allocations = HostelAllocation::with(['user', 'room.hostel', 'semester'])->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.hostel.index', compact('hostels', 'allocations'));
    }

    public function showHostel(CampusFacility $hostel)
    {
        abort_unless($hostel->type === 'hall', 404);

        $rooms = $hostel->hostelRooms()->with(['allocations' => function($q) {
            $q->where('status', 'approved')->with('user');
        }])->orderBy('room_number')->get();

        $totalCapacity = $rooms->sum('capacity');
        $totalOccupancy = $rooms->sum('occupancy');
        $availableSpots = max(0, $totalCapacity - $totalOccupancy);
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');

        return view('admin.hostel.show', compact('hostel', 'rooms', 'totalCapacity', 'totalOccupancy', 'availableSpots', 'currencyCode'));
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

        DB::beginTransaction();
        try {
            $allocation->update(['status' => 'approved']);
            $room->increment('occupancy');

            $student = $allocation->user;
            $hostel = $room->hostel ?: $room->campusFacility;
            $feeAmount = (float) $room->fee_per_semester;
            $currencyCode = SystemSetting::getSetting('default_currency', 'USD');

            if ($feeAmount > 0) {
                // Determine relevant semester and academic year
                $semester = $allocation->semester 
                    ?? Semester::where('is_current', true)->first() 
                    ?? Semester::first();
                $academicYearId = $semester?->academic_year_id 
                    ?: (AcademicYear::where('is_active', true)->value('id') ?: AcademicYear::first()?->id);

                if (!$academicYearId) {
                    $year = AcademicYear::create([
                        'name' => date('Y') . '/' . (date('Y') + 1),
                        'year' => date('Y') . '/' . (date('Y') + 1),
                        'start_date' => now()->startOfYear(),
                        'end_date' => now()->endOfYear(),
                        'is_active' => true,
                        'is_current' => true,
                    ]);
                    $academicYearId = $year->id;
                }

                // 1. Get or create FeeStructure for Hall Accommodation
                $feeStructure = FeeStructure::firstOrCreate(
                    [
                        'name' => 'Hall of Residence Accommodation Fee',
                        'type' => 'other',
                        'frequency' => 'semester',
                        'academic_year_id' => $academicYearId,
                        'semester_id' => $semester?->id,
                    ],
                    [
                        'amount' => $feeAmount,
                        'description' => 'Official student accommodation charge for university halls of residence.',
                        'is_mandatory' => false,
                        'is_active' => true,
                        'due_date' => now()->addDays(30),
                    ]
                );

                $invoiceNumber = 'INV-HALL-' . date('Ymd') . '-' . str_pad($allocation->id, 5, '0', STR_PAD_LEFT);
                $txnRef = 'TXN-HALL-' . date('Ymd') . '-' . str_pad($allocation->id, 5, '0', STR_PAD_LEFT);

                // 2. Create FeeRecord (Official Student Invoice)
                $feeRecord = FeeRecord::where('invoice_number', $invoiceNumber)->first();
                if (!$feeRecord) {
                    $feeRecord = FeeRecord::create([
                        'user_id' => $allocation->user_id,
                        'fee_structure_id' => $feeStructure->id,
                        'invoice_number' => $invoiceNumber,
                        'amount' => $feeAmount,
                        'discount_amount' => 0,
                        'late_fee' => 0,
                        'total_amount' => $feeAmount,
                        'paid_amount' => 0,
                        'balance_amount' => $feeAmount,
                        'type' => 'accommodation',
                        'status' => 'pending',
                        'due_date' => now()->addDays(30),
                        'payment_notes' => "Accommodation charge for " . ($hostel?->name ?? 'Hall') . " - Room {$room->room_number} (Semester: " . ($semester?->name ?? 'Current') . ")",
                        'processed_by' => auth()->id(),
                    ]);
                }

                // 3. Create Payment Transaction (Reflected on the Transactions side of Finance)
                $payment = Payment::where('reference_number', $invoiceNumber)
                    ->orWhere('transaction_id', $txnRef)
                    ->first();
                if (!$payment) {
                    Payment::create([
                        'fee_record_id' => $feeRecord->id,
                        'student_id' => $allocation->user_id,
                        'amount' => $feeAmount,
                        'payment_method' => 'bank_transfer',
                        'transaction_id' => $txnRef,
                        'reference_number' => $invoiceNumber,
                        'notes' => "Hall Accommodation Allocation: " . ($hostel?->name ?? 'Hall') . " Room {$room->room_number}",
                        'status' => 'pending',
                        'processed_by' => auth()->id(),
                        'payment_date' => now(),
                    ]);
                }

                // 4. Create FinanceRevenue stream entry in Finance Hub
                $revenue = FinanceRevenue::where('reference_number', $invoiceNumber)->first();
                if (!$revenue) {
                    FinanceRevenue::create([
                        'revenue_code' => 'REV-' . date('Ymd') . '-' . rand(100, 999),
                        'category' => 'Accommodation & Halls',
                        'title' => 'Hall Accommodation Fee - ' . ($student ? $student->name : 'Student #' . $allocation->user_id),
                        'amount' => $feeAmount,
                        'transaction_date' => now()->toDateString(),
                        'payment_method' => 'bank_transfer',
                        'reference_number' => $invoiceNumber,
                        'payer_name' => $student ? $student->name : 'Student #' . $allocation->user_id,
                        'notes' => ($hostel?->name ?? 'Hall') . " - Room {$room->room_number} (Semester: " . ($semester?->name ?? 'Current') . ")",
                        'received_by' => auth()->id(),
                    ]);
                }

                // 5. Send Invoice Notification to Student
                Notification::create([
                    'user_id' => $allocation->user_id,
                    'type' => 'invoice',
                    'title' => 'Room Allocation Approved - Invoice Issued',
                    'message' => "Your room allocation request for {$hostel?->name} (Room {$room->room_number}) has been approved. An accommodation invoice #{$invoiceNumber} of {$currencyCode} " . number_format($feeAmount, 2) . " has been issued to your fees account.",
                    'action_url' => route('student.fees.index'),
                    'priority' => 'high',
                ]);
            } else {
                // Free room notification
                Notification::create([
                    'user_id' => $allocation->user_id,
                    'type' => 'hostel',
                    'title' => 'Hall Room Allocation Approved',
                    'message' => "Your room allocation request for " . ($hostel?->name ?? 'Hall') . " (Room {$room->room_number}) has been approved.",
                    'action_url' => route('student.hostel.index'),
                    'priority' => 'normal',
                ]);
            }

            DB::commit();
            return back()->with('success', 'Allocation approved successfully.' . ($feeAmount > 0 ? ' Invoice and finance transaction recorded.' : ''));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error approving hostel allocation: ' . $e->getMessage(), [
                'allocation_id' => $allocation->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to approve allocation: ' . $e->getMessage());
        }
    }

    public function rejectAllocation(HostelAllocation $allocation)
    {
        $allocation->update(['status' => 'rejected']);
        return back()->with('success', 'Allocation rejected successfully.');
    }
}
