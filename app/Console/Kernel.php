<?php

namespace App\Console;

use App\Admin\Models\Feed;
use App\Console\Commands\ImportFeedCommand;
use App\Console\Commands\RemoveOldProductsCommand;
use App\Logger\Logger;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RemoveOldProductsCommand::class,
        ImportFeedCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        try {
            $feedsRun = Feed::where('is_active', true)
                ->get(['run_at', 'clean_at', 'slug', 'store_name']);
        } catch (\Exception $exception) {
            Logger::alert('Database query did not go well.', ['exception' => $exception->getMessage()]);
            $feedsRun = [];
        }

        foreach ($feedsRun as $feed) {
            $timeZone = config('app.timezone', 'UTC');

            $schedule->command("import:feed {$feed->slug}")
                     ->cron($feed->run_at)
                     ->timezone($timeZone)
                     ->description("Import products for shop {$feed->store_name}");

            $schedule->command("clean {$feed->slug}")
                ->cron($feed->clean_at)
                ->description('Run clean up');
        }

        $commandsAtCaches = Cache::pull('schedule-commands', []);

        foreach ($commandsAtCaches as $commandsAtCache) {
            $schedule->command($commandsAtCache)
                ->runInBackground();
        }

        Cache::forget('schedule-commands');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
