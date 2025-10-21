<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VerifyPasswordHashing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that all user passwords are properly hashed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking password hashing for all users...');
        $this->info('');

        $users = User::all();
        $issues = 0;

        foreach ($users as $user) {
            $isHashed = $this->isPasswordHashed($user->password);

            if ($isHashed) {
                $this->info("✓ {$user->email} - Password properly hashed");
            } else {
                $this->error("✗ {$user->email} - Password NOT hashed!");
                $issues++;
            }
        }

        $this->info('');

        if ($issues === 0) {
            $this->info('🎉 All passwords are properly hashed!');
        } else {
            $this->error("⚠️  Found {$issues} users with unhashed passwords.");
            $this->info('Run: php artisan fix:passwords --force');
        }

        return $issues === 0 ? 0 : 1;
    }

    /**
     * Check if password is properly hashed
     */
    private function isPasswordHashed($password)
    {
        // Check for bcrypt hash (starts with $2y$ and is 60 characters)
        if (strlen($password) === 60 && substr($password, 0, 4) === '$2y$') {
            return true;
        }

        // Check for Argon2 hash
        if (substr($password, 0, 8) === '$argon2i' || substr($password, 0, 8) === '$argon2id') {
            return true;
        }

        return false;
    }
}
