<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);

        $middleware->prependToPriorityList(
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\RequireStudentActivation::class,
        );

        // You can also register other middleware here
        $middleware->web(append: [
            \App\Http\Middleware\RequireStudentActivation::class,
            \App\Http\Middleware\RequirePasswordChange::class,
        ]);

        $middleware->api(append: [
            // Add any API middleware here
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withProviders([
        \App\Providers\ViewServiceProvider::class,
    ])->create();
