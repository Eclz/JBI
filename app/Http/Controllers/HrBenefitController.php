<?php

namespace App\Http\Controllers;

use App\Models\HrBenefitPlan;
use App\Models\HrBenefitEnrollment;
use Illuminate\Http\Request;

class HrBenefitController extends Controller
{
    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'provider' => 'required|string|max:150',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'employee_cost' => 'required|numeric|min:0',
            'company_cost' => 'required|numeric|min:0',
        ]);

        HrBenefitPlan::create($data);

        return back()->with('success', 'Benefit plan created successfully.');
    }

    public function assignBenefit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hr_benefit_plan_id' => 'required|exists:hr_benefit_plans,id',
            'enrollment_date' => 'required|date',
        ]);

        // Check if already enrolled
        $existing = HrBenefitEnrollment::where('user_id', $request->user_id)
            ->where('hr_benefit_plan_id', $request->hr_benefit_plan_id)
            ->where('status', 'Active')
            ->first();

        if ($existing) {
            return back()->with('error', 'Employee is already actively enrolled in this plan.');
        }

        HrBenefitEnrollment::create([
            'user_id' => $request->user_id,
            'hr_benefit_plan_id' => $request->hr_benefit_plan_id,
            'enrollment_date' => $request->enrollment_date,
            'status' => 'Active',
        ]);

        return back()->with('success', 'Employee enrolled in benefit plan successfully.');
    }

    public function terminateEnrollment(Request $request, HrBenefitEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'Terminated']);
        return back()->with('success', 'Benefit enrollment terminated.');
    }
}
