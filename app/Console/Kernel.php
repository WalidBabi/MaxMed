<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These schedules are run in a single process, so avoid doing any heavy processing.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reservations:cleanup')->everyMinute();
        $schedule->command('sitemap:generate')->daily();
        $schedule->command('campaigns:send-scheduled')->everyMinute();
        
        // Process email and notification queues every minute
        $schedule->command('queue:work --queue=notifications,emails --stop-when-empty --tries=3')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');

    }
} 