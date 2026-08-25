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
            $timezone = \App\Models\SystemSetting::getSetting('timezone', config('app.timezone'));
            if (in_array($timezone, timezone_identifiers_list(), true)) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }

            $currencyCode = \App\Models\SystemSetting::getSetting('default_currency', 'USD');
            $acceptedCurrencies = \App\Models\SystemSetting::getSetting('accepted_currencies', [$currencyCode]);
            $acceptedCurrencies = is_array($acceptedCurrencies) ? $acceptedCurrencies : json_decode($acceptedCurrencies, true);
            View::share('currencyCode', $currencyCode);
            View::share('systemCurrency', $currencyCode);
            View::share('acceptedCurrencies', $acceptedCurrencies ?: [$currencyCode]);
            View::share('admissionWindow', \App\Models\SystemSetting::admissionWindow());
            View::share('departments', Department::all());
        } catch (\Throwable $e) {
            View::share('currencyCode', 'USD');
            View::share('systemCurrency', 'USD');
            View::share('acceptedCurrencies', ['USD']);
            View::share('admissionWindow', [
                'isOpen' => true,
                'status' => 'open',
                'start' => null,
                'end' => null,
                'timezone' => config('app.timezone'),
            ]);
            View::share('departments', collect());
        }
    }
}
