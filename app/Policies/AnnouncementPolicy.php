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
        if ($user->isAdmin() || $announcement->created_by === $user->id) {
            return true;
        }

        $isLive = $announcement->is_published
            && (!$announcement->expires_at || $announcement->expires_at->isFuture());

        if (!$isLive) {
            return false;
        }

        $roles = $announcement->target_roles;

        return empty($roles) || in_array($user->role, $roles, true);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isFaculty();
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || $announcement->created_by === $user->id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->update($user, $announcement);
    }
}
