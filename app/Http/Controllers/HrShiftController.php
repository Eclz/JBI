<?php

namespace App\Http\Controllers;

use App\Models\HrShift;
use App\Models\HrShiftAssignment;
use Illuminate\Http\Request;

class HrShiftController extends Controller
{
    public function storeShift(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'required|integer|min:0',
        ]);

        HrShift::create($data);

        return back()->with('success', 'Shift created successfully.');
    }

    public function assignShift(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hr_shift_id' => 'required|exists:hr_shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        // In a more robust system, we would check for overlapping shift assignments here.
        HrShiftAssignment::create([
            'user_id' => $request->user_id,
            'hr_shift_id' => $request->hr_shift_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Shift assigned to employee successfully.');
    }
}
