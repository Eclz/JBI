<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

/**
 * Auto-discovered by Laravel (App\Models\Announcement -> App\Policies\AnnouncementPolicy).
 */
class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->hasPermission('announcements', 'view') || $announcement->created_by === $user->id) {
            return true;
        }

        $isLive = $announcement->is_published
            && (!$announcement->expires_at || $announcement->expires_at->isFuture());

        if (!$isLive) {
            return false;
        }

        $roles = $announcement->target_roles;

        return empty($roles) || collect($roles)->contains(fn ($role) => $user->hasRole($role));
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('announcements', 'create') || $user->isFaculty();
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('announcements', 'edit') || $announcement->created_by === $user->id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('announcements', 'delete')
            || $announcement->created_by === $user->id;
    }
}
