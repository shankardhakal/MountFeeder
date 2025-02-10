<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Admin\Models\Feed;
use App\Admin\Models\FeedImportLog;
use App\Events\FeedImportEvent;
use App\Import\FeedFileHelper;
use App\Import\Resolver\Attributes;
use App\Import\WebsiteLocale;
use App\Logger\Logger;
use App\Woocommerce\WoocommerceApi;
use App\Woocommerce\WoocommerceProduct;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveOldProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean {shop_slug} {attribute_slug=pa_verkoper} {--D|delete_all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans woocommerce products for a given shop';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * @var WoocommerceApi
     */
    private $woocommerceApi;

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        Logger::info('DELETE_IS_DISABLED');
        exit(1);
        $this->info('Cleaning started.');
        $shopSlug = $this->argument('shop_slug');
        $deleteAll = $this->option('delete_all');
        // $this->sendSlackMessage(json_encode(['clean' => true, 'shopslug' => $shopSlug, 'deleteAll' => $deleteAll]));
        // $feed = Feed::getFeedBySlug($shopSlug);

        if (null === $feed) {
            $this->error('No feed found.');
            event(new FeedImportEvent(FeedImportLog::ERROR, "[{$shopSlug}][CLEANUP] Feed does not exist."));

            return 1;
        }

        event(new FeedImportEvent(
            FeedImportLog::INFO,
            "[{$shopSlug}][CLEANUP] Cleaning up products from wordpress started.",
            $feed
        ));

        $this->info('Cleaning shop started at '.Carbon::now()->toDateTimeString());

        $this->woocommerceApi = app(WoocommerceApi::class, [$feed->website]);

        $locale = $feed->website->configuration->locale ?? WebsiteLocale::LOCALE_nl_NL;
        $shopAttributeSlug = Attributes::ATTRIBUTE_ON_WEBSITE_CREATE[$locale]['name'];
        if ($deleteAll) {
            // $this->sendSlackMessage("$feed->slug: Delete everything");
            $this->info('Delete all');

            $productsToClean = $this->woocommerceApi->getProductIds($shopSlug, 'pa_product_site', true);

            $cleanList = array_values($productsToClean);

            // $this->sendSlackMessage(sprintf('%u will be deleted.', count($cleanList)));
            $this->woocommerceApi->deleteProducts($cleanList);
            event(new FeedImportEvent(FeedImportLog::INFO, 'Deleted everything.', $feed));

            return 1;
        }

        $options = [
            'slug'             => $feed->slug,
            'name'             => $feed->store_name,
            'for_cleanup'      => true,
            'category_mapping' => [],
            'locale'           => $locale,
            'feed_mapping'     => $feed->fieldMapping->pluck('source_field', 'woocommerce_field')->toArray(),
        ];

        $networkMapping = $feed->network->fieldMapping->pluck('source_field', 'woocommerce_field')->toArray();
        $options['feed_mapping'] = array_filter(array_merge($networkMapping, $options['feed_mapping']));

        $skuKeyAtFeed = $options['feed_mapping'][WoocommerceProduct::SKU] ?? $options['feed_mapping'][WoocommerceProduct::ALTERNATE_SKU] ?? null;

        if (empty($skuKeyAtFeed)) {
            Log::error('INCOMPLETE_FEED_MAPPING', ['message'=>'MISSING_SKU_MAPPING']);

            return 1;
        }

        $filePath = FeedFileHelper::getFeedDownloadLocation($feed);

        $skuAtFeed = [];
        foreach ($this->csvFeedService->extractColumns($filePath, [$skuKeyAtFeed]) as $column) {
            $sku = trim($column[$skuKeyAtFeed] ?? null);

            if (isset($sku) && ! empty($sku)) {
                $skuAtFeed[$sku] = 1;
            }
        }

        if (empty($skuAtFeed)) {
            // $this->sendSlackMessage(sprintf('%s : Clean aborted. There were no products in the feed.', $shopSlug));
            exit(1);
        }

        try {
            $attributeTerm = $this->woocommerceApi->getAttributeTerm($shopSlug, $shopAttributeSlug);

            if ($attributeTerm->isEmpty()) {
                throw new \RuntimeException('SHOP_NOT_FOUND');
            }

            $filter = [
                'attribute_term' => $attributeTerm->get('id'),
            ];

            $totalProductsAtShop = $this->woocommerceApi->getData(WoocommerceApi::ENDPOINT_PRODUCTS, $filter);

            $cleanList = [];
            $emptySkus = [];

            foreach ($totalProductsAtShop->pluck('id', 'sku')->toArray() as $sku => $id) {
                if (! isset($skuAtFeed[$sku])) {
                    $cleanList[] = $id;
                }

                if (empty($sku)) {
                    $emptySkus[] = $sku;
                }
            }

            $currentMessage = sprintf(
                '%s : Out of %u products. %u will be removed. Of which %u had empty sku.Reason: not found in feed.',
                $shopSlug,
                count($totalProductsAtShop),
                count($cleanList),
                count($emptySkus)
            );
            // $this->sendSlackMessage($currentMessage);

            event(new FeedImportEvent(FeedImportLog::INFO, "[{$shopSlug}][CLEANUP] {$currentMessage}", $feed));

            $this->info($currentMessage);
            $this->woocommerceApi->deleteProducts($cleanList);
        } catch (\Exception $exception) {
            $this->error('An error occured running command. Error '.$exception->getMessage().$exception->getTraceAsString());

            event(new FeedImportEvent(
                FeedImportLog::ERROR,
                "[{$shopSlug}][CLEANUP] ".' An error occured running command. Error '.$exception->getMessage(),
                $feed
            ));

            return 1;
        }

        $currentMessage = 'Clean completed.';
        $this->info($currentMessage);
        event(new FeedImportEvent(FeedImportLog::INFO, "[{$shopSlug}][CLEANUP] {$currentMessage}", $feed));

        return 1;
    }
}
