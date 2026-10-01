<?php

namespace App\Http\Controllers;

use App\Models\HrPerformanceReview;
use App\Models\HrPerformanceGoal;
use Illuminate\Http\Request;

class HrPerformanceController extends Controller
{
    public function storeReview(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reviewer_id' => 'required|exists:users,id',
            'review_period' => 'required|string|max:150',
            'review_date' => 'nullable|date',
        ]);

        $data['status'] = 'Draft';

        HrPerformanceReview::create($data);

        return back()->with('success', 'Performance review cycle initiated.');
    }

    public function updateReview(Request $request, HrPerformanceReview $review)
    {
        $data = $request->validate([
            'status' => 'required|in:Draft,Scheduled,Completed',
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        $review->update($data);

        return back()->with('success', 'Performance review updated successfully.');
    }

    public function storeGoal(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $data['status'] = 'Not Started';
        $data['progress_percentage'] = 0;

        HrPerformanceGoal::create($data);

        return back()->with('success', 'Goal assigned to employee successfully.');
    }

    public function updateGoalProgress(Request $request, HrPerformanceGoal $goal)
    {
        $data = $request->validate([
            'status' => 'required|in:Not Started,In Progress,Completed,Cancelled',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        if ($data['progress_percentage'] == 100) {
            $data['status'] = 'Completed';
        }

        $goal->update($data);

        return back()->with('success', 'Goal progress updated successfully.');
    }
}
