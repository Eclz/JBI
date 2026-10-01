<?php

namespace App\Http\Controllers\Faculty\Dean;

use App\Http\Controllers\Controller;
use App\Models\QualityReview;
use App\Models\Program;
use Illuminate\Http\Request;

class QualityReviewController extends Controller
{
    public function index()
    {
        $reviews = QualityReview::with('program')
            ->where('dean_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('faculty.dean.quality.index', compact('reviews'));
    }

    public function create()
    {
        $deanId = auth()->id();
        $programs = Program::whereHas('department.faculty', function($q) use ($deanId) {
            $q->where('dean_id', $deanId);
        })->get();
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

        return redirect()->route('faculty.dean.quality.index')->with('success', 'Quality review drafted successfully.');
    }

    public function show(QualityReview $quality)
    {
        $this->authorizeAccess($quality);
        return view('faculty.dean.quality.show', compact('quality'));
    }

    public function edit(QualityReview $quality)
    {
        $this->authorizeAccess($quality);
        if ($quality->status !== 'Draft') {
            return redirect()->route('faculty.dean.quality.index')->with('error', 'Only draft reviews can be edited.');
        }

        $deanId = auth()->id();
        $programs = Program::whereHas('department.faculty', function($q) use ($deanId) {
            $q->where('dean_id', $deanId);
        })->get();
        return view('faculty.dean.quality.edit', compact('quality', 'programs'));
    }

    public function update(Request $request, QualityReview $quality)
    {
        $this->authorizeAccess($quality);
        if ($quality->status !== 'Draft') {
            return redirect()->route('faculty.dean.quality.index')->with('error', 'Only draft reviews can be edited.');
        }

        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title' => 'required|string|max:255',
            'review_notes' => 'required|string',
            'metrics' => 'nullable|array',
        ]);

        $quality->update($validated);

        return redirect()->route('faculty.dean.quality.index')->with('success', 'Quality review updated successfully.');
    }

    public function destroy(QualityReview $quality)
    {
        $this->authorizeAccess($quality);
        if ($quality->status !== 'Draft') {
            return redirect()->route('faculty.dean.quality.index')->with('error', 'Only draft reviews can be deleted.');
        }

        $quality->delete();

        return redirect()->route('faculty.dean.quality.index')->with('success', 'Quality review deleted.');
    }

    public function submit(QualityReview $quality)
    {
        $this->authorizeAccess($quality);
        if ($quality->status !== 'Draft') {
            return back()->with('error', 'Only drafts can be submitted.');
        }

        $quality->update(['status' => 'Pending Approval']);

        return back()->with('success', 'Quality review submitted for approval.');
    }

    private function authorizeAccess(QualityReview $quality)
    {
        if ($quality->dean_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
