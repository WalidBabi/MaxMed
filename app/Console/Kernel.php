<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SafeMigrations::class,
    ];

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
        $schedule->command('users:cleanup-unverified --days=30')->daily();
        $schedule->command('users:send-verification-reminders --reminder-after=10')->daily();
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