<?php

namespace App\Http\Controllers;

use App\Models\HrVacancy;
use App\Models\HrApplicant;
use App\Models\User;
use Illuminate\Http\Request;

class HrRecruitingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'position_title' => 'required|string|max:150',
            'department' => 'nullable|string|max:150',
            'job_description' => 'nullable|string',
            'num_openings' => 'required|integer|min:1',
            'employment_type' => 'required|string',
            'location' => 'nullable|string',
            'closing_date' => 'nullable|date',
        ]);

        $data['hiring_manager_id'] = auth()->id();
        $data['opening_date'] = now();

        $vacancy = HrVacancy::create($data);

        return redirect()->route('human-resources.recruiting.show', $vacancy->id)
                         ->with('success', 'Vacancy created successfully.');
    }

    public function show(HrVacancy $vacancy)
    {
        $vacancy->load(['hiringManager', 'applicants' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Group applicants by status for the Kanban board
        $kanban = [
            'Applied' => [],
            'Screening' => [],
            'Shortlisted' => [],
            'Interview' => [],
            'Offer' => [],
            'Hired' => [],
            'Rejected' => [],
        ];

        foreach ($vacancy->applicants as $applicant) {
            if (isset($kanban[$applicant->status])) {
                $kanban[$applicant->status][] = $applicant;
            } else {
                // Fallback for custom statuses
                $kanban['Applied'][] = $applicant;
            }
        }

        return view('human-resources.recruiting.show', compact('vacancy', 'kanban'));
    }

    public function updateApplicantStatus(Request $request, HrApplicant $applicant)
    {
        $request->validate([
            'status' => 'required|string|in:Applied,Screening,Shortlisted,Interview,Assessment,Reference Check,Offer,Hired,Rejected',
        ]);

        $applicant->update(['status' => $request->status]);

        // If hired, prompt to create an employee record/onboarding in the view
        if ($request->status === 'Hired' && !$applicant->hired_user_id) {
            return back()->with('success', 'Applicant marked as Hired. Please proceed to onboard them.');
        }

        return back()->with('success', 'Applicant status updated successfully.');
    }
}
