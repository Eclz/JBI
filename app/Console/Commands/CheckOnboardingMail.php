<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckOnboardingMail extends Command
{
    protected $signature = 'onboarding:mail-check';

    protected $description = 'Check SMTP connection/authentication without sending an email';

    public function handle(): int
    {
        if (config('mail.default') !== 'smtp') {
            $this->error('SMTP is not the configured default mailer.');

            return self::FAILURE;
        }
        try {
            $transport = Mail::mailer()->getSymfonyTransport();
            $transport->getStream()->setTimeout(10);
            $transport->start();
            $transport->stop();
            $this->info('SMTP connection and authentication succeeded. No email was sent; inbox delivery is not verified.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('SMTP connection/authentication failed. Check mail credentials, host, port and TLS settings.');

            return self::FAILURE;
        }
    }
}
