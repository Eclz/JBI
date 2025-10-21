<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
        'likes_count',
        'attachments',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Get the topic
     */
    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    /**
     * Get the author
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the parent reply (for nested replies)
     */
    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    /**
     * Get child replies
     */
    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    /**
     * Scope for approved replies
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for top-level replies (no parent)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
