<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Admin\Models\Feed;
use App\Admin\Models\FieldMapping;
use Illuminate\Console\Command;

class ImportFeedMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-feed-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $feeds = Feed::all();

        foreach ($feeds as $feed) {
            $mapping = json_decode($feed->feed_mapping, true);

            if (null !== $mapping) {
                foreach ($mapping as $woocommerce=>$csv) {
                    if (is_array($csv)) {
                        continue;
                    }

                    $field = new FieldMapping(['woocommerce_field'=>$woocommerce, 'source_field'=>$csv, 'feed_id'=>$feed->id]);
                    $field->save();
                }
            }
        }
    }
}
