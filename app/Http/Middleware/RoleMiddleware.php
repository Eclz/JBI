<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();


        $hasRole = $user->hasRole($role);
        if (!$hasRole && $role === 'admin' && $user->hasGuardRole('admin')) {
            $hasExplicitPermission = collect($request->route()?->gatherMiddleware() ?? [])
                ->contains(fn (string $middleware) => str_starts_with($middleware, 'permission:'));

            if (!$hasExplicitPermission) {
                abort(403, 'Access denied. You do not have permission to access this area.');
            }

            return $next($request);
        }

        if (!$hasRole) {
            abort(403, 'Access denied. You do not have permission to access this area.');
        }

        return $next($request);
    }
}
