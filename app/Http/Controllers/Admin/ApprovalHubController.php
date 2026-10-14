<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalHubController extends Controller
{
    public function index()
    {
        $qualityReviews = \App\Models\QualityReview::where('status', 'Pending Approval')->with(['program', 'dean'])->get();
        $evaluations = \App\Models\FacultyEvaluation::where('status', 'Pending Approval')->with(['faculty', 'evaluator'])->get();
        $budgetRequests = \App\Models\BudgetRequest::where('status', 'Pending Finance Approval')->with(['department', 'requester'])->get();
        $studentIssues = \App\Models\StudentIssue::where('status', 'Escalated')->with(['student', 'reporter'])->get();
        $partnerships = \App\Models\ExternalPartnership::where('status', 'Pending')->with(['manager'])->get();

        return view('admin.approval-hub.index', compact(
            'qualityReviews', 'evaluations', 'budgetRequests', 'studentIssues', 'partnerships'
        ));
    }

    public function approveQuality(\Illuminate\Http\Request $request, \App\Models\QualityReview $quality)
    {
        $quality->update([
            'status' => 'Approved',
            'registrar_feedback' => $request->input('notes')
        ]);
        return back()->with('success', 'Academic Quality Review approved.');
    }

    public function rejectQuality(\Illuminate\Http\Request $request, \App\Models\QualityReview $quality)
    {
        $quality->update([
            'status' => 'Rejected',
            'registrar_feedback' => $request->input('notes')
        ]);
        return back()->with('success', 'Academic Quality Review rejected.');
    }

    public function approveEvaluation(\Illuminate\Http\Request $request, \App\Models\FacultyEvaluation $evaluation)
    {
        $evaluation->update([
            'status' => 'Approved',
            'comments' => $evaluation->comments . "\n\nAdmin Notes: " . $request->input('notes')
        ]);
        return back()->with('success', 'Faculty Evaluation approved.');
    }

    public function rejectEvaluation(\Illuminate\Http\Request $request, \App\Models\FacultyEvaluation $evaluation)
    {
        $evaluation->update([
            'status' => 'Rejected',
            'comments' => $evaluation->comments . "\n\nAdmin Rejection Notes: " . $request->input('notes')
        ]);
        return back()->with('success', 'Faculty Evaluation rejected.');
    }

    public function approveBudget(\Illuminate\Http\Request $request, \App\Models\BudgetRequest $budget)
    {
        $budget->update([
            'status' => 'Approved',
            'finance_notes' => $request->input('notes')
        ]);
        return back()->with('success', 'Budget Request approved.');
    }

    public function rejectBudget(\Illuminate\Http\Request $request, \App\Models\BudgetRequest $budget)
    {
        $budget->update([
            'status' => 'Rejected',
            'finance_notes' => $request->input('notes')
        ]);
        return back()->with('success', 'Budget Request rejected.');
    }

    public function resolveIssue(\Illuminate\Http\Request $request, \App\Models\StudentIssue $issue)
    {
        $issue->update([
            'status' => 'Resolved',
            'action_taken' => $issue->action_taken . "\n\nAdmin Resolution: " . $request->input('notes')
        ]);
        return back()->with('success', 'Student Issue resolved.');
    }

    public function approvePartnership(\Illuminate\Http\Request $request, \App\Models\ExternalPartnership $partnership)
    {
        $partnership->update([
            'status' => 'Active',
        ]);
        return back()->with('success', 'Partnership approved and marked Active.');
    }

    public function rejectPartnership(\Illuminate\Http\Request $request, \App\Models\ExternalPartnership $partnership)
    {
        $partnership->update([
            'status' => 'Concluded',
        ]);
        return back()->with('success', 'Partnership marked as Concluded/Rejected.');
    }
}
