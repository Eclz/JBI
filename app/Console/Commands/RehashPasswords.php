<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RehashPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:rehash-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rehash all user passwords to use Bcrypt with a default password in Laravel 12.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->warn('No users found in the database.');
            return self::SUCCESS;
        }

        $this->info('Starting to rehash passwords for ' . $users->count() . ' users...');

        $defaultPassword = 'password'; // Default password for all users

        foreach ($users as $user) {
            // Only rehash if the password is not a valid Bcrypt hash
            if (!preg_match('/^\$2[ayb]\$.{56}$/', $user->password)) {
                $user->password = Hash::make($defaultPassword);
                $user->force_password_reset = true; // Force password reset (assuming this column exists)
                $user->save();

                $this->info("Rehashed password for user {$user->email} with default password.");
            } else {
                $this->line("Skipping user {$user->email}: Password is already a valid Bcrypt hash.");
            }
        }

        $this->info('Password rehashing completed successfully!');
        $this->warn("All users now have the default password: '{$defaultPassword}' where rehashed.");
        $this->warn('Users with rehashed passwords will be required to reset their passwords on next login.');

        return self::SUCCESS;
    }
}
