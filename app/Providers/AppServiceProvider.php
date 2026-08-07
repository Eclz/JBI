<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Department;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        try {
            $currencyCode = \App\Models\SystemSetting::getSetting('default_currency', 'UGX');
            View::share('currencyCode', $currencyCode);
            View::share('systemCurrency', $currencyCode);
            View::share('departments', Department::all());
        } catch (\Throwable $e) {
            View::share('currencyCode', 'UGX');
            View::share('systemCurrency', 'UGX');
            View::share('departments', collect());
        }
    }
}

