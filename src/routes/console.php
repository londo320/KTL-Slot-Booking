<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('slots:generate', ['--days' => 30])
        ->dailyAt('00:15')
        ->withoutOverlapping()
        ->timezone('Europe/London')
        ->appendOutputTo(storage_path('logs/slots_generate.log'));

Schedule::command('app:auto-release-slots')
        ->everyFifteenMinutes()
        ->withoutOverlapping()
        ->timezone('Europe/London')
        ->appendOutputTo(storage_path('logs/auto_release_slots.log'));