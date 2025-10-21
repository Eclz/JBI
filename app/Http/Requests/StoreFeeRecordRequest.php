<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeeRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-fees');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'student')->where('is_active', true);
                }),
            ],
            'fee_structure_id' => [
                'required',
                'exists:fee_structures,id',
                Rule::exists('fee_structures', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'amount' => 'required|numeric|min:0|max:999999.99',
            'discount_amount' => 'nullable|numeric|min:0|max:999999.99|lte:amount',
            'late_fee' => 'nullable|numeric|min:0|max:999999.99',
            'due_date' => 'required|date|after_or_equal:today',
            'payment_notes' => 'nullable|string|max:1000',
            'auto_calculate' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a student.',
            'user_id.exists' => 'The selected student is invalid or inactive.',
            'fee_structure_id.required' => 'Please select a fee structure.',
            'fee_structure_id.exists' => 'The selected fee structure is invalid or inactive.',
            'amount.required' => 'Please enter the fee amount.',
            'amount.numeric' => 'The amount must be a valid number.',
            'amount.min' => 'The amount must be at least 0.',
            'amount.max' => 'The amount cannot exceed $999,999.99.',
            'discount_amount.numeric' => 'The discount must be a valid number.',
            'discount_amount.min' => 'The discount must be at least 0.',
            'discount_amount.lte' => 'The discount cannot exceed the base amount.',
            'late_fee.numeric' => 'The late fee must be a valid number.',
            'late_fee.min' => 'The late fee must be at least 0.',
            'due_date.required' => 'Please select a due date.',
            'due_date.date' => 'Please enter a valid date.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'payment_notes.max' => 'Payment notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If auto_calculate is true, fetch amount from fee structure
        if ($this->auto_calculate && $this->fee_structure_id) {
            $feeStructure = \App\Models\FeeStructure::find($this->fee_structure_id);
            if ($feeStructure) {
                $this->merge([
                    'amount' => $feeStructure->amount,
                    'due_date' => $feeStructure->due_date ?? now()->addDays(30),
                ]);
            }
        }

        // Set default values
        $this->merge([
            'discount_amount' => $this->discount_amount ?? 0,
            'late_fee' => $this->late_fee ?? 0,
        ]);
    }

    /**
     * Get the validated data with calculated fields.
     */
    public function validatedWithCalculations(): array
    {
        $validated = $this->validated();

        $amount = $validated['amount'];
        $discountAmount = $validated['discount_amount'] ?? 0;
        $lateFee = $validated['late_fee'] ?? 0;

        $totalAmount = $amount - $discountAmount + $lateFee;

        return array_merge($validated, [
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'balance_amount' => $totalAmount,
            'status' => 'pending',
            'invoice_number' => $this->generateInvoiceNumber(),
        ]);
    }

    /**
     * Generate a unique invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastRecord = \App\Models\FeeRecord::whereDate('created_at', today())->latest()->first();
        $sequence = $lastRecord ? (intval(substr($lastRecord->invoice_number, -6)) + 1) : 1;

        return sprintf('%s-%s-%06d', $prefix, $date, $sequence);
    }
}
