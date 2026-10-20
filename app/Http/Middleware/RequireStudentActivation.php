<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireStudentActivation
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->role !== 'student') {
            return $next($request);
        }

        if ($user->hasVerifiedEmail()) {
            if ($user->isAdmitted()) {
                return $next($request);
            }
            $onboarding = ['student.home', 'student.dashboard', 'student.dashboard.acknowledge', 'student.admission-letter.show'];
            $restrictedModule = $request->routeIs('student.*', 'library.*', 'forums.*', 'messages.*', 'facilities.*');
            if (!$restrictedModule || in_array($request->route()?->getName(), $onboarding, true)) {
                return $next($request);
            }
            $message = 'Your student services will unlock once admissions activates your registration. Continue your admission steps on the dashboard.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'redirect' => route('student.dashboard')], 403);
            }
            return redirect()->route('student.dashboard')->with('warning', $message);
        }

        $allowed = ['student.home', 'student.dashboard', 'verification.notice', 'verification.verify',
            'verification.resend', 'verification.email.update', 'logout',
            'password.request', 'password.email', 'password.reset', 'password.update'];
        if (in_array($request->route()?->getName(), $allowed, true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Activate your account by verifying your email before continuing.',
                'redirect' => route('student.dashboard')], 403);
        }

        return redirect()->route('student.dashboard')->with('warning', 'Activate your account to unlock applications and student services.');
    }
}
