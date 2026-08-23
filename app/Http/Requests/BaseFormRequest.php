<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error_count' => $validator->errors()->count(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Get common validation rules for text fields.
     */
    protected function getTextRules(int $min = 1, int $max = 255, bool $required = true): array
    {
        $rules = ['string'];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = "min:{$min}";
        $rules[] = "max:{$max}";
        
        return $rules;
    }

    /**
     * Get common validation rules for email fields.
     */
    protected function getEmailRules(bool $required = true, bool $unique = false, string $table = null, string $column = 'email', int $ignoreId = null): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'string';
        $rules[] = 'email:rfc,dns';
        $rules[] = 'max:255';
        
        if ($unique && $table) {
            $uniqueRule = "unique:{$table},{$column}";
            if ($ignoreId) {
                $uniqueRule .= ",{$ignoreId}";
            }
            $rules[] = $uniqueRule;
        }
        
        return $rules;
    }

    /**
     * Get common validation rules for phone fields.
     */
    protected function getPhoneRules(bool $required = false): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'string';
        $rules[] = 'max:30';
        $rules[] = 'regex:/^[0-9\-\+\s\(\)\.]{7,25}$/';
        
        return $rules;
    }

    /**
     * Get common validation rules for date fields.
     */
    protected function getDateRules(bool $required = true, string $before = null, string $after = null): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'date';
        
        if ($before) {
            $rules[] = "before:{$before}";
        }
        
        if ($after) {
            $rules[] = "after:{$after}";
        }
        
        return $rules;
    }

    /**
     * Get common validation rules for file uploads.
     */
    protected function getFileRules(array $mimes = ['pdf'], int $maxSize = 2048, bool $required = false): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'file';
        $rules[] = 'mimes:' . implode(',', $mimes);
        $rules[] = "max:{$maxSize}";
        
        return $rules;
    }

    /**
     * Get common validation rules for image uploads.
     */
    protected function getImageRules(int $maxSize = 2048, bool $required = false, array $dimensions = null): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'image';
        $rules[] = 'mimes:jpeg,png,jpg,gif';
        $rules[] = "max:{$maxSize}";
        
        if ($dimensions) {
            $dimensionRule = 'dimensions:';
            $dimensionParts = [];
            
            if (isset($dimensions['min_width'])) {
                $dimensionParts[] = "min_width={$dimensions['min_width']}";
            }
            if (isset($dimensions['min_height'])) {
                $dimensionParts[] = "min_height={$dimensions['min_height']}";
            }
            if (isset($dimensions['max_width'])) {
                $dimensionParts[] = "max_width={$dimensions['max_width']}";
            }
            if (isset($dimensions['max_height'])) {
                $dimensionParts[] = "max_height={$dimensions['max_height']}";
            }
            
            $rules[] = $dimensionRule . implode(',', $dimensionParts);
        }
        
        return $rules;
    }

    /**
     * Get common validation rules for numeric fields.
     */
    protected function getNumericRules(bool $required = true, float $min = null, float $max = null, int $decimals = 2): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'numeric';
        
        if ($min !== null) {
            $rules[] = "min:{$min}";
        }
        
        if ($max !== null) {
            $rules[] = "max:{$max}";
        }
        
        if ($decimals >= 0) {
            $rules[] = "decimal:0,{$decimals}";
        }
        
        return $rules;
    }

    /**
     * Get common validation rules for boolean fields.
     */
    protected function getBooleanRules(bool $required = false): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'boolean';
        
        return $rules;
    }

    /**
     * Sanitize input data.
     */
    protected function sanitizeInput(): void
    {
        $input = $this->all();
        
        // Trim string values
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
                // Convert empty strings to null
                if ($value === '') {
                    $value = null;
                }
            }
        });
        
        $this->replace($input);
    }

    /**
     * Get validation rules for JBI-specific ID formats.
     */
    protected function getJbiIdRules(string $type, bool $required = true, bool $unique = false, string $table = null, string $column = null, int $ignoreId = null): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        
        $rules[] = 'string';
        
        // Define patterns for different ID types
        $patterns = [
            'student' => '/^JBI\d{4,6}$/',
            'employee' => '/^JBI\d{3,5}$/',
            'admission' => '/^ADM\d{6}$/',
            'invoice' => '/^INV-\d{6}$/',
        ];
        
        if (isset($patterns[$type])) {
            $rules[] = 'regex:' . $patterns[$type];
        }
        
        if ($unique && $table && $column) {
            $uniqueRule = "unique:{$table},{$column}";
            if ($ignoreId) {
                $uniqueRule .= ",{$ignoreId}";
            }
            $rules[] = $uniqueRule;
        }
        
        return $rules;
    }
}
