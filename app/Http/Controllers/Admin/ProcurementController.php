<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcurementRequisition;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        $currencyCode = SystemSetting::getSetting('default_currency', 'USD');
        $requisitions = ProcurementRequisition::with(['department', 'requester'])
            ->orderByRaw("FIELD(status, 'Pending', 'Approved', 'Rejected')")
            ->latest()
            ->paginate(15);

        return view('admin.finance.procurement.index', compact('requisitions', 'currencyCode'));
    }

    public function approve(Request $request, ProcurementRequisition $requisition)
    {
        if ($requisition->status !== 'Pending') {
            return back()->withErrors(['error' => 'This requisition has already been processed.']);
        }

        $requisition->update([
            'status' => 'Approved'
        ]);

        return back()->with('success', 'Requisition approved successfully.');
    }

    public function reject(Request $request, ProcurementRequisition $requisition)
    {
        if ($requisition->status !== 'Pending') {
            return back()->withErrors(['error' => 'This requisition has already been processed.']);
        }

        $requisition->update([
            'status' => 'Rejected'
        ]);

        return back()->with('success', 'Requisition rejected.');
    }
}
