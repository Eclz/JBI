<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Semester;
use App\Models\User;
use App\Services\StaffOnboardingService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PrepareOnboarding extends Command
{
    protected $signature = 'onboarding:prepare {--registration-start=} {--registration-end=} {--repair-staff} {--protect-files}';

    protected $description = 'Prepare existing onboarding records and explicitly configure course registration dates';

    public function handle(): int
    {
        if ($this->option('registration-start') || $this->option('registration-end')) {
            $validator = validator([
                'start' => $this->option('registration-start'), 'end' => $this->option('registration-end'),
            ], ['start' => 'required|date_format:Y-m-d', 'end' => 'required|date_format:Y-m-d|after_or_equal:start']);
            if ($validator->fails()) {
                $this->error($validator->errors()->first());

                return self::FAILURE;
            }
            if (Semester::where('is_current', true)->count() !== 1) {
                $this->error('Exactly one current semester is required. Set it in Academic Setup first.');

                return self::FAILURE;
            }
            DB::transaction(function () {
                $semester = Semester::where('is_current', true)->lockForUpdate()->firstOrFail();
                $old = $semester->getAttributes();
                $end = Carbon::parse($this->option('registration-end'));
                $semester->registration_start = $this->option('registration-start');
                $semester->registration_end = $this->option('registration-end');
                // Keep the semester open through its registration period so the daily closure job cannot undo it.
                if (! $semester->end_date || $semester->end_date->lt($end)) {
                    $semester->end_date = $end;
                }
                $semester->is_active = true;
                $semester->save();
                AuditLog::create([
                    'action' => 'onboarding_registration_window_updated', 'model_type' => Semester::class,
                    'model_id' => $semester->id, 'old_values' => $old, 'new_values' => $semester->getAttributes(),
                ]);
            });
            $this->info('Current semester registration dates updated, including the full closing day.');
        }
        if ($this->option('repair-staff')) {
            $officerId = User::where('is_active', true)->get()->first(fn ($user) => $user->hasPermission('hr_onboarding', 'edit'))?->id;
            User::where('is_active', true)->whereNotIn('role', ['student', 'parent', 'applicant', ''])->each(function ($user) use ($officerId) {
                if (! $user->must_change_password && \Illuminate\Support\Facades\Hash::check('ABCxyz,.?123', $user->password)) {
                    $user->update(['must_change_password' => true]);
                }
                StaffOnboardingService::ensure($user, $officerId);
            });
            $this->info('Active staff HR records and checklists reconciled. No invitations were sent.');
        }
        if ($this->option('protect-files')) {
            $moved = 0;
            Application::each(function ($application) use (&$moved) {
                $paths = array_merge(array_values($application->documents ?? []), [$application->payment_proof]);
                foreach ($paths as $item) {
                    $path = is_array($item) ? ($item['path'] ?? null) : $item;
                    if (! $path || str_contains($path, '..') || ! Storage::disk('public')->exists($path)) {
                        continue;
                    }
                    $contents = Storage::disk('public')->get($path);
                    if (Storage::disk('local')->exists($path) && Storage::disk('local')->get($path) !== $contents) {
                        throw new \RuntimeException('Conflicting private file; no public file removed: '.$path);
                    }
                    if (! Storage::disk('local')->put($path, $contents)
                        || Storage::disk('local')->get($path) !== $contents) {
                        throw new \RuntimeException('Could not verify private copy: '.$path);
                    }
                    if (! Storage::disk('public')->delete($path)) {
                        throw new \RuntimeException('Private copy verified, but the old public file could not be removed: '.$path);
                    }
                    $moved++;
                }
            });
            $this->info("Protected {$moved} existing application files.");
        }

        return self::SUCCESS;
    }
}
