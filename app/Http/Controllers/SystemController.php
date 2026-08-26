<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['index', 'update']);
        $this->middleware(['auth'])->only(['index', 'update']);
    }

    public function index()
    {
        $user = auth()->user();

        return view('settings.index', compact('user'));
    }

    public function adminSettings()
    {
        $settings = SystemSetting::all()->keyBy('key');
        $departments = Department::where('is_active', true)->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $feeStructures = \App\Models\FeeStructure::where('is_active', true)->orderBy('name')->get();
        $currencyRegions = config('currencies.regions');
        $supportedCurrencies = config('currencies.supported');
        $admissionWindow = SystemSetting::admissionWindow();
        $currentSemester = \App\Models\Semester::where('is_current', true)->first();

        return view('admin.system.settings', compact('settings', 'departments', 'academicYears', 'feeStructures', 'currencyRegions', 'supportedCurrencies', 'admissionWindow', 'currentSemester'));
    }

    public function settings()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();

        return view('admin.system.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        // Check if this is an admin updating system settings
        if (auth()->user()->role === 'admin' && $request->has('app_name')) {
            return $this->updateAdminSettings($request);
        }

        // Otherwise, this is a user updating their personal settings
        $user = auth()->user();

        $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'theme' => 'nullable|in:light,dark,auto',
            'language' => 'nullable|string|max:10',
        ]);

        // Update user preferences (this could be stored in a user_settings table or JSON column)
        $user->update([
            'preferences' => array_merge($user->preferences ?? [], $request->only([
                'email_notifications',
                'sms_notifications',
                'theme',
                'language'
            ]))
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Your settings have been updated successfully.');
    }

    public function updateAdminSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_email' => 'required|email',
            'app_phone' => 'nullable|string|max:20',
            'app_address' => 'nullable|string|max:500',
            'app_description' => 'nullable|string|max:1000',
            'maintenance_mode' => 'nullable|boolean',
            'admission_enabled' => 'nullable|boolean',
            'max_students_per_course' => 'nullable|integer|min:1',
            'academic_year_start' => 'nullable|date',
            'academic_year_end' => 'nullable|date|after:academic_year_start',
            'operating_region' => ['required', Rule::in(array_keys(config('currencies.regions')))],
            'default_currency' => ['required', Rule::in(array_keys(config('currencies.supported')))],
            'accepted_currencies' => 'required|array|min:1',
            'accepted_currencies.*' => [Rule::in(array_keys(config('currencies.supported')))],
            'timezone' => ['required', Rule::in(timezone_identifiers_list())],
            'admission_open_at' => 'nullable|date',
            'admission_close_at' => 'nullable|date|after:admission_open_at',
            'registration_fee_structure_id' => 'nullable|integer|exists:fee_structures,id',
            'registration_payment_days' => 'nullable|integer|min:1|max:365',
            'tuition_min_percent' => 'nullable|numeric|min:0|max:100',
            'tuition_payment_days' => 'nullable|integer|min:1|max:365',
            'exam_types' => 'nullable|string|max:500',
        ]);

        if (!in_array($validated['default_currency'], $validated['accepted_currencies'], true)) {
            return back()->withErrors([
                'default_currency' => 'The default currency must also be selected as an accepted currency.',
            ])->withInput();
        }

        $values = $request->except(['_token', '_method']);
        $values['maintenance_mode'] = $request->boolean('maintenance_mode');
        $values['admission_enabled'] = $request->boolean('admission_enabled');
        $values['accepted_currencies'] = $request->input('accepted_currencies', []);

        foreach ($values as $key => $value) {
            $type = is_array($value) ? 'json' : (is_bool($value) ? 'boolean' : 'string');
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode(array_values($value)) : ($value ?? ''),
                    'type' => $type,
                    'group' => $this->getSettingGroup($key),
                ]
            );
        }

        return redirect()->route('admin.settings')
            ->with('success', 'System settings updated successfully.');
    }

    private function getSettingGroup($key)
    {
        $groups = [
            'app_name' => 'general',
            'app_email' => 'general',
            'app_phone' => 'general',
            'app_address' => 'general',
            'app_description' => 'general',
            'maintenance_mode' => 'system',
            'admission_enabled' => 'admissions',
            'max_students_per_course' => 'academic',
            'academic_year_start' => 'academic',
            'academic_year_end' => 'academic',
            'exam_types' => 'academic',
            'default_currency' => 'financial',
            'accepted_currencies' => 'financial',
            'operating_region' => 'financial',
            'timezone' => 'system',
            'admission_open_at' => 'admissions',
            'admission_close_at' => 'admissions',
            'registration_fee_structure_id' => 'financial',
            'registration_payment_days' => 'financial',
            'tuition_min_percent' => 'financial',
            'tuition_payment_days' => 'financial',
        ];

        return $groups[$key] ?? 'general';
    }
}
