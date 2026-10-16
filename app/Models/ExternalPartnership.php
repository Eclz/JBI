<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalPartnership extends Model
{
    protected $fillable = [
        'managed_by',
        'organization_name',
        'partnership_type',
        'objectives',
        'funding_amount',
        'status',
    ];

    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'managed_by');
    }
}
