<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\ProgramLevel;

class AcademicSetupController extends Controller
{
    public function index()
    {
        return view('admin.academic-setup.index', [
            'counts' => [
                'levels' => ProgramLevel::where('is_active', true)->count(),
                'faculties' => Faculty::where('is_active', true)->count(),
                'departments' => Department::where('is_active', true)->count(),
                'programs' => Program::where('is_active', true)->count(),
                'courses' => Course::where('status', 'active')->count(),
                'fees' => FeeStructure::where('is_active', true)->count(),
            ],
            'academicYear' => AcademicYear::where('year', 2026)->first(),
        ]);
    }
}
