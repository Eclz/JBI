<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'library_item_id', 'user_id', 'borrowed_at', 'due_at', 'returned_at', 'renewals', 'status', 'fines'
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_at' => 'date',
        'returned_at' => 'date',
    ];

    public function libraryItem()
    {
        return $this->belongsTo(LibraryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
