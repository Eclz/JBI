<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FeeRecord;
use App\Models\User;
use App\Models\FeeStructure;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeeRecord>
 */
class FeeRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::where('role', 'student')->inRandomOrder()->first() 
                  ?? User::factory()->student()->create();
        $feeStructure = FeeStructure::inRandomOrder()->first() ?? FeeStructure::factory()->create();

        $amount = $feeStructure->amount;
        $discountAmount = fake()->optional(0.3)->randomFloat(2, 0, $amount * 0.2);
        $lateFee = fake()->optional(0.2)->randomFloat(2, 25, 100);
        $totalAmount = $amount - ($discountAmount ?? 0) + ($lateFee ?? 0);
        
        $status = fake()->randomElement(['pending', 'partial', 'paid', 'overdue']);
        $paidAmount = match($status) {
            'paid' => $totalAmount,
            'partial' => fake()->randomFloat(2, $totalAmount * 0.3, $totalAmount * 0.8),
            default => 0,
        };
        
        $balanceAmount = $totalAmount - $paidAmount;

        return [
            'user_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'invoice_number' => 'INV-' . fake()->unique()->numerify('######'),
            'amount' => $amount,
            'discount_amount' => $discountAmount ?? 0,
            'late_fee' => $lateFee ?? 0,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'status' => $status,
            'due_date' => fake()->dateTimeBetween('-30 days', '+60 days'),
            'paid_date' => $status === 'paid' ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'payment_method' => $paidAmount > 0 ? fake()->randomElement(['cash', 'card', 'bank_transfer', 'online']) : null,
            'transaction_id' => $paidAmount > 0 ? fake()->uuid() : null,
            'payment_notes' => fake()->optional(0.3)->sentence(),
            'payment_history' => $status === 'partial' ? [
                [
                    'amount' => $paidAmount,
                    'date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                    'method' => fake()->randomElement(['cash', 'card', 'bank_transfer']),
                ]
            ] : null,
            'processed_by' => fake()->optional(0.8)->randomElement(User::where('role', 'admin')->pluck('id')->toArray()),
        ];
    }

    /**
     * Create paid fee record
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'paid_amount' => $attributes['total_amount'],
                'balance_amount' => 0,
                'paid_date' => fake()->dateTimeBetween('-30 days', 'now'),
                'payment_method' => fake()->randomElement(['cash', 'card', 'bank_transfer', 'online']),
                'transaction_id' => fake()->uuid(),
            ];
        });
    }

    /**
     * Create overdue fee record
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'paid_amount' => 0,
            'balance_amount' => $attributes['total_amount'],
        ]);
    }
}
