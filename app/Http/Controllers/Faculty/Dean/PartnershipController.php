<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Models\ExternalPartnership;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function index()
    {
        $partnerships = ExternalPartnership::where('managed_by', auth()->id())->latest()->paginate(15);
        return view('faculty.dean.partnerships.index', compact('partnerships'));
    }

    public function create()
    {
        return view('faculty.dean.partnerships.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'partnership_type' => 'required|string|max:255',
            'objectives' => 'required|string',
            'funding_amount' => 'nullable|numeric|min:0',
        ]);

        $validated['managed_by'] = auth()->id();
        $validated['status'] = 'Pending';
        if (empty($validated['funding_amount'])) {
            $validated['funding_amount'] = 0;
        }

        ExternalPartnership::create($validated);

        return redirect()->route('faculty.dean.partnerships.index')->with('success', 'Partnership proposal created successfully.');
    }

    public function show(ExternalPartnership $partnership)
    {
        if ($partnership->managed_by !== auth()->id()) abort(403);
        return view('faculty.dean.partnerships.show', compact('partnership'));
    }

    public function edit(ExternalPartnership $partnership)
    {
        if ($partnership->managed_by !== auth()->id()) abort(403);
        
        return view('faculty.dean.partnerships.edit', compact('partnership'));
    }

    public function update(Request $request, ExternalPartnership $partnership)
    {
        if ($partnership->managed_by !== auth()->id()) abort(403);

        $validated = $request->validate([
            'organization_name' => 'required|string|max:255',
            'partnership_type' => 'required|string|max:255',
            'objectives' => 'required|string',
            'funding_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:Pending,Active,Concluded',
        ]);

        if (empty($validated['funding_amount'])) {
            $validated['funding_amount'] = 0;
        }

        $partnership->update($validated);

        return redirect()->route('faculty.dean.partnerships.index')->with('success', 'Partnership updated successfully.');
    }

    public function destroy(ExternalPartnership $partnership)
    {
        if ($partnership->managed_by !== auth()->id()) abort(403);
        
        $partnership->delete();

        return redirect()->route('faculty.dean.partnerships.index')->with('success', 'Partnership deleted.');
    }
}
