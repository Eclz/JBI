<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalHubController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all_active'); // 'all_active', 'pending_only', 'all'

        if ($statusFilter === 'pending_only') {
            $qualityReviews = \App\Models\QualityReview::whereIn('status', ['Pending Approval', 'Pending Registrar Approval'])->with(['program', 'dean'])->latest()->get();
            $evaluations = \App\Models\FacultyEvaluation::where('status', 'Pending Approval')->with(['faculty', 'evaluator'])->latest()->get();
            $budgetRequests = \App\Models\BudgetRequest::where('status', 'Pending Finance Approval')->with(['department', 'requester'])->latest()->get();
            $studentIssues = \App\Models\StudentIssue::where('status', 'Escalated')->with(['student', 'reporter'])->latest()->get();
            $partnerships = \App\Models\ExternalPartnership::where('status', 'Pending')->with(['manager'])->latest()->get();
            $studentPayments = \App\Models\Payment::where('status', 'pending')->with(['student.studentProfile', 'feeRecord.feeStructure'])->latest()->get();
        } elseif ($statusFilter === 'all') {
            $qualityReviews = \App\Models\QualityReview::with(['program', 'dean'])->latest()->get();
            $evaluations = \App\Models\FacultyEvaluation::with(['faculty', 'evaluator'])->latest()->get();
            $budgetRequests = \App\Models\BudgetRequest::with(['department', 'requester'])->latest()->get();
            $studentIssues = \App\Models\StudentIssue::with(['student', 'reporter'])->latest()->get();
            $partnerships = \App\Models\ExternalPartnership::with(['manager'])->latest()->get();
            $studentPayments = \App\Models\Payment::with(['student.studentProfile', 'feeRecord.feeStructure'])->latest()->take(50)->get();
        } else {
            // 'all_active' (Default): Includes pending, draft, and active submissions requiring administrative review
            $qualityReviews = \App\Models\QualityReview::whereIn('status', ['Pending Approval', 'Pending Registrar Approval', 'Draft'])->with(['program', 'dean'])->latest()->get();
            $evaluations = \App\Models\FacultyEvaluation::whereIn('status', ['Pending Approval', 'Draft'])->with(['faculty', 'evaluator'])->latest()->get();
            $budgetRequests = \App\Models\BudgetRequest::whereIn('status', ['Pending Finance Approval', 'Draft'])->with(['department', 'requester'])->latest()->get();
            $studentIssues = \App\Models\StudentIssue::whereIn('status', ['Escalated', 'Open', 'In Progress'])->with(['student', 'reporter'])->latest()->get();
            $partnerships = \App\Models\ExternalPartnership::whereIn('status', ['Pending', 'Draft'])->with(['manager'])->latest()->get();
            $studentPayments = \App\Models\Payment::where('status', 'pending')->with(['student.studentProfile', 'feeRecord.feeStructure'])->latest()->get();
        }

        $currencyCode = \App\Models\SystemSetting::getSetting('default_currency', 'USD');

        return view('admin.approval-hub.index', compact(
            'qualityReviews', 'evaluations', 'budgetRequests', 'studentIssues', 'partnerships', 'studentPayments', 'currencyCode', 'statusFilter'
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
