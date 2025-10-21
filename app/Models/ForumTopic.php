<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'forum_id',
        'title',
        'content',
        'user_id',
        'is_pinned',
        'is_locked',
        'is_approved',
        'views_count',
        'replies_count',
        'last_reply_at',
        'last_reply_by',
        'tags',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_approved' => 'boolean',
        'last_reply_at' => 'datetime',
        'tags' => 'array',
    ];

    /**
     * Get the forum
     */
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Get the author
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the last reply author
     */
    public function lastReplyAuthor()
    {
        return $this->belongsTo(User::class, 'last_reply_by');
    }

    /**
     * Get topic replies
     */
    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }

    /**
     * Get approved replies
     */
    public function approvedReplies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id')->where('is_approved', true);
    }

    /**
     * Scope for approved topics
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pinned topics
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
