<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get setting value with proper type casting
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Scope by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get setting by key
     */
    public static function getSetting($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->typed_value : $default;
    }

    public static function registrationWindow(): array
    {
        $timezone = static::getSetting('timezone', config('app.timezone'));
        $now = Carbon::now($timezone);
        $enabled = filter_var(static::getSetting('registration_enabled', true), FILTER_VALIDATE_BOOLEAN);
        $startValue = static::getSetting('registration_open_at');
        $endValue = static::getSetting('registration_close_at');
        $start = $startValue ? Carbon::parse($startValue, $timezone) : null;
        $end = $endValue ? Carbon::parse($endValue, $timezone) : null;

        $isOpen = $enabled
            && (!$start || $now->greaterThanOrEqualTo($start))
            && (!$end || $now->lessThanOrEqualTo($end));

        $status = 'open';
        if (!$enabled) {
            $status = 'disabled';
        } elseif ($start && $now->isBefore($start)) {
            $status = 'scheduled';
        } elseif ($end && $now->isAfter($end)) {
            $status = 'closed';
        }

        return compact('isOpen', 'status', 'start', 'end', 'timezone', 'now');
    }
}
