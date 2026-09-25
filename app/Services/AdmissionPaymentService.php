<?php

namespace App\Services;

use App\Models\Application;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\FinanceRevenue;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdmissionPaymentService
{
    /**
     * Get or create the master Admission & Application Fee structure.
     */
    public static function getOrCreateAdmissionFeeStructure(): FeeStructure
    {
        $structure = FeeStructure::where('name', 'Admission & Application Fee')
            ->first();

        if (!$structure) {
            $academicYearId = \App\Models\AcademicYear::where('is_active', true)->value('id')
                ?: \App\Models\AcademicYear::first()?->id;

            if (!$academicYearId) {
                $year = \App\Models\AcademicYear::create([
                    'name' => date('Y') . '/' . (date('Y') + 1),
                    'code' => 'AY-' . date('Y'),
                    'start_date' => now()->startOfYear(),
                    'end_date' => now()->endOfYear(),
                    'is_active' => true,
                    'status' => 'active',
                ]);
                $academicYearId = $year->id;
            }

            $structure = FeeStructure::create([
                'name' => 'Admission & Application Fee',
                'type' => 'other',
                'amount' => 150.00,
                'frequency' => 'one_time',
                'academic_year_id' => $academicYearId,
                'is_active' => true,
                'description' => 'Official institutional application processing and admission registration fee',
            ]);
        }

        $configuredId = SystemSetting::getSetting('registration_fee_structure_id');
        if (empty($configuredId)) {
            SystemSetting::setSetting('registration_fee_structure_id', (string) $structure->id);
        }

        return $structure;
    }

    /**
     * Compute admission fee amount according to degree level.
     */
    public static function getAdmissionFeeAmount(Application $application): float
    {
        $levelName = strtolower($application->programRecord?->level?->name ?? '');
        if (str_contains($levelName, 'master') || str_contains($levelName, 'doctor') || str_contains($levelName, 'phd') || str_contains($levelName, 'postgraduate')) {
            return 250.00;
        } elseif (str_contains($levelName, 'bachelor') || str_contains($levelName, 'degree') || str_contains($levelName, 'undergraduate')) {
            return 150.00;
        }

        return 100.00;
    }

    /**
     * Record a verified admission payment into Finance (FeeRecord, Payment, and FinanceRevenue).
     */
    public static function recordAdmissionPayment(Application $application, ?int $processedBy = null): ?Payment
    {
        try {
            $feeStructure = self::getOrCreateAdmissionFeeStructure();
            $amount = self::getAdmissionFeeAmount($application);

            // Find or provision User account for applicant
            $user = User::where('email', $application->email)->first();
            if (!$user) {
                $user = User::create([
                    'first_name' => $application->first_name,
                    'last_name' => $application->last_name,
                    'name' => trim($application->first_name . ' ' . $application->last_name),
                    'email' => $application->email,
                    'password' => Hash::make(Str::random(24)),
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

            $paymentRef = $application->payment_ref ?: ('PAY-' . date('Y') . '-' . strtoupper(Str::random(6)));
            if (!$application->payment_ref) {
                $application->update(['payment_ref' => $paymentRef]);
            }

            $paymentDate = $application->payment_verified_at ?: ($application->payment_uploaded_at ?: now());
            $adminId = $processedBy ?: ($application->payment_verified_by ?: (auth()->id() ?: User::where('role', 'admin')->value('id')));

            // 1. Create or update FeeRecord
            $feeRecord = FeeRecord::where('user_id', $user->id)
                ->where(function ($q) use ($feeStructure, $paymentRef) {
                    $q->where('fee_structure_id', $feeStructure->id)
                      ->orWhere('invoice_number', $paymentRef);
                })
                ->first();

            if (!$feeRecord) {
                $feeRecord = FeeRecord::create([
                    'user_id' => $user->id,
                    'fee_structure_id' => $feeStructure->id,
                    'invoice_number' => $paymentRef,
                    'amount' => $amount,
                    'discount_amount' => 0,
                    'late_fee' => 0,
                    'total_amount' => $amount,
                    'paid_amount' => $amount,
                    'balance_amount' => 0,
                    'status' => 'paid',
                    'due_date' => $paymentDate->toDateString(),
                    'paid_date' => $paymentDate->toDateString(),
                    'payment_method' => 'Bank Transfer',
                    'transaction_id' => $paymentRef,
                    'payment_notes' => "Admission Application Fee for Application #{$application->application_number} (" . ($application->programRecord?->name ?? $application->program) . ')',
                    'payment_history' => [
                        [
                            'amount' => $amount,
                            'date' => $paymentDate->toDateString(),
                            'method' => 'Bank Transfer',
                            'transaction_id' => $paymentRef,
                            'proof_path' => $application->payment_proof,
                            'processed_by' => $adminId,
                            'processed_at' => $paymentDate->toDateTimeString(),
                        ]
                    ],
                    'processed_by' => $adminId,
                ]);
            } else {
                // Ensure record is marked as paid with full amount
                $feeRecord->update([
                    'paid_amount' => $amount,
                    'total_amount' => $amount,
                    'balance_amount' => 0,
                    'status' => 'paid',
                    'paid_date' => $paymentDate->toDateString(),
                    'payment_method' => $feeRecord->payment_method ?: 'Bank Transfer',
                    'transaction_id' => $feeRecord->transaction_id ?: $paymentRef,
                ]);
            }

            // 2. Create or update Payment record
            $payment = Payment::where('reference_number', $paymentRef)
                ->orWhere(function ($q) use ($feeRecord, $paymentRef) {
                    $q->where('fee_record_id', $feeRecord->id)
                      ->where('transaction_id', $paymentRef);
                })
                ->first();

            if (!$payment) {
                $payment = Payment::create([
                    'fee_record_id' => $feeRecord->id,
                    'student_id' => $user->id,
                    'amount' => $amount,
                    'payment_method' => 'Bank Transfer',
                    'transaction_id' => $paymentRef,
                    'reference_number' => $paymentRef,
                    'notes' => "Admission payment verified for Application #{$application->application_number}",
                    'payment_proof' => $application->payment_proof,
                    'status' => 'completed',
                    'processed_by' => $adminId,
                    'payment_date' => $paymentDate,
                ]);
            } else {
                $payment->update([
                    'fee_record_id' => $feeRecord->id,
                    'status' => 'completed',
                    'payment_proof' => $application->payment_proof ?: $payment->payment_proof,
                    'payment_date' => $paymentDate,
                ]);
            }

            // 3. Create or update FinanceRevenue record (Bursar Hub stream)
            $revenue = FinanceRevenue::where('reference_number', $paymentRef)->first();
            if (!$revenue) {
                FinanceRevenue::create([
                    'revenue_code' => 'REV-' . date('Ymd') . '-' . rand(100, 999),
                    'category' => 'Admissions & Applications',
                    'title' => 'Admission Application Fee - ' . trim($application->first_name . ' ' . $application->last_name),
                    'amount' => $amount,
                    'transaction_date' => $paymentDate->toDateString(),
                    'payment_method' => 'Bank Transfer',
                    'reference_number' => $paymentRef,
                    'payer_name' => trim($application->first_name . ' ' . $application->last_name),
                    'notes' => "Application #{$application->application_number} (" . ($application->programRecord?->name ?? $application->program) . ')',
                    'received_by' => $adminId,
                ]);
            }

            return $payment;
        } catch (\Throwable $e) {
            Log::error('Failed to record admission payment into finance: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Synchronize all verified admission payments that are not yet reflected in Finance.
     */
    public static function syncAllAdmissionPayments(): int
    {
        $applications = Application::where(function ($q) {
            $q->where('payment_status', 'verified')
              ->orWhereIn('status', ['admitted', 'approved'])
              ->orWhereNotNull('payment_verified_at');
        })->get();

        $synced = 0;
        foreach ($applications as $app) {
            $payment = self::recordAdmissionPayment($app);
            if ($payment) {
                $synced++;
            }
        }

        return $synced;
    }
}
