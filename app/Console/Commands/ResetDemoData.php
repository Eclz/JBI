<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDemoData extends Command
{
    protected $signature = 'demo:reset
        {--force : Permit the destructive reset without an interactive confirmation}';

    protected $description = 'Delete all application data and recreate the approved JBI demo dataset';

    public function handle(): int
    {
        if (! config('app.demo_reset_allowed', false)) {
            $this->error('Demo reset is disabled. Set DEMO_RESET_ALLOWED=true for an approved demo environment.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm(
            'This permanently deletes every application record and recreates only demo data. Continue?'
        )) {
            $this->warn('Demo reset cancelled.');

            return self::SUCCESS;
        }

        $this->warn('Resetting the database and creating the approved demo dataset...');

        $exitCode = Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seeder' => DemoDatabaseSeeder::class,
        ]);

        $this->output->write(Artisan::output());

        if ($exitCode !== self::SUCCESS) {
            $this->error('Demo reset failed. Restore the latest database backup before retrying.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Demo reset complete. Retained operator accounts:');
        $this->line('  admin@jbiuniversity.com / password123');
        $this->line('  faculty@jbiuniversity.com / password123');
        $this->line('  student@jbiuniversity.com / password123');
        $this->line('  parent@jbiuniversity.com / password123');

        return self::SUCCESS;
    }
}
