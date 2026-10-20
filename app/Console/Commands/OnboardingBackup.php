<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class OnboardingBackup extends Command
{
    protected $signature = 'onboarding:backup';

    protected $description = 'Create a private MySQL backup before onboarding migrations and data preparation';

    public function handle(): int
    {
        $connection = config('database.connections.'.config('database.default'));
        if (($connection['driver'] ?? '') !== 'mysql') {
            $this->error('This backup command requires MySQL.');

            return self::FAILURE;
        }
        $binary = collect(['/Applications/XAMPP/xamppfiles/bin/mysqldump', '/opt/homebrew/bin/mysqldump'])
            ->first(fn ($path) => is_executable($path)) ?? 'mysqldump';
        $directory = storage_path('app/private/backups');
        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }
        $path = $directory.'/before-onboarding-'.now()->format('Ymd-His').'.sql';
        $process = new Process([
            $binary, '--single-transaction', '--skip-lock-tables',
            '--host='.$connection['host'], '--port='.$connection['port'],
            '--user='.$connection['username'], '--result-file='.$path, $connection['database'],
        ], null, ['MYSQL_PWD' => $connection['password'] ?? '']);
        $process->setTimeout(120)->run();
        if (! $process->isSuccessful()) {
            $this->error('Database backup failed. '.$process->getErrorOutput());

            return self::FAILURE;
        }
        chmod($path, 0600);
        $this->info('Backup created: '.$path);

        return self::SUCCESS;
    }
}
