<?php

namespace App\Console\Commands;

use App\Admin\Models\Website;
use App\Import\Resolver\Attributes;
use App\Mail\FeedCountsReady;
use App\Woocommerce\WoocommerceApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendProductCountEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:product_count';

    protected $description = 'Get product count';

    /**
     * @var WoocommerceApi
     */
    private $woocommerceApi;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $websites = Website::all();

        $webshopCount = [];

        foreach ($websites as $website) {
            $this->woocommerceApi = new WoocommerceApi($website);
            $hasMore = true;
            $page = 1;

            $this->info(sprintf('Current website -%s', $website->name));

            $attributeName = Attributes::ATTRIBUTE_ON_WEBSITE_CREATE[$website->configuration->locale ?? 'nl_NL']['name'];
            $action = function ($eachData) use (&$webshopCount, &$page, $attributeName, $website, &$hasMore) {
                $hasMore = true;
                $attributes = array_column((array) $eachData->attributes, 'options', 'name');
                $attributeVal = $attributes[$attributeName][0] ?? '';

                if (! isset($webshopCount[$website->name][$attributeVal])) {
                    $webshopCount[$website->name][$attributeVal] = 0;
                }
                $webshopCount[$website->name][$attributeVal]++;
            };

            try {
                while ($hasMore) {
                    $hasMore = false;

                    $this->woocommerceApi->getData('products', ['page' => $page], $action);
                    $page++;
                }
            } catch (\Exception $exception) {
                $this->error(sprintf("{$website->name} - Error - ".$exception->getMessage()));
            }
        }

        Mail::to(['jaspervdlinden@hotmail.nl'])->send(new FeedCountsReady($webshopCount));
    }
}
