<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StopImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:stop {feedSlug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stops a running import';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shopSlug = $this->argument('feedSlug');
        $command = sprintf("ps -fe | grep 'php artisan import:feed %s' |  awk '{print $2}'", $shopSlug);
        exec($command, $result);

        exec(sprintf("ps -fe | grep 'php artisan import:feed %s'", $shopSlug), $slack);

        // $this->sendSlackMessage("PS request " . implode(PHP_EOL, $slack));

        foreach ($result as $pid) {
            exec(sprintf('kill -9 %s', $pid), $kill);
        }

        return 1;
    }
}
