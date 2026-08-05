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
        $profile = $user->studentProfile;
        if (!$profile) {
            return;
        }

        $application = $application ?: Application::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->first();

        $program = null;
        if ($profile->program_id) {
            $program = Program::with('department')->find($profile->program_id);
        }

        $departmentCode = $program?->department?->code ?? 'JBI';

        if (!$profile->admission_number || str_starts_with($profile->admission_number, 'PENDING')) {
            $profile->admission_number = self::generateAdmissionNumber($departmentCode);
        }

        $registrationNumber = sprintf('%s-%s-%04d', $departmentCode, now()->year, self::nextSequence('registration'));

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

        $user->update([
            'is_active' => true,
            'student_id' => $registrationNumber,
        ]);

        if ($application) {
            $application->update([
                'status' => 'admitted',
                'payment_verified_at' => now(),
                'payment_verified_by' => $processedBy,
                'admission_number' => $profile->admission_number,
                'student_number' => $registrationNumber,
                'admitted_at' => now(),
            ]);
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Admission Activated',
            'message' => 'Your registration fee was approved and your admission is now active.',
            'priority' => 'high',
        ]);

        if ($application) {
            Mail::to($application->email)->send(new AdmissionLetter($application));
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

    private static function generateAdmissionNumber(string $departmentCode): string
    {
        $prefix = strtoupper(substr($departmentCode, 0, 3));
        $year = now()->year;

        $lastNumber = StudentProfile::where('admission_number', 'like', "{$prefix}{$year}%")
            ->orderBy('admission_number', 'desc')
            ->first();

        $lastSequence = $lastNumber ? intval(substr($lastNumber->admission_number, -4)) : 0;
        $newSequence = $lastSequence + 1;

        return $prefix . $year . str_pad((string) $newSequence, 4, '0', STR_PAD_LEFT);
    }

    private static function nextSequence(string $key): int
    {
        $year = now()->year;
        $count = StudentProfile::whereYear('created_at', $year)->count();

        return $count + 1;
    }
}
