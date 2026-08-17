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
        try {
            View::share('departments', Department::where('is_active', true)->get());
            View::share('currencyCode', SystemSetting::getSetting('default_currency', 'USD'));
        } catch (\Throwable $e) {
            View::share('departments', collect());
            View::share('currencyCode', 'USD');
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
