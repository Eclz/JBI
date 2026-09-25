<?php

namespace App\Http\Controllers;

use App\Models\HrSuccessionPlan;
use App\Models\HrSuccessor;
use App\Models\User;
use Illuminate\Http\Request;

class HrSuccessionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'position_name' => 'required|string|max:150',
            'department' => 'nullable|string|max:150',
            'current_holder_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $plan = HrSuccessionPlan::create($data);

        return redirect()->route('human-resources.succession.show', $plan->id)
                         ->with('success', 'Succession plan created successfully.');
    }

    public function show(HrSuccessionPlan $succession)
    {
        $succession->load(['currentHolder', 'successors.user.hrProfile']);
        $users = User::whereNotIn('role', ['student', 'applicant', 'parent', ''])->whereNotNull('role')->orderBy('first_name')->get();
        return view('human-resources.succession.show', compact('succession', 'users'));
    }

    public function addSuccessor(Request $request, HrSuccessionPlan $succession)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'readiness_level' => 'required|string',
            'development_needs' => 'nullable|string',
        ]);

        if ($succession->successors()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Employee is already listed as a successor for this position.');
        }

        $succession->successors()->create([
            'user_id' => $request->user_id,
            'readiness_level' => $request->readiness_level,
            'development_needs' => $request->development_needs,
        ]);

        return back()->with('success', 'Successor added successfully.');
    }
}
