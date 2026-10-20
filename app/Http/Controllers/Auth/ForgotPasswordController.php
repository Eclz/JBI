<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(\Illuminate\Http\Request $request)
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }
        $request->validate(['email' => 'required|string|email|max:254']);
        try {
            $response = $this->broker()->sendResetLink($request->only('email'), function ($user, $token) {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($user, $token));
            });
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('Password reset email failed', ['exception' => $exception->getMessage()]);
            try {
                $user = \App\Models\User::where('email', $request->email)->first();
                if ($user) $this->broker()->deleteToken($user);
            } catch (\Throwable $cleanupError) {
                \Illuminate\Support\Facades\Log::warning('Could not clear unsuccessful reset token.');
            }
            return back()->withInput($request->only('email'))->with('warning', 'We could not send the reset email. Try again shortly or contact admissions. Your account is unchanged.');
        }
        return $response === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
