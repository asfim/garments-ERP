<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Biometric ZKTeco Auto-Sync every minute in background
Schedule::command('biometric:sync')->everyMinute()->withoutOverlapping();

