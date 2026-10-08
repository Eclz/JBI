<?php

namespace Tests\Feature;

use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFeeBalanceSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_uses_invoice_total_and_recorded_outstanding_balance(): void
    {
        $student = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $feeStructure = FeeStructure::factory()->create();

        FeeRecord::factory()->create([
            'user_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'amount' => 100,
            'total_amount' => 125,
            'paid_amount' => 40,
            'balance_amount' => 85,
            'status' => 'partial',
        ]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertViewHas('totalFees', 125)
            ->assertViewHas('paidFees', 40)
            ->assertViewHas('pendingFees', 85);
    }

    public function test_completed_student_payment_reduces_dashboard_and_fee_page_balance(): void
    {
        $student = User::factory()->student()->create();
        StudentProfile::factory()->active()->create(['user_id' => $student->id]);
        $feeStructure = FeeStructure::factory()->create();
        $feeRecord = FeeRecord::factory()->create([
            'user_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'amount' => 100,
            'total_amount' => 125,
            'paid_amount' => 40,
            'balance_amount' => 85,
            'status' => 'partial',
        ]);

        $this->actingAs($student)
            ->post(route('student.fees.processPayment', $feeRecord), [
                'amount' => 25,
                'payment_method' => 'card',
            ])
            ->assertRedirect(route('student.fees.index'));

        $this->assertDatabaseHas('fee_records', [
            'id' => $feeRecord->id,
            'paid_amount' => 65,
            'balance_amount' => 60,
            'status' => 'partial',
        ]);

        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertViewHas('pendingFees', 60);

        $this->get(route('student.fees.index'))
            ->assertOk()
            ->assertViewHas('summary', function (array $summary): bool {
                return (float) $summary['outstanding'] === 60.0;
            });
    }
}
