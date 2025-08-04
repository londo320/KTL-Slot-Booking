<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schedule = app(Schedule::class);

foreach ($schedule->events() as $event) {
    echo $event->command . " | Cron: " . $event->expression . PHP_EOL;
}
