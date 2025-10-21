<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class CheckMissingRoutes extends Command
{
    protected $signature = 'audit:missing-routes {--json : Return output in JSON format}';
    protected $description = 'Scan controllers and views for route references and identify any missing or invalid ones';

    public function handle()
    {
        $routeNames = collect(Route::getRoutes())->mapWithKeys(function ($route) {
            return [$route->getName() => true];
        });

        $usedRoutes = [];
        $missingRoutes = [];

        $files = (new Finder())->in([
            base_path('resources/views'),
            base_path('app/Http/Controllers'),
        ])->name('*.php')->name('*.blade.php');

        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());
            $lines = explode("\n", $content);
            foreach ($lines as $lineNumber => $line) {
                // Skip commented lines
                if (Str::startsWith(trim($line), ['//', '#', '{{--'])) {
                    continue;
                }

                // Match route('name') calls
                if (preg_match_all("/route\(['\"](.*?)['\"]\)/", $line, $matches)) {
                    foreach ($matches[1] as $routeName) {
                        $usedRoutes[] = $routeName;
                        if (!isset($routeNames[$routeName])) {
                            $missingRoutes[] = [
                                'route' => $routeName,
                                'file' => $file->getRelativePathname(),
                                'line' => $lineNumber + 1,
                            ];
                        }
                    }
                }
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($missingRoutes, JSON_PRETTY_PRINT));
        } else {
            if (empty($missingRoutes)) {
                $this->info("No missing routes found.");
            } else {
                $this->warn("Missing route references found:");
                foreach ($missingRoutes as $miss) {
                    $this->line("[{$miss['file']} @ line {$miss['line']}] route('{$miss['route']}')");
                }
            }
        }

        return 0;
    }
}
