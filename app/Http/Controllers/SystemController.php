<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function settings()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();

        return view('admin.system.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_email' => 'required|email',
            'app_phone' => 'nullable|string|max:20',
            'app_address' => 'nullable|string|max:500',
            'academic_year_start' => 'required|date',
            'academic_year_end' => 'required|date|after:academic_year_start',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('system.settings')
            ->with('success', 'System settings updated successfully.');
    }
}
