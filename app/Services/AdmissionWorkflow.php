<?php

namespace App\Services;

use App\Mail\AdmissionLetter;
use App\Models\Application;
use App\Models\Notification;
use App\Models\Program;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class AdmissionWorkflow
{
    public static function activateStudent(User $user, ?Application $application = null, ?int $processedBy = null): void
    {
        $user->unsetRelation('studentProfile');
        $profile = $user->studentProfile ?: StudentProfile::where('user_id', $user->id)->first();

        $application = $application ?: Application::where(function($q) use ($user) {
                $q->where('email', $user->email)->orWhere('email', strtolower(trim($user->email)));
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$profile && $application) {
            $program = $application->program_id ? Program::with('department')->find($application->program_id) : null;
            if (!$program && $application->program) {
                $program = Program::with('department')->where('name', $application->program)->first();
            }
            $deptCode = $program?->department?->code ?? 'JBI';
            $admNumber = $application->admission_number ?: self::generateAdmissionNumber($deptCode);
            $profile = StudentProfile::create([
                'user_id' => $user->id,
                'admission_number' => $admNumber,
                'admission_date' => now(),
                'department_id' => $program?->department_id,
                'program_id' => $program?->id ?? $application->program_id,
                'program' => $application->program ?? $program?->name,
                'current_semester' => 1,
                'year_of_study' => 1,
                'status' => 'active',
                'application_status' => 'approved',
                'previous_school' => $application->previous_school,
                'previous_gpa' => $application->previous_gpa,
            ]);
        }

        if (!$profile) {
            return;
        }

        $program = null;
        if ($profile->program_id) {
            $program = Program::with('department')->find($profile->program_id);
        }

        $departmentCode = $program?->department?->code ?? 'JBI';

        if (!$profile->admission_number || str_starts_with($profile->admission_number, 'PENDING')) {
            $profile->admission_number = self::generateAdmissionNumber($departmentCode);
        }

        $studentNumber = $profile->getRawOriginal('student_id') ?: self::generateStudentNumber($departmentCode);

        if (!$profile->registration_fee_paid_at) {
            $profile->registration_fee_paid_at = now();
        }
        if (!$profile->tuition_deadline_at && $profile->registration_fee_paid_at) {
            $days = (int) \App\Models\SystemSetting::getSetting('tuition_payment_days', 30);
            $profile->tuition_deadline_at = $profile->registration_fee_paid_at->copy()->addDays($days);
        }
        $profile->status = 'active';
        $profile->application_status = 'approved';
        $profile->save();
        $user->setRelation('studentProfile', $profile);

        $studentRole = \App\Models\Role::where('guard_role', 'student')->first();
        $userUpdates = [
            'is_active' => true,
            'student_id' => $studentNumber,
        ];
        if (!$user->role_id && $studentRole) {
            $userUpdates['role_id'] = $studentRole->id;
        }
        $user->update($userUpdates);

        if ($application) {
            $application->update([
                'status' => 'admitted',
                'payment_status' => 'verified',
                'payment_verified_at' => $application->payment_verified_at ?: now(),
                'payment_verified_by' => $application->payment_verified_by ?: $processedBy,
                'admission_number' => $profile->admission_number,
                'student_number' => $studentNumber,
                'admitted_at' => now(),
            ]);
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Admission Approved & Student Number Issued',
            'message' => "Congratulations! You have been officially admitted. Your Student Number is {$studentNumber} and Admission Number is {$profile->admission_number}.",
            'priority' => 'high',
        ]);

        if ($application) {
            try {
                Mail::to($application->email)->send(new AdmissionLetter($application));
            } catch (\Throwable $mailErr) {
                \Illuminate\Support\Facades\Log::error('Admission letter email failed: ' . $mailErr->getMessage());
            }

            // Deliver Admission Letter directly to Student's Mailbox
            \App\Models\Message::create([
                'sender_id' => $processedBy ?: (\App\Models\User::where('role', 'admin')->first()?->id ?: $user->id),
                'receiver_id' => $user->id,
                'subject' => 'Official JBI University Letter of Admission - Student No: ' . $studentNumber,
                'body' => "Dear {$user->first_name},\n\nCongratulations! We are pleased to inform you that you have been granted official admission to JBI University for the programme: " . ($application->programRecord->name ?? $application->program) . ".\n\nYour Official Student Number is: {$studentNumber}\nYour Admission Number is: {$profile->admission_number}\n\nYou can view and download your official Letter of Admission directly in your portal.",
                'type' => 'system',
                'is_read' => false,
                'related_link' => route('student.admission-letter.show'),
            ]);
        }

        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\AdminAdmissionNotification($user, $application));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send admin admission notification: ' . $e->getMessage());
        }
    }

    public static function generateAdmissionNumber(string $departmentCode): string
    {
        $prefix = strtoupper(substr($departmentCode, 0, 3));
        $year = now()->year;

        $profiles = StudentProfile::where('admission_number', 'like', "{$prefix}{$year}%")
            ->pluck('admission_number');

        $applications = Application::where('admission_number', 'like', "{$prefix}{$year}%")
            ->pluck('admission_number');

        $allNumbers = $profiles->concat($applications);

        $maxSequence = 0;
        foreach ($allNumbers as $adm) {
            if ($adm && preg_match('/^' . preg_quote($prefix . $year, '/') . '(\d+)$/', $adm, $matches)) {
                $seq = intval($matches[1]);
                if ($seq > $maxSequence) {
                    $maxSequence = $seq;
                }
            }
        }

        $newSequence = $maxSequence + 1;
        return $prefix . $year . str_pad((string) $newSequence, 4, '0', STR_PAD_LEFT);
    }

    public static function generateStudentNumber(?string $departmentCode = null): string
    {
        $year = now()->year;
        
        // Find highest sequence among STU{$year}xxxx numbers
        $existingUsers = User::where('student_id', 'like', "STU{$year}%")->pluck('student_id');
        $existingApps = Application::where('student_number', 'like', "STU{$year}%")->pluck('student_number');

        $all = $existingUsers->concat($existingApps)->filter();

        $maxSeq = 0;
        foreach ($all as $num) {
            if (preg_match('/^STU' . $year . '(\d+)$/', $num, $matches)) {
                $seq = intval($matches[1]);
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        $newSequence = $maxSeq + 1;
        return sprintf('STU%s%04d', $year, $newSequence);
    }
}
