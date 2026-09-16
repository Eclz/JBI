<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'isbn', 'category', 'total_copies', 'available_copies', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
    ];
}
