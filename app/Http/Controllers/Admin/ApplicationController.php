<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Models\Department;
use App\Models\Program;
use App\Models\StudentProfile;
use App\Models\Notification;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\SystemSetting;
use App\Mail\AdmissionFeeInstructions;
use App\Mail\AdmissionLetter;
use App\Services\AdmissionWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Application::with(['program.department', 'program.level']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);
        $applications->getCollection()->transform(function ($application) {
            $application->setAttribute('approval_readiness', $this->buildApprovalReadiness($application));
            return $application;
        });
        $programs = Program::where('is_active', true)->with(['department', 'level'])->get();

        return view('admin.applications.index', compact('applications', 'programs'));
    }

    public function show($id)
    {
        $application = Application::with(['program.department', 'program.level'])->findOrFail($id);
        $approvalReadiness = $this->buildApprovalReadiness($application);

        return view('admin.applications.show', compact('application', 'approvalReadiness'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'force_approve' => 'nullable|boolean',
        ]);

        $application = Application::findOrFail($id);

        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'This application has already been processed.');
        }

        $result = $this->performApproval($application, $request->notes, $request->boolean('force_approve'));

        if (!$result['ok']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('admin.applications.index')->with('success', $result['message']);
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'integer|exists:applications,id',
            'notes' => 'nullable|string|max:1000',
            'only_ready' => 'nullable|boolean',
            'force_approve' => 'nullable|boolean',
        ]);

        $onlyReady = $request->boolean('only_ready', true);
        $forceApprove = $request->boolean('force_approve', false);

        $applications = Application::whereIn('id', $validated['application_ids'])
            ->where('status', 'pending')
            ->get();

        $approvedCount = 0;
        $skipped = [];
        $errors = [];

        foreach ($applications as $application) {
            $readiness = $this->buildApprovalReadiness($application);
            if ($onlyReady && !$readiness['ready']) {
                $skipped[] = $application->application_number ?? ('#' . $application->id);
                continue;
            }

            $result = $this->performApproval($application, $validated['notes'] ?? null, $forceApprove);
            if ($result['ok']) {
                $approvedCount++;
            } else {
                $errors[] = ($application->application_number ?? ('#' . $application->id)) . ': ' . $result['message'];
            }
        }

        $message = "Bulk approval completed. Approved: {$approvedCount}.";
        if (!empty($skipped)) {
            $message .= ' Skipped (not ready): ' . count($skipped) . '.';
        }
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' | ', array_slice($errors, 0, 3));
        }

        return redirect()->route('admin.applications.index')->with('success', $message);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $application = Application::findOrFail($id);

        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'This application has already been processed.');
        }

        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'review_notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('admin.applications.index')
                           ->with('success', 'Application rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $application = Application::findOrFail($id);

        if ($application->status !== 'approved' || !$application->payment_proof) {
            return redirect()->back()->with('error', 'Payment proof not submitted or application not approved.');
        }

        DB::beginTransaction();
        try {
            $departmentId = null;
            $program = null;
            if ($application->program_id) {
                $program = Program::with('department')->find($application->program_id);
            } elseif ($application->program) {
                $program = Program::with('department')
                    ->where('name', $application->program)
                    ->first();
            }
            if ($program?->department) {
                $departmentId = $program->department->id;
            }

            $application->update([
                'review_notes' => $request->notes,
                'payment_status' => 'verified',
                'payment_verified_at' => now(),
                'payment_verified_by' => auth()->id(),
            ]);

            $studentUser = $this->createStudentAccount($application, $departmentId);

            if ($studentUser) {
                AdmissionWorkflow::activateStudent($studentUser, $application, auth()->id());
            }

            DB::commit();

            return redirect()->back()
                           ->with('success', 'Payment verified successfully. Admission letter sent to applicant.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to verify payment: ' . $e->getMessage());
        }
    }

    private function createStudentAccount(Application $application, ?int $departmentId = null): ?User
    {
        if ($application->type !== 'student') {
            return null;
        }

        $existingUser = User::where('email', $application->email)->first();
        $program = $application->program_id ? Program::with('department')->find($application->program_id) : null;
        if (!$program && $application->program) {
            $program = Program::with('department')
                ->where('name', $application->program)
                ->first();
        }

        $resolvedDepartmentId = $departmentId ?? $program?->department_id;

        if (!$resolvedDepartmentId) {
            return $existingUser;
        }

        if (!$existingUser) {
            $password = Str::random(12);
            $existingUser = User::create([
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'name' => $application->first_name . ' ' . $application->last_name,
                'email' => $application->email,
                'password' => Hash::make($password),
                'phone' => $application->phone,
                'date_of_birth' => $application->date_of_birth,
                'gender' => $application->gender,
                'address' => $application->address,
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
                'must_change_password' => true,
            ]);
        }

        if (!$existingUser->studentProfile) {
            $admissionNumber = $application->admission_number ?: $this->generateAdmissionNumber($resolvedDepartmentId);
            $registrationDays = (int) SystemSetting::getSetting('registration_payment_days', 14);
            $tuitionDays = (int) SystemSetting::getSetting('tuition_payment_days', 30);

            StudentProfile::create([
                'user_id' => $existingUser->id,
                'admission_number' => $admissionNumber,
                'admission_date' => now(),
                'registration_deadline_at' => now()->addDays($registrationDays),
                'tuition_deadline_at' => now()->addDays($tuitionDays),
                'department_id' => $resolvedDepartmentId,
                'program_id' => $program?->id ?? $application->program_id,
                'program' => $application->program ?? $program?->name,
                'current_semester' => 1,
                'year_of_study' => 1,
                'status' => 'pending',
                'application_status' => 'approved',
                'previous_school' => $application->previous_school,
                'previous_gpa' => $application->previous_gpa,
            ]);

            if (!$application->admission_number) {
                $application->update(['admission_number' => $admissionNumber]);
            }

            $feeStructureId = SystemSetting::getSetting('registration_fee_structure_id');
            if ($feeStructureId) {
                $feeStructure = FeeStructure::find($feeStructureId);
                if ($feeStructure) {
                    $dueDate = now()->addDays($registrationDays);
                    FeeRecord::create([
                        'user_id' => $existingUser->id,
                        'fee_structure_id' => $feeStructure->id,
                        'invoice_number' => 'REG-' . strtoupper(Str::random(8)),
                        'amount' => $feeStructure->amount,
                        'discount_amount' => 0,
                        'late_fee' => 0,
                        'total_amount' => $feeStructure->amount,
                        'paid_amount' => 0,
                        'balance_amount' => $feeStructure->amount,
                        'status' => 'pending',
                        'due_date' => $dueDate,
                    ]);
                }
            }
        }

        return $existingUser;
    }

    private function performApproval(Application $application, ?string $notes, bool $forceApprove = false): array
    {
        $readiness = $this->buildApprovalReadiness($application);
        if ($application->type === 'student' && !$readiness['ready'] && !$forceApprove) {
            return [
                'ok' => false,
                'message' => 'Application is missing required approval details: ' . implode(', ', $readiness['missing']) . '.',
            ];
        }

        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'review_notes' => $notes,
                'payment_status' => $application->payment_proof ? 'uploaded' : ($application->payment_status ?? 'pending'),
            ]);

            $studentUser = null;
            if ($application->type === 'student') {
                $studentUser = $this->createStudentAccount($application);
            }

            try {
                Mail::to($application->email)->send(new AdmissionFeeInstructions($application));
                $successMessage = 'Application approved successfully. Payment instructions sent to ' . $application->email;
            } catch (\Exception $mailError) {
                Log::error('Failed to send admission fee email', [
                    'error' => $mailError->getMessage(),
                    'application_id' => $application->id,
                ]);
                $successMessage = 'Application approved successfully. However, email failed to send to ' . $application->email;
            }

            DB::commit();

            if ($studentUser) {
                Notification::create([
                    'user_id' => $studentUser->id,
                    'title' => 'Application Approved',
                    'message' => 'Your application was approved. Please pay the registration fee to activate your account.',
                    'type' => 'success',
                    'priority' => 'high',
                ]);
            }

            return ['ok' => true, 'message' => $successMessage];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve application: ' . $e->getMessage());

            return ['ok' => false, 'message' => 'Failed to approve application: ' . $e->getMessage()];
        }
    }

    private function buildApprovalReadiness(Application $application): array
    {
        $weightedChecks = [
            ['label' => 'Applicant details', 'weight' => 20, 'passed' => !empty($application->first_name) && !empty($application->last_name)],
            ['label' => 'Contact details', 'weight' => 20, 'passed' => !empty($application->email) && !empty($application->phone) && !empty($application->address)],
            ['label' => 'Program selected', 'weight' => 25, 'passed' => $application->type !== 'student' || !empty($application->program_id) || !empty($application->program)],
            ['label' => 'Supporting documents', 'weight' => 20, 'passed' => !empty($application->documents) && is_array($application->documents)],
            ['label' => 'Prior education info', 'weight' => 15, 'passed' => $application->type !== 'student' || !empty($application->previous_school) || !empty($application->previous_qualification)],
        ];

        $totalWeight = collect($weightedChecks)->sum('weight');
        $achievedWeight = collect($weightedChecks)->where('passed', true)->sum('weight');
        $score = $totalWeight > 0 ? (int) round(($achievedWeight / $totalWeight) * 100) : 0;
        $missing = collect($weightedChecks)
            ->filter(fn ($row) => !$row['passed'])
            ->pluck('label')
            ->values()
            ->all();

        $checks = collect($weightedChecks)->mapWithKeys(function ($row) {
            return [$row['label'] => $row['passed']];
        })->all();

        return [
            'ready' => empty($missing) || $score >= 80,
            'score' => $score,
            'missing' => $missing,
            'checks' => $checks,
            'weighted_checks' => $weightedChecks,
        ];
    }

    private function generateAdmissionNumber(int $departmentId): string
    {
        $department = Department::find($departmentId);
        $year = date('Y');
        $prefix = $department ? strtoupper(substr($department->code, 0, 3)) : 'JBI';

        $lastNumber = StudentProfile::where('admission_number', 'like', "{$prefix}{$year}%")
            ->orderBy('admission_number', 'desc')
            ->first();

        $lastSequence = $lastNumber ? intval(substr($lastNumber->admission_number, -4)) : 0;
        $newSequence = $lastSequence + 1;

        return $prefix . $year . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
