<?php

namespace App\Http\Controllers;

use App\Models\HrJobRole;
use Illuminate\Http\Request;

class JobRoleController extends Controller
{
    public function index()
    {
        $jobRoles = HrJobRole::latest()->paginate(20);
        return view('human-resources.job-roles.index', compact('jobRoles'));
    }

    public function create()
    {
        return view('human-resources.job-roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary_band_min' => 'nullable|numeric|min:0',
            'salary_band_max' => 'nullable|numeric|min:0|gte:salary_band_min',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        HrJobRole::create($data);

        return redirect()->route('human-resources.job-roles.index')->with('success', 'Job role created successfully.');
    }

    public function edit(HrJobRole $jobRole)
    {
        return view('human-resources.job-roles.edit', compact('jobRole'));
    }

    public function update(Request $request, HrJobRole $jobRole)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary_band_min' => 'nullable|numeric|min:0',
            'salary_band_max' => 'nullable|numeric|min:0|gte:salary_band_min',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $jobRole->update($data);

        return redirect()->route('human-resources.job-roles.index')->with('success', 'Job role updated successfully.');
    }

    public function destroy(HrJobRole $jobRole)
    {
        $jobRole->delete();
        return redirect()->route('human-resources.job-roles.index')->with('success', 'Job role deleted successfully.');
    }
}
