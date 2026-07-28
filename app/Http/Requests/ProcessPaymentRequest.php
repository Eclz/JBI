<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->can('manage-fees');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $feeRecord = $this->route('fee');

        return [
            'payment_amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . ($feeRecord->balance_amount ?? 999999.99),
            ],
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => [
                'required',
                Rule::in(['cash', 'bank_transfer', 'credit_card', 'debit_card', 'check', 'online', 'mobile_money', 'other']),
            ],
            'transaction_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('fee_records', 'transaction_id')->whereNotNull('transaction_id'),
            ],
            'payment_notes' => 'nullable|string|max:1000',
            'payment_proof' => 'required_if:payment_method,cash|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'send_receipt' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_amount.required' => 'Please enter the payment amount.',
            'payment_amount.numeric' => 'The payment amount must be a valid number.',
            'payment_amount.min' => 'The payment amount must be at least $0.01.',
            'payment_amount.max' => 'The payment amount cannot exceed the balance amount.',
            'payment_date.required' => 'Please select the payment date.',
            'payment_date.date' => 'Please enter a valid date.',
            'payment_date.before_or_equal' => 'The payment date cannot be in the future.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'transaction_id.unique' => 'This transaction ID has already been used.',
            'transaction_id.max' => 'Transaction ID cannot exceed 255 characters.',
            'payment_notes.max' => 'Payment notes cannot exceed 1000 characters.',
            'payment_proof.required_if' => 'Please upload proof of payment for cash payments.',
            'payment_proof.mimes' => 'Payment proof must be a PDF or image file (JPG, JPEG, PNG).',
            'payment_proof.max' => 'Payment proof cannot exceed 5MB.',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'payment_amount' => 'payment amount',
            'payment_date' => 'payment date',
            'payment_method' => 'payment method',
            'transaction_id' => 'transaction ID',
            'payment_notes' => 'payment notes',
            'payment_proof' => 'payment proof',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default payment date to today if not provided
        if (!$this->payment_date) {
            $this->merge([
                'payment_date' => now()->format('Y-m-d'),
            ]);
        }

        // Generate transaction ID if needed and not provided
        if ($this->payment_method === 'online' && !$this->transaction_id) {
            $this->merge([
                'transaction_id' => 'TXN-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -6)),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $feeRecord = $this->route('fee');

            // Check if fee record is already fully paid
            if ($feeRecord && $feeRecord->status === 'paid') {
                $validator->errors()->add('payment_amount', 'This fee record is already fully paid.');
            }

            // Check if payment amount is reasonable
            if ($this->payment_amount && $feeRecord) {
                if ($this->payment_amount > $feeRecord->balance_amount) {
                    $validator->errors()->add(
                        'payment_amount',
                        sprintf(
                            'Payment amount ($%s) cannot exceed balance amount ($%s).',
                            number_format($this->payment_amount, 2),
                            number_format($feeRecord->balance_amount, 2)
                        )
                    );
                }
            }
        });
    }
}
