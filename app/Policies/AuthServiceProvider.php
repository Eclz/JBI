<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\FacultyProfile;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\FeeRecord;
use App\Policies\UserPolicy;
use App\Policies\CoursePolicy;
use App\Policies\StudentProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Course::class => CoursePolicy::class,
        StudentProfile::class => StudentProfilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('manage-system', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-academic', function (User $user) {
            return $user->isAdmin() || $user->isFaculty();
        });

        Gate::define('view-reports', function (User $user) {
            return $user->isAdmin() || $user->isFaculty();
        });
    }
}
