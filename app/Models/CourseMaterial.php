<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'external_url',
        'is_downloadable',
        'is_published',
        'order',
        'uploaded_by',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'is_published' => 'boolean',
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the course that owns the material.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who uploaded the material.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get human readable file size.
     */
    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        if ($this->type === 'link') {
            return $this->external_url;
        }

        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return null;
    }

    /**
     * Check if the material is available.
     */
    public function getIsAvailableAttribute()
    {
        if ($this->type === 'link') {
            return !empty($this->external_url);
        }

        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get the material icon based on type.
     */
    public function getIconAttribute()
    {
        $icons = [
            'document' => 'bi-file-earmark-text',
            'video' => 'bi-play-circle',
            'audio' => 'bi-music-note',
            'image' => 'bi-image',
            'link' => 'bi-link-45deg',
        ];

        return $icons[$this->type] ?? 'bi-file';
    }

    /**
     * Get the material color based on type.
     */
    public function getColorAttribute()
    {
        $colors = [
            'document' => 'primary',
            'video' => 'danger',
            'audio' => 'success',
            'image' => 'warning',
            'link' => 'info',
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    /**
     * Scope to get published materials.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to order by custom order and creation date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }
}
