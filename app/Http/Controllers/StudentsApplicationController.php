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
        $email = $request->email;
        if (auth()->check()) {
            $email = auth()->user()->email;
            $request->merge(['email' => $email]);
        }

        $existingApp = null;
        if ($email) {
            Application::where('email', $email)->where('status', 'rejected')->delete();
            $existingApp = Application::where('email', $email)->first();
        }

        $emailRule = $existingApp ? 'required|email|unique:applications,email,' . $existingApp->id : 'required|email|unique:applications,email';

        $validated = $request->validate([
            'type' => 'required|in:student,faculty',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => $emailRule,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'program_id_1' => 'required_if:type,student|exists:programs,id',
            'program_id_2' => 'nullable|exists:programs,id|different:program_id_1',
            'program_id_3' => 'nullable|exists:programs,id|different:program_id_1|different:program_id_2',
            'program_id_4' => 'nullable|exists:programs,id|different:program_id_1|different:program_id_2|different:program_id_3',
            'program_id_5' => 'nullable|exists:programs,id|different:program_id_1|different:program_id_2|different:program_id_3|different:program_id_4',
            'program_id_6' => 'nullable|exists:programs,id|different:program_id_1|different:program_id_2|different:program_id_3|different:program_id_4|different:program_id_5',
            'previous_school' => 'nullable|string|max:255',
            'previous_qualification' => 'nullable|string|max:255',
            'previous_gpa' => 'nullable|numeric|min:0|max:4',
            'department' => 'nullable|required_if:type,faculty|string|max:255',
            'position' => 'nullable|string|max:255',
            'highest_degree' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer|min:0',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'emergency_contact_name' => 'required_if:type,student|string|max:255',
            'emergency_contact_phone' => 'required_if:type,student|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if (auth()->check()) {
                $user = auth()->user();
                $userUpdateData = [
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address'],
                    'emergency_contact' => $validated['emergency_contact_name'] ?? null,
                    'emergency_phone' => $validated['emergency_contact_phone'] ?? null,
                ];

                if ($request->hasFile('profile_picture')) {
                    $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
                    $userUpdateData['profile_picture'] = $profilePicturePath;
                }

                $user->update($userUpdateData);
            }

            if (!$existingApp) {
                $validated['application_number'] = Application::generateApplicationNumber($validated['type']);
            }

            if ($request->hasFile('documents')) {
                $documents = [];
                $appNum = $existingApp ? $existingApp->application_number : $validated['application_number'];
                foreach ($request->file('documents') as $key => $file) {
                    $path = $file->store('applications/' . $appNum, 'public');
                    $documents[$key] = $path;
                }
                $validated['documents'] = $documents;
            }

            if ($validated['type'] === 'student') {
                $choices = [];
                for ($i = 1; $i <= 6; $i++) {
                    if (!empty($validated["program_id_$i"])) {
                        $choices[] = (int) $validated["program_id_$i"];
                    }
                }
                $validated['program_choices'] = $choices;
                $validated['program_id'] = $validated['program_id_1'] ?? null;

                if (!empty($validated['program_id'])) {
                    $program = Program::find($validated['program_id']);
                    $validated['program'] = $program?->name;
                }
            }

            // Exclude temporary fields
            $applicationData = collect($validated)->except([
                'emergency_contact_name', 
                'emergency_contact_phone', 
                'profile_picture',
                'program_id_1',
                'program_id_2',
                'program_id_3',
                'program_id_4',
                'program_id_5',
                'program_id_6'
            ])->toArray();

            if ($existingApp) {
                $applicationData['status'] = 'pending';
                $existingApp->update($applicationData);
                $application = $existingApp;
            } else {
                $applicationData['status'] = 'pending';
                $applicationData['payment_ref'] = Application::generatePaymentRef();
                $application = Application::create($applicationData);
            }


            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'application',
                    'title' => 'New Application Received',
                    'message' => "New {$application->type} application from {$application->full_name}",
                    'priority' => 'high',
                    'action_url' => route('admin.applications.show', $application),
                ]);

                Mail::to($admin->email)->queue(new NewApplicationNotification($application));
            }

            Mail::to($application->email)->queue(new ApplicationSubmitted($application));

            DB::commit();

            return redirect()->route('dashboard')
                           ->with('success', 'Your application has been submitted successfully! Please submit your fee payment proof below.');

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
                                  ->whereIn('status', ['pending', 'approved'])
                                  ->firstOrFail();

        return view('applications.upload-payment', compact('application'));
    }

    public function storePayment(Request $request, $token)
    {
        $application = Application::where('application_number', $token)
                                  ->whereIn('status', ['pending', 'approved'])
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
                    'action_url' => route('admin.applications.show', $application),
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')
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

    public function regeneratePaymentRef(Request $request, Application $application)
    {
        if ($application->email !== auth()->user()->email) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($application->payment_status, ['pending', 'rejected'])) {
            $application->update([
                'payment_ref' => Application::generatePaymentRef()
            ]);
            return back()->with('success', 'Payment reference regenerated successfully!');
        }

        return back()->withErrors(['error' => 'Cannot regenerate payment reference after uploading receipt.']);
    }
}
