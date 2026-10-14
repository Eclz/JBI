<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Exam;
use App\Models\Timetable;
use App\Models\Assignment;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::where('is_current', true)->first() ?? AcademicYear::first();
        $semesters = Semester::all();

        // 1. Upcoming exams and assessments
        $upcomingExams = Exam::with('course')
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(10)
            ->get();

        $upcomingAssignments = Assignment::with('course')
            ->where('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        $timetables = Timetable::with(['course', 'faculty'])->get();

        // Build Events Array for FullCalendar
        $events = [];

        // Add Exams
        $allExams = Exam::with('course')->get();
        foreach ($allExams as $exam) {
            if ($exam->start_time && $exam->end_time) {
                $events[] = [
                    'title' => 'Exam: ' . ($exam->course->code ?? '') . ' ' . $exam->title,
                    'start' => $exam->start_time->toIso8601String(),
                    'end' => $exam->end_time->toIso8601String(),
                    'backgroundColor' => '#dc3545', // danger
                    'borderColor' => '#dc3545',
                    'extendedProps' => [
                        'description' => 'Course: ' . ($exam->course->name ?? '') . '<br>Duration: ' . ($exam->duration_minutes ?? 120) . ' mins',
                        'type' => 'Examination'
                    ]
                ];
            }
        }

        // Add Assignments
        $allAssignments = Assignment::with('course')->get();
        foreach ($allAssignments as $assign) {
            if ($assign->due_date) {
                $events[] = [
                    'title' => 'Due: ' . ($assign->course->code ?? '') . ' ' . $assign->title,
                    'start' => $assign->due_date->toIso8601String(),
                    'allDay' => true,
                    'backgroundColor' => '#0d6efd', // primary
                    'borderColor' => '#0d6efd',
                    'extendedProps' => [
                        'description' => 'Course: ' . ($assign->course->name ?? '') . '<br>Max Score: ' . ($assign->max_score ?? 100),
                        'type' => 'Assignment'
                    ]
                ];
            }
        }

        // Add HR Events (Leaves) if model exists
        if (class_exists(\App\Models\LeaveRequest::class)) {
            $leaves = \App\Models\LeaveRequest::with('user')->where('status', 'approved')->get();
            foreach ($leaves as $leave) {
                if ($leave->start_date && $leave->end_date) {
                    $events[] = [
                        'title' => 'Leave: ' . ($leave->user->full_name ?? 'Staff'),
                        'start' => $leave->start_date->format('Y-m-d'),
                        'end' => $leave->end_date->addDay()->format('Y-m-d'), // FullCalendar exclusive end date
                        'allDay' => true,
                        'backgroundColor' => '#198754', // success
                        'borderColor' => '#198754',
                        'extendedProps' => [
                            'description' => 'Staff: ' . ($leave->user->full_name ?? '') . '<br>Type: ' . ($leave->leave_type ?? 'Leave') . '<br>Status: Approved',
                            'type' => 'Staff Leave'
                        ]
                    ];
                }
            }
        }

        // System configuration dates (Semester, Registration, Program Change)
        $settings = \App\Models\SystemSetting::pluck('value', 'key');
        
        if (isset($settings['semester_start_date']) && isset($settings['semester_end_date'])) {
            $events[] = [
                'title' => 'Academic Semester',
                'start' => $settings['semester_start_date'],
                'end' => \Carbon\Carbon::parse($settings['semester_end_date'])->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#0dcaf0', // info
                'borderColor' => '#0dcaf0',
                'extendedProps' => [
                    'description' => 'Official academic semester duration.',
                    'type' => 'Academic Event'
                ]
            ];
        }

        if (isset($settings['semester_registration_start']) && isset($settings['semester_registration_end'])) {
            $events[] = [
                'title' => 'Registration Window',
                'start' => $settings['semester_registration_start'],
                'end' => \Carbon\Carbon::parse($settings['semester_registration_end'])->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#ffc107', // warning
                'borderColor' => '#ffc107',
                'extendedProps' => [
                    'description' => 'Period for students to register for courses.',
                    'type' => 'Academic Event'
                ]
            ];
        }
        
        if (isset($settings['program_change_start']) && isset($settings['program_change_end'])) {
            $events[] = [
                'title' => 'Program Change Window',
                'start' => $settings['program_change_start'],
                'end' => \Carbon\Carbon::parse($settings['program_change_end'])->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#6610f2', // purple
                'borderColor' => '#6610f2',
                'extendedProps' => [
                    'description' => 'Period for students to request program changes.',
                    'type' => 'Academic Event'
                ]
            ];
        }

        return view('calendar.index', compact('currentYear', 'semesters', 'upcomingExams', 'upcomingAssignments', 'timetables', 'events'));
    }
}
