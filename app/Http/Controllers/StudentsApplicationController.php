<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Department;
use App\Models\Program;
use App\Models\User;
use App\Models\Notification;
use App\Models\SystemSetting;
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
        if (!auth()->user()->hasVerifiedEmail()) {
            return back()->withErrors(['application' => 'Verify your email address before submitting your application. Use Resend verification above if needed.'])->withInput();
        }
        if (!SystemSetting::admissionWindow()['isOpen']) {
            return back()->withErrors(['application' => 'Applications are currently closed. Your information was not submitted.']);
        }

        $email = $request->email;
        if (auth()->check()) {
            $user = auth()->user();
            $email = $user->email;
            
            $nameParts = explode(' ', trim($user->name ?? ''), 2);
            $firstName = $request->first_name ?: ($user->first_name ?: ($nameParts[0] ?? 'Applicant'));
            $lastName = $request->last_name ?: ($user->last_name ?: ($nameParts[1] ?? 'Student'));
            $phone = $request->phone ?: $user->phone;

            $request->merge([
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
            ]);
        }

        $normalizedEmail = strtolower(trim($email ?? ''));
        $request->merge(['email' => $normalizedEmail]);
        $existingApp = Application::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();
        if ($existingApp && !in_array($existingApp->status, ['pending', 'rejected'], true)) {
            return back()->withErrors(['application' => 'This application has already been admitted. Contact admissions for changes.']);
        }

        $emailRule = $existingApp ? 'required|email|unique:applications,email,' . $existingApp->id : 'required|email|unique:applications,email';

        $validated = $request->validate([
            'type' => 'required|in:student',
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
            'documents' => 'required|array|min:1|max:8',
            'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'emergency_contact_name' => 'required_if:type,student|string|max:255',
            'emergency_contact_phone' => 'required_if:type,student|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'application_notes' => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            if ($existingApp) {
                $existingApp = Application::whereKey($existingApp->id)->lockForUpdate()->firstOrFail();
                if (!in_array($existingApp->status, ['pending', 'rejected'], true)) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['application' => 'This application has already been processed.']);
                }
                DB::table('application_revisions')->insert([
                    'application_id' => $existingApp->id,
                    'submitted_by' => auth()->id(),
                    'snapshot' => json_encode($existingApp->getAttributes()),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
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
                    $path = $file->store('applications/' . $appNum, 'local');
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
                // Student applicants are NOT automatically assigned a course/programme.
                // The programmes applied for are saved in program_choices and reflected in admission,
                // but the official programme must be manually assigned by staff with application permissions.
                $validated['program_id'] = null;
                $validated['program'] = null;
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
                'program_id_6',
                'application_notes',
            ])->toArray();

            if (!empty($validated['application_notes'])) {
                $applicationData['review_notes'] = $validated['application_notes'];
            }

            if ($existingApp) {
                $applicationData['status'] = 'pending';
                $applicationData['documents_verified_at'] = null;
                $applicationData['documents_verified_by'] = null;
                $applicationData['documents_verified_notes'] = null;
                $applicationData['reviewed_at'] = null;
                $applicationData['reviewed_by'] = null;
                $applicationData['review_notes'] = $validated['application_notes'] ?? null;
                $existingApp->update($applicationData);
                $application = $existingApp;
            } else {
                $applicationData['status'] = 'pending';
                $applicationData['payment_ref'] = Application::generatePaymentRef();
                $application = Application::create($applicationData);
            }

            \App\Services\OnboardingNotificationService::notify('applications',
                $existingApp ? 'Application updated — review required' : 'New Application Received',
                "{$application->full_name} submitted an application. Review payment, documents and programme in order.",
                route('admin.applications.show', $application));
            Mail::to($application->email)->queue((new ApplicationSubmitted($application))->onQueue('onboarding')->afterCommit());

            DB::commit();

            return redirect()->route('dashboard')
                           ->with('success', 'Your application has been submitted successfully! Please proceed to the next step below.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Application submission failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => 'Failed to submit application: ' . $e->getMessage()]);
        }
    }

    public function success(Application $application)
    {
        abort_unless(strtolower(auth()->user()->email) === strtolower($application->email), 403);
        return view('applications.success', compact('application'));
    }

    public function uploadPayment(Request $request, $token)
    {
        $application = Application::where('application_number', $token)
                                  ->whereIn('status', ['pending', 'approved'])
                                  ->firstOrFail();

        abort_unless(strtolower(auth()->user()->email) === strtolower($application->email), 403);
        $currencyCode = \App\Services\AdmissionPaymentService::registrationFeeCurrency($application);
        $feeAmount = \App\Services\AdmissionPaymentService::getAdmissionFeeAmount($application, $currencyCode);

        return view('applications.upload-payment', compact('application', 'currencyCode', 'feeAmount'));
    }

    public function storePayment(Request $request, $token)
    {
        $application = Application::where('application_number', $token)
                                  ->whereIn('status', ['pending', 'approved'])
                                  ->firstOrFail();

        abort_unless(strtolower(auth()->user()->email) === strtolower($application->email), 403);
        if ($application->isPaymentApproved()) {
            return back()->withErrors(['payment_proof' => 'This payment has already been verified. Contact admissions for corrections.']);
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'payment_currency' => 'nullable|in:ZAR,USD',
        ]);

        DB::beginTransaction();
        try {
            $path = $request->file('payment_proof')->store('payment-proofs/' . $application->application_number, 'local');
            $paymentCurrency = $request->input('payment_currency', $application->payment_currency ?: 'ZAR');

            $application->update([
                'payment_currency' => $paymentCurrency,
                'payment_proof' => $path,
                'payment_status' => 'uploaded',
                'payment_uploaded_at' => now(),
            ]);

            \App\Services\OnboardingNotificationService::notify('applications',
                'Registration Fee Proof Uploaded', "{$application->full_name} uploaded a registration fee receipt in {$paymentCurrency}. Verify it before reviewing documents.",
                route('admin.applications.show', $application));

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
        abort_unless(strtolower(auth()->user()->email) === strtolower($application->email), 403);
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
