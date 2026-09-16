<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Semester;

Schedule::call(function () {
    $semesters = Semester::where('is_active', true)->whereNotNull('end_date')->get();
    
    foreach ($semesters as $semester) {
        // If the current date has passed the semester's end date, close it
        if (now()->startOfDay()->gt($semester->end_date)) {
            $semester->update(['is_active' => false, 'is_current' => false]);
        }
    }
})->daily()->name('close-expired-semesters');
