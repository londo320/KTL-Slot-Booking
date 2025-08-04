<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        \Log::info('Scheduling AutoReleaseSlots');

        $schedule->command('slots:generate --days=7')
            ->everyMinute()
            ->description('Auto-generate slots from templates for the next week');

        $schedule->command('app:auto-release-slots')
            ->everyMinute()
            ->description('Auto-release slots based on SlotReleaseRules');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}