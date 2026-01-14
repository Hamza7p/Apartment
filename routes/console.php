<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->afterResolving(Schedule::class, function (Schedule $schedule) {
    $schedule->command('reservations:expire-requests')->daily();
});

app()->afterResolving(Schedule::class, function (Schedule $schedule) {
    $schedule->command('reservations:end')->daily();
});

// ToDo:
/**
 * add schedule for apartment availability
 */
