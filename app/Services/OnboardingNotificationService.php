<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class OnboardingNotificationService
{
    public static function notify(string $module, string $title, string $message, string $url): void
    {
        User::where('is_active', true)->whereNotIn('role', ['student', 'parent', 'applicant', ''])->with(['roleCatalog', 'roles'])->get()
            ->filter(fn (User $user) => $user->hasPermission($module, 'view'))
            ->each(fn (User $user) => Notification::create([
                'user_id' => $user->id,
                'type' => 'application',
                'title' => $title,
                'message' => $message,
                'action_url' => $url,
                'priority' => 'high',
            ]));
    }
}
