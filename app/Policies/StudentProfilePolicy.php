<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isFaculty();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentProfile $studentProfile): bool
    {
        // Admin can view any student profile
        if ($user->isAdmin()) {
            return true;
        }

        // Faculty can view students in their department
        if ($user->isFaculty()) {
            return $user->facultyProfile?->department_id === $studentProfile->department_id;
        }

        // Students can view their own profile
        return $user->id === $studentProfile->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentProfile $studentProfile): bool
    {
        // Admin can update any student profile
        if ($user->isAdmin()) {
            return true;
        }

        // Students can update their own profile
        return $user->id === $studentProfile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentProfile $studentProfile): bool
    {
        return $user->isAdmin();
    }
}
