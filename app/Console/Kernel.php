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
        $schedule->command('sitemap:ultimate --images')->daily();
        $schedule->command('campaigns:send-scheduled')->everyMinute();
        $schedule->command('users:cleanup-unverified --days=30')->daily();
        $schedule->command('users:send-verification-reminders --reminder-after=10')->daily();

        // Notify superadmins about due/expiring expenses daily at configured hour
        $hour = (int) config('expenses.notify.send_hour_local', 9);
        $time = str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00';
        $schedule->command('expenses:notify-due')->dailyAt($time);
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