<?php

namespace App\Services;

use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Str;

class FeeInvoiceService
{
    /**
     * Ensure student has generated fee invoices backing up their financial status.
     * Prevents students from falsely having a 0 balance with no fee records / invoices.
     */
    public static function ensureStudentInvoiced(User $user): void
    {
        if (!$user->isStudent()) {
            return;
        }

        // Check if student already has fee records
        $existingCount = FeeRecord::where('user_id', $user->id)->count();
        if ($existingCount > 0) {
            return;
        }

        $sp = $user->studentProfile;
        $activeYear = AcademicYear::where('is_active', true)->first() 
            ?? AcademicYear::orderBy('year', 'desc')->first();
        $activeSem = Semester::where('is_active', true)->first() 
            ?? Semester::orderBy('id', 'asc')->first();

        // Check active fee structures
        $feeStructures = FeeStructure::where('is_active', true)->get();

        // If no active fee structures exist, create default fee structures
        if ($feeStructures->count() === 0) {
            $defaultStructures = [
                [
                    'name' => 'Semester Tuition Fee',
                    'description' => 'Standard undergraduate degree semester tuition fee charge',
                    'type' => 'tuition',
                    'amount' => 1200.00,
                    'frequency' => 'semester',
                    'academic_year_id' => $activeYear?->id,
                    'semester_id' => $activeSem?->id,
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'name' => 'University Functional & Registration Fee',
                    'description' => 'Annual ICT, library, identity card, and university administrative services fee',
                    'type' => 'registration',
                    'amount' => 250.00,
                    'frequency' => 'semester',
                    'academic_year_id' => $activeYear?->id,
                    'semester_id' => $activeSem?->id,
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
                [
                    'name' => 'Examination & Assessment Fee',
                    'description' => 'End of semester examination processing and booklet evaluation charge',
                    'type' => 'exam',
                    'amount' => 150.00,
                    'frequency' => 'semester',
                    'academic_year_id' => $activeYear?->id,
                    'semester_id' => $activeSem?->id,
                    'is_mandatory' => true,
                    'is_active' => true,
                ],
            ];

            foreach ($defaultStructures as $ds) {
                FeeStructure::create($ds);
            }

            $feeStructures = FeeStructure::where('is_active', true)->get();
        }

        // Generate official invoices for this student
        foreach ($feeStructures as $structure) {
            $dueDate = $structure->due_date ? $structure->due_date : now()->addDays(30);
            $amount = $structure->amount;

            FeeRecord::create([
                'user_id' => $user->id,
                'fee_structure_id' => $structure->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'amount' => $amount,
                'discount_amount' => 0,
                'late_fee' => 0,
                'total_amount' => $amount,
                'paid_amount' => 0,
                'balance_amount' => $amount,
                'type' => $structure->type,
                'status' => 'pending',
                'due_date' => $dueDate,
                'payment_notes' => $structure->name . ' (' . ($activeYear?->year ?? '2026/2027') . ')',
            ]);
        }
    }
}
