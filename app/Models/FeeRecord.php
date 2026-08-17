<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FeeRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'fee_structure_id',
        'invoice_number',
        'amount',
        'type',
        'discount_amount',
        'late_fee',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'transaction_id',
        'payment_notes',
        'payment_history',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'payment_history' => 'array',
    ];

    protected $dates = [
        'due_date',
        'paid_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the student that owns the fee record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user that owns the fee record (alias for student).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the fee structure that this record is based on.
     */
    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Get the user who processed the payment.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include paid records.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include pending records.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include partial records.
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope a query to only include overdue records.
     */
    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'overdue')
              ->orWhere(function ($subQ) {
                  $subQ->where('status', 'pending')
                       ->where('due_date', '<', now());
              });
        });
    }

    /**
     * Scope a query to filter by academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->whereHas('feeStructure', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    /**
     * Scope a query to filter by semester.
     */
    public function scopeForSemester($query, $semesterId)
    {
        return $query->whereHas('feeStructure', function ($q) use ($semesterId) {
            $q->where('semester_id', $semesterId);
        });
    }

    /**
     * Check if the fee record is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->status === 'paid') {
            return false;
        }

        return $this->due_date->isPast() && in_array($this->status, ['pending', 'partial']);
    }

    /**
     * Check if the fee record is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->balance_amount <= 0;
    }

    /**
     * Check if the fee record has partial payment.
     */
    public function hasPartialPayment(): bool
    {
        return $this->paid_amount > 0 && $this->balance_amount > 0;
    }

    /**
     * Get the payment completion percentage.
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }

        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    /**
     * Get the status badge class for display.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'bg-success',
            'partial' => 'bg-warning',
            'pending' => $this->isOverdue() ? 'bg-danger' : 'bg-secondary',
            'overdue' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the display status text.
     */
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'pending' && $this->isOverdue()) {
            return 'Overdue';
        }

        return match ($this->status) {
            'paid' => 'Paid',
            'partial' => 'Partial',
            'pending' => 'Pending',
            'overdue' => 'Overdue',
            default => 'Unknown',
        };
    }

    /**
     * Get the days until due date.
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get the formatted payment history.
     */
    public function getFormattedPaymentHistoryAttribute(): array
    {
        if (!$this->payment_history) {
            return [];
        }

        return collect($this->payment_history)->map(function ($payment) {
            return [
                'amount' => number_format($payment['amount'] ?? 0, 2),
                'date' => isset($payment['date']) ? Carbon::parse($payment['date'])->format('M d, Y') : 'N/A',
                'method' => ucfirst(str_replace('_', ' ', $payment['method'] ?? 'Unknown')),
                'transaction_id' => $payment['transaction_id'] ?? 'N/A',
                'notes' => $payment['notes'] ?? '',
                'processed_by' => isset($payment['processed_by']) ? User::find($payment['processed_by'])?->name : 'System',
                'processed_at' => isset($payment['processed_at']) ? Carbon::parse($payment['processed_at'])->format('M d, Y g:i A') : 'N/A',
            ];
        })->toArray();
    }

    /**
     * Calculate and update late fee if applicable.
     */
    public function calculateLateFee(): void
    {
        if (!$this->due_date || !$this->feeStructure || $this->status === 'paid') {
            return;
        }

        $feeStructure = $this->feeStructure;

        if ($feeStructure->late_fee_amount > 0 && $feeStructure->late_fee_days) {
            $gracePeriodEnd = $this->due_date->addDays($feeStructure->late_fee_days);

            if (now()->isAfter($gracePeriodEnd) && $this->late_fee == 0) {
                $this->late_fee = $feeStructure->late_fee_amount;
                $this->total_amount = $this->amount - $this->discount_amount + $this->late_fee;
                $this->balance_amount = $this->total_amount - $this->paid_amount;
                $this->status = $this->balance_amount <= 0 ? 'paid' : ($this->paid_amount > 0 ? 'partial' : 'overdue');
                $this->save();
            }
        }
    }

    /**
     * Update the status based on payment amounts.
     */
    public function updateStatus(): void
    {
        if ($this->balance_amount <= 0) {
            $this->status = 'paid';
            if (!$this->paid_date) {
                $this->paid_date = now();
            }
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->isOverdue()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($feeRecord) {
            // Generate invoice number if not provided
            if (!$feeRecord->invoice_number) {
                $feeRecord->invoice_number = 'INV-' . now()->format('Ymd') . '-' . str_pad(static::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function ($feeRecord) {
            // Update status before saving
            $feeRecord->updateStatus();
        });
    }
}
