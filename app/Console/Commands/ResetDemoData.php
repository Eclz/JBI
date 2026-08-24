<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class ResetDemoData extends Command
{
    protected $signature = 'demo:reset
        {--force : Permit the destructive reset without an interactive confirmation}';

    protected $description = 'Delete all application data and recreate the approved JBI demo dataset';

    public function handle(): int
    {
        if (config('app.data_mode') !== 'demo') {
            $this->error('Demo reset is available only when APP_DATA_MODE=demo.');

            return self::FAILURE;
        }

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

        $connection = config('database.default');
        $prefix = (string) config("database.connections.{$connection}.prefix", '');

        if ($prefix !== '') {
            Schema::disableForeignKeyConstraints();

            try {
                foreach (Schema::getTableListing() as $table) {
                    $physicalTable = str_contains($table, '.')
                        ? substr($table, strrpos($table, '.') + 1)
                        : $table;

                    if (str_starts_with($physicalTable, $prefix)) {
                        Schema::drop(substr($physicalTable, strlen($prefix)));
                    }
                }
            } finally {
                Schema::enableForeignKeyConstraints();
            }

            $exitCode = Artisan::call('migrate', [
                '--force' => true,
                '--seed' => true,
                '--seeder' => DemoDatabaseSeeder::class,
            ]);
        } else {
            $exitCode = Artisan::call('migrate:fresh', [
                '--force' => true,
                '--seeder' => DemoDatabaseSeeder::class,
            ]);
        }

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
