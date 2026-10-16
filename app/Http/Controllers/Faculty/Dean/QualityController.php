<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Models\QualityReview;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index()
    {
        $reviews = QualityReview::with('program')->where('dean_id', auth()->id())->latest()->paginate(15);
        return view('faculty.dean.quality.index', compact('reviews'));
    }

    public function create()
    {
        $programs = \App\Models\Program::orderBy('name')->get();
        return view('faculty.dean.quality.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title' => 'required|string|max:255',
            'review_notes' => 'required|string',
            'metrics' => 'nullable|array',
        ]);

        $validated['dean_id'] = auth()->id();
        $validated['status'] = 'Draft';

        QualityReview::create($validated);

        return redirect()->route('faculty.dean.quality.index')->with('success', 'Quality review created successfully as Draft.');
    }

    public function show(QualityReview $quality)
    {
        if ($quality->dean_id !== auth()->id()) abort(403);
        return view('faculty.dean.quality.show', compact('quality'));
    }

    public function edit(QualityReview $quality)
    {
        if ($quality->dean_id !== auth()->id()) abort(403);
        if ($quality->status !== 'Draft' && $quality->status !== 'Rejected') {
            return back()->with('error', 'Only drafts or rejected reviews can be edited.');
        }

        $programs = \App\Models\Program::orderBy('name')->get();
        return view('faculty.dean.quality.edit', compact('quality', 'programs'));
    }

    public function update(Request $request, QualityReview $quality)
    {
        if ($quality->dean_id !== auth()->id()) abort(403);
        if ($quality->status !== 'Draft' && $quality->status !== 'Rejected') {
            return back()->with('error', 'Only drafts or rejected reviews can be edited.');
        }

        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title' => 'required|string|max:255',
            'review_notes' => 'required|string',
            'metrics' => 'nullable|array',
        ]);

        // If it was rejected, put it back to Draft so it can be re-submitted
        if ($quality->status === 'Rejected') {
            $validated['status'] = 'Draft';
        }

        $quality->update($validated);

        return redirect()->route('faculty.dean.quality.index')->with('success', 'Quality review updated successfully.');
    }

    public function submit(QualityReview $quality)
    {
        if ($quality->dean_id !== auth()->id()) abort(403);
        if ($quality->status !== 'Draft') {
            return back()->with('error', 'Only drafts can be submitted for approval.');
        }

        $quality->update(['status' => 'Pending Registrar Approval']);

        return back()->with('success', 'Review submitted to the Registrar for approval.');
    }

    public function destroy(QualityReview $quality)
    {
        if ($quality->dean_id !== auth()->id()) abort(403);
        if ($quality->status === 'Approved') {
            return back()->with('error', 'Approved reviews cannot be deleted.');
        }

        $quality->delete();

        return redirect()->route('faculty.dean.quality.index')->with('success', 'Quality review deleted.');
    }
}
