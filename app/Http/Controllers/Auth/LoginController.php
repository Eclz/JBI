<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'student' && $user->studentProfile) {
            $profile = $user->studentProfile;
            if ($profile->registration_deadline_at && !$profile->registration_fee_paid_at) {
                if (now()->greaterThan($profile->registration_deadline_at)) {
                    $user->update(['is_active' => false]);
                    $profile->update(['status' => 'inactive']);
                    $this->guard()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['error' => 'Your account was deactivated for missing the registration payment deadline.']);
                }
            }
        }

        if (!$user->is_active) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['error' => 'Your account is inactive. Please contact administration.']);
        }

        // Log successful login
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_type' => 'User',
            'model_id' => $user->id,
            'details' => json_encode([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]),
        ]);

        // Redirect based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.users.index'));
            case 'faculty':
                return redirect()->intended(route('faculty.courses.index'));
            case 'student':
                return redirect()->intended(route('student.dashboard'));
            default:
                return redirect()->intended(route('dashboard'));
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Log logout before actually logging out
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'logout',
                'model_type' => 'User',
                'model_id' => auth()->id(),
                'details' => json_encode([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]),
            ]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Log failed login attempt
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'failed_login',
                'model_type' => 'User',
                'model_id' => $user->id,
                'details' => json_encode([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]),
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), 5
        );
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(\Illuminate\Cache\RateLimiter::class);
    }
}
