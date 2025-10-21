<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FixPasswordHashing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:passwords {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all unhashed passwords in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will update all user passwords. Do you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting password hash fix...');

        $users = User::all();
        $fixedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Check if password needs hashing
            if ($this->needsHashing($user->password)) {
                // Hash the password
                $hashedPassword = Hash::make($user->password);

                // Update directly in database to avoid model events
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => $hashedPassword]);

                $this->line("Fixed password for user: {$user->email}");
                $fixedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->info("Password fix completed!");
        $this->info("Fixed: {$fixedCount} passwords");
        $this->info("Skipped: {$skippedCount} passwords (already hashed)");

        return 0;
    }

    /**
     * Check if a password needs hashing
     */
    private function needsHashing($password)
    {
        // Check if it's already a bcrypt hash
        if (strlen($password) === 60 && substr($password, 0, 4) === '$2y$') {
            return false;
        }

        // Check if it's already an Argon2 hash
        if (substr($password, 0, 8) === '$argon2i' || substr($password, 0, 8) === '$argon2id') {
            return false;
        }

        return true;
    }
}
