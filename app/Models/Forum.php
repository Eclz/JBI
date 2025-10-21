<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'course_id',
        'department_id',
        'type',
        'access_roles',
        'is_active',
        'allow_anonymous',
        'moderated',
        'created_by',
    ];

    protected $casts = [
        'access_roles' => 'array',
        'is_active' => 'boolean',
        'allow_anonymous' => 'boolean',
        'moderated' => 'boolean',
    ];

    /**
     * Get the course (if course-specific)
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the department (if department-specific)
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get forum topics
     */
    public function topics()
    {
        return $this->hasMany(ForumTopic::class);
    }

    /**
     * Get approved topics
     */
    public function approvedTopics()
    {
        return $this->hasMany(ForumTopic::class)->where('is_approved', true);
    }

    /**
     * Scope for active forums
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for user role access
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->whereNull('access_roles')
              ->orWhereJsonContains('access_roles', $role);
        });
    }
}
