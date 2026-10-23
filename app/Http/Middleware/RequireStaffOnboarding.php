<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireStaffOnboarding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // Super admins and non-staff (students, parents, applicants) are not restricted by staff onboarding
        if ($user->isSuperAdmin() || in_array($user->role, ['student', 'applicant', 'parent', null, ''], true)) {
            return $next($request);
        }

        // Check if the staff user has an incomplete onboarding
        if ($user->hasPendingOnboarding()) {
            // Whitelisted routes allowed while staff onboarding is in progress
            $allowedRoutes = [
                'onboarding.staff-status',
                'password.change.form',
                'password.change',
                'logout',
                'profile.show',
                'profile.edit',
                'profile.update',
                'profile.password.update',
                'verification.notice',
                'verification.verify',
                'verification.resend',
                'verification.email.update',
                'login',
                'password.request',
                'password.email',
                'password.reset',
                'password.update',
            ];

            $routeName = $request->route()?->getName();
            if (in_array($routeName, $allowedRoutes, true)) {
                return $next($request);
            }

            $message = 'Your staff onboarding is currently in progress. Your dashboard and functional modules will unlock once onboarding is completed.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'redirect' => route('onboarding.staff-status')
                ], 403);
            }

            return redirect()->route('onboarding.staff-status')->with('warning', $message);
        }

        return $next($request);
    }
}
