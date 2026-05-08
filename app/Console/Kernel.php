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
        'App\Console\Commands\CallRoute',
        'App\Console\Commands\SendIncompleteProfileReminders',
        'App\Console\Commands\ScrapeJobFeeds',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('queue:work --stop-when-empty')->everyFiveMinutes()->withoutOverlapping(5)->sendOutputTo(storage_path() . '/logs/queue-jobs.log');
        $schedule->command('route:call check-package-validity')->daily()->withoutOverlapping(5)->sendOutputTo(storage_path() . '/logs/queue-jobs.log');
		$schedule->command('route:call send-alerts')->daily()->withoutOverlapping(5)->sendOutputTo(storage_path() . '/logs/queue-jobs.log');
		$schedule->command('send:incomplete-profile-reminders')->weekly()->withoutOverlapping(5)->sendOutputTo(storage_path() . '/logs/incomplete-profile-reminders.log');
		
		// Alberta sources
		$schedule->command('jobs:scrape ahs')->hourly()->withoutOverlapping(10)->sendOutputTo(storage_path() . '/logs/job-scraper-ahs.log');
		$schedule->command('jobs:scrape covenant')->everyThreeHours()->withoutOverlapping(10)->sendOutputTo(storage_path() . '/logs/job-scraper-covenant.log');
		$schedule->command('jobs:scrape ab-ltc')->everyThreeHours()->withoutOverlapping(10)->sendOutputTo(storage_path() . '/logs/job-scraper-ab-ltc.log');
		$schedule->command('jobs:scrape ab-agencies')->daily()->withoutOverlapping(10)->sendOutputTo(storage_path() . '/logs/job-scraper-ab-agencies.log');
		
		// Sync employer-posted jobs to pSEO
		$schedule->command('jobs:sync-legacy-to-medo')->everyFifteenMinutes()->withoutOverlapping(5)->sendOutputTo(storage_path() . '/logs/job-sync-legacy.log');
		
		// National maintenance
		$schedule->command('jobs:expire')->daily()->sendOutputTo(storage_path() . '/logs/jobs-expire.log');
		$schedule->command('indexnow:ping')->everyFifteenMinutes()->sendOutputTo(storage_path() . '/logs/indexnow-ping.log');
		
		// Note: cache:prune-stale-pages is handled by standard Laravel file cache expiration when using Cache::remember. 
		// If using Redis or other drivers, standard TTL handles this.
		// Note: sitemap:build is generated dynamically.
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

}
