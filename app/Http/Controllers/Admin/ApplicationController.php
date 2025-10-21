<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::with(['studentProfile.department', 'facultyProfile.department'])
                    ->where('is_active', false);

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department_id')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('studentProfile', function($subQ) use ($request) {
                    $subQ->where('department_id', $request->department_id);
                })->orWhereHas('facultyProfile', function($subQ) use ($request) {
                    $subQ->where('department_id', $request->department_id);
                });
            });
        }

        if ($request->filled('application_status')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('studentProfile', function($subQ) use ($request) {
                    $subQ->where('application_status', $request->application_status);
                })->orWhereHas('facultyProfile', function($subQ) use ($request) {
                    $subQ->where('application_status', $request->application_status);
                });
            });
        }

        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('admin.applications.index', compact('applications', 'departments'));
    }

    public function show($id)
    {
        $user = User::with(['studentProfile.department', 'facultyProfile.department'])
                          ->findOrFail($id);

        if ($user->is_active) {
            return redirect()->route('admin.applications.index')
                           ->with('error', 'This application has already been processed.');
        }

        return view('admin.applications.show', compact('user'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $registerController = new \App\Http\Controllers\Auth\RegisterController();

        $processRequest = new Request([
            'action' => 'approve',
            'notes' => $request->notes,
        ]);

        $response = $registerController->processApplication($processRequest, $id);
        $data = json_decode($response->getContent(), true);

        if ($data['success']) {
            return redirect()->route('admin.applications.index')
                           ->with('success', $data['message']);
        } else {
            return redirect()->back()
                           ->with('error', $data['error'] ?? 'Failed to approve application.');
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $registerController = new \App\Http\Controllers\Auth\RegisterController();

        $processRequest = new Request([
            'action' => 'reject',
            'notes' => $request->notes,
        ]);

        $response = $registerController->processApplication($processRequest, $id);
        $data = json_decode($response->getContent(), true);

        if ($data['success']) {
            return redirect()->route('admin.applications.index')
                           ->with('success', $data['message']);
        } else {
            return redirect()->back()
                           ->with('error', $data['error'] ?? 'Failed to reject application.');
        }
    }
}
