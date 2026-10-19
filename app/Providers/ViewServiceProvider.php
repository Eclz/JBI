<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\SystemSetting;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Console commands such as migrate must not require an initialized database.
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            View::share('departments', Department::where('is_active', true)->get());
        } catch (\Throwable $e) {
            View::share('departments', collect());
        }

        // Share notifications with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $notifications = Auth::user()->notifications()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $unreadCount = Auth::user()->notifications()
                    ->where('is_read', false)
                    ->count();

                $view->with([
                    'headerNotifications' => $notifications,
                    'unreadNotificationsCount' => $unreadCount
                ]);
            }
        });
    }
}
