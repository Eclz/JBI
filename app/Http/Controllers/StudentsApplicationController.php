<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Department;
use App\Models\Program;
use App\Models\User;
use App\Models\Notification;
use App\Mail\ApplicationSubmitted;
use App\Mail\NewApplicationNotification;
use App\Mail\ApplicationApproved;
use App\Mail\AdmissionFeeInstructions;
use App\Mail\AdmissionLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StudentsApplicationController extends Controller
{
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $programs = Program::where('is_active', true)->with(['department', 'level'])->orderBy('name')->get();

        return view('applications.create', compact('departments', 'programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:student,faculty',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applications,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'program_id' => 'required_if:type,student|exists:programs,id',
            'previous_school' => 'nullable|string|max:255',
            'previous_qualification' => 'nullable|string|max:255',
            'previous_gpa' => 'nullable|numeric|min:0|max:4',
            'department' => 'nullable|required_if:type,faculty|string|max:255',
            'position' => 'nullable|string|max:255',
            'highest_degree' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer|min:0',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $validated['application_number'] = Application::generateApplicationNumber($validated['type']);

            if ($request->hasFile('documents')) {
                $documents = [];
                foreach ($request->file('documents') as $key => $file) {
                    $path = $file->store('applications/' . $validated['application_number'], 'public');
                    $documents[$key] = $path;
                }
                $validated['documents'] = $documents;
            }

            if ($validated['type'] === 'student' && !empty($validated['program_id'])) {
                $program = Program::find($validated['program_id']);
                $validated['program'] = $program?->name;
            }

            $application = Application::create($validated);

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'application',
                    'title' => 'New Application Received',
                    'message' => "New {$application->type} application from {$application->full_name}",
                    'priority' => 'high',
                    'link' => route('admin.applications.show', $application),
                ]);

                Mail::to($admin->email)->queue(new NewApplicationNotification($application));
            }

            Mail::to($application->email)->queue(new ApplicationSubmitted($application));

            DB::commit();

            return redirect()->route('applications.success', $application)
                           ->with('success', 'Your application has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to submit application. Please try again.']);
        }
    }

    public function success(Application $application)
    {
        return view('applications.success', compact('application'));
    }

    public function uploadPayment(Request $request, $token)
    {
        $application = Application::where('application_number', $token)
                                  ->where('status', 'approved')
                                  ->firstOrFail();

        return view('applications.upload-payment', compact('application'));
    }

    public function storePayment(Request $request, $token)
    {
        $application = Application::where('application_number', $token)
                                  ->where('status', 'approved')
                                  ->firstOrFail();

        $request->validate([
            'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $path = $request->file('payment_proof')->store('payment-proofs/' . $application->application_number, 'public');

            $application->update([
                'payment_proof' => $path,
                'payment_status' => 'uploaded',
                'payment_uploaded_at' => now(),
            ]);

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'payment',
                    'title' => 'Payment Proof Uploaded',
                    'message' => "{$application->full_name} has uploaded payment proof",
                    'priority' => 'high',
                    'link' => route('admin.applications.show', $application),
                ]);
            }

            DB::commit();

            return redirect()->route('applications.payment-success', $application->application_number)
                           ->with('success', 'Payment proof uploaded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to upload payment proof. Please try again.']);
        }
    }

    public function paymentSuccess($token)
    {
        $application = Application::where('application_number', $token)->firstOrFail();
        return view('applications.payment-success', compact('application'));
    }
}
