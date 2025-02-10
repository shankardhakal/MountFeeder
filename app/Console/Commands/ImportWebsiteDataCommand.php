<?php

namespace App\Console\Commands;

use App\Admin\Models\Feed;
use App\Admin\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportWebsiteDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:website';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all websites';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $websites = Website::all();

        /** @var Website $website */
        foreach ($websites as $website) {
            /** @var Feed $feed */
            foreach ($website->feeds as $feed) {
            }
        }

        return 0;
    }
}
