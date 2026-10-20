<?php

namespace App\Services;

use App\Mail\AccountCreatedSetupPassword;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AccountInvitationService
{
    public static function send(User $user): bool
    {
        try {
            $token = Password::broker()->createToken($user);
            Mail::to($user->email)->send(new AccountCreatedSetupPassword($user, $token));
            $user->forceFill(['setup_email_sent_at' => now(), 'setup_email_error' => null])->save();

            return true;
        } catch (\Throwable $exception) {
            $user->forceFill(['setup_email_error' => 'Email could not be sent. Check mail settings and resend the invitation.'])->save();
            Log::error('Account invitation failed', ['user_id' => $user->id, 'error' => $exception->getMessage()]);

            return false;
        }
    }
}
