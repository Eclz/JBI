<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PaymentReferenceNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fee_record_id',
        'fee_structure_id',
        'prn_number',
        'fee_item_name',
        'amount',
        'payment_type',
        'status',
        'generated_at',
        'expires_at',
        'paid_at',
        'transaction_reference',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feeRecord()
    {
        return $this->belongsTo(FeeRecord::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Helper accessor to determine if PRN is expired (30-day limit)
     */
    public function getIsExpiredAttribute()
    {
        if ($this->status === 'paid') {
            return false;
        }

        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            // Auto update status if expired
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    /**
     * Generate a unique 10-digit PRN string
     */
    public static function generateUniquePrn()
    {
        do {
            $prn = 'PRN' . rand(100000000, 999999999);
        } while (self::where('prn_number', $prn)->exists());

        return $prn;
    }
}
