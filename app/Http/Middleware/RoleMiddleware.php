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


        if ($user->role !== $role) {
            // Redirect based on user's actual role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard')->with('error', 'Access denied. You do not have permission to access this area.');
                case 'faculty':
                    return redirect()->route('dashboard')->with('error', 'Access denied. You do not have permission to access this area.');
                case 'student':
                    return redirect()->route('dashboard')->with('error', 'Access denied. You do not have permission to access this area.');
                default:
                    return redirect()->route('dashboard')->with('error', 'Access denied. Invalid user role.');
            }
        }

        return $next($request);
    }
}
