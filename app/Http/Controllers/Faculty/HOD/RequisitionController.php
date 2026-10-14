<?php

namespace App\Http\Controllers\Faculty\HOD;

use App\Http\Controllers\Controller;
use App\Models\ProcurementRequisition;
use App\Models\Department;
use Illuminate\Http\Request;

class RequisitionController extends Controller
{
    private function getDepartment()
    {
        return Department::where('head_of_department_id', auth()->id())->firstOrFail();
    }

    public function index()
    {
        $department = $this->getDepartment();
        $requisitions = ProcurementRequisition::where('department_id', $department->id)
            ->latest()
            ->paginate(15);

        return view('faculty.hod.requisitions.index', compact('requisitions', 'department'));
    }

    public function store(Request $request)
    {
        $department = $this->getDepartment();

        $request->validate([
            'item_description' => 'required|string|max:500',
            'quantity' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:0',
        ]);

        ProcurementRequisition::create([
            'requisition_number' => 'REQ-' . strtoupper(uniqid()),
            'department_id' => $department->id,
            'item_description' => $request->item_description,
            'quantity' => $request->quantity,
            'estimated_cost' => $request->estimated_cost,
            'status' => 'Pending',
            'requested_by' => auth()->id(),
        ]);

        return back()->with('success', 'Requisition submitted successfully and is awaiting finance approval.');
    }
}
