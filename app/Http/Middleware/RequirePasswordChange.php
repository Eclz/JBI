<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            // Allow access to password change routes and logout
            $allowedRoutes = [
                'password.change.form',
                'password.change',
                'logout',
                'profile.password.update'
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('password.change.form')
                    ->with('warning', 'You must change your password before continuing.');
            }
        }

        return $next($request);
    }
}
