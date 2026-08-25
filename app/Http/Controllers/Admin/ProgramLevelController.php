<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramLevelController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramLevel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $programLevels = $query->orderByRaw("CASE code WHEN 'CERT' THEN 1 WHEN 'DIP' THEN 2 WHEN 'ADVDIP' THEN 3 WHEN 'BACH' THEN 4 WHEN 'MASTER' THEN 5 WHEN 'PHD' THEN 6 ELSE 99 END")
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.program-levels.index', compact('programLevels'));
    }

    public function create()
    {
        return view('admin.program-levels.create');
    }

    public function store(Request $request)
    {

        $this->normalizeCode($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:program_levels,code',
            'description' => 'nullable|string|max:2000',
        ]);


        DB::beginTransaction();

        try {
            ProgramLevel::create([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('admin.program-levels.index')
                ->with('success', 'Program level created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create program level', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create program level.'])->withInput();
        }
    }

    public function edit(ProgramLevel $programLevel)
    {
        return view('admin.program-levels.edit', compact('programLevel'));
    }

    public function update(Request $request, ProgramLevel $programLevel)
    {
        $this->normalizeCode($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:program_levels,code,' . $programLevel->id,
            'description' => 'nullable|string|max:2000',
            // 'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $programLevel->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', $programLevel->is_active),
            ]);

            DB::commit();

            return redirect()->route('admin.program-levels.index')
                ->with('success', 'Program level updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update program level', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update program level.'])->withInput();
        }
    }

    public function destroy(ProgramLevel $programLevel)
    {
        if ($programLevel->programs()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a program level with programs.']);
        }

        $programLevel->delete();

        return redirect()->route('admin.program-levels.index')
            ->with('success', 'Program level deleted successfully.');
    }

    private function normalizeCode(Request $request): void
    {
        $code = $request->input('code');
        $code = is_string($code) ? trim($code) : null;
        $code = $code === '' ? null : strtoupper($code);
        $request->merge(['code' => $code]);
    }
}
