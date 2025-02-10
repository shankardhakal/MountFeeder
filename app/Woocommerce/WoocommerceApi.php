<?php
/**
 * Created by PhpStorm.
 * User: ram
 * Date: 22/06/18
 * Time: 12:54 PM.
 */

namespace App\Woocommerce;

use App\Admin\Models\Website;
use App\Import\WebsiteApiDataCacheService;
use App\Logger\Logger;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\Options;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WoocommerceApi extends Client
{
    const DEFAULT_ITEMS_PER_PAGE = 100;

    const BATCH_ACTION_UPDATE = 'update';
    const BATCH_ACTION_CREATE = 'create';
    const BATCH_ACTION_DELETE = 'delete';

    public const ENDPOINT_PRODUCT_CATEGORIES = 'products/categories';
    public const ENDPOINT_PRODUCT_ATTRIBUTES = 'products/attributes';
    public const ENDPOINT_PRODUCTS = 'products';

    private array $lastErrors = [];

    private Website $website;

    private WebsiteApiDataCacheService $apiDataCacheService;

    protected array $processedSku = [

    ];

    /**
     * @return array
     */
    public function getLastErrors(): array
    {
        return $this->lastErrors;
    }

    /**
     * @var array
     */
    private array $failedProduct = [
        self::BATCH_ACTION_UPDATE => [],
        self::BATCH_ACTION_CREATE => [],
    ];

    public function __construct(Website $website, WebsiteApiDataCacheService $apiDataCacheService)
    {
        $this->lastErrors = [];

        $this->website = $website;
        $this->apiDataCacheService = $apiDataCacheService;

        parent::__construct(
            $website->url,
            $website->api_key,
            $website->api_secret,
            [
                'wp_api' => true,
                'verify_ssl' => false,
                'timeout' => 1000,
            ]
        );
    }

    /**
     * @param  bool  $responseCache
     *
     * @return Collection
     */
    public function getAttributes(bool $responseCache = true): Collection
    {
        return $this->getData(
            self::ENDPOINT_PRODUCT_ATTRIBUTES,
            [],
            $responseCache
        );
    }

    /**
     * @param  bool  $responseCache
     *
     * @return Collection
     */
    public function getCategories(bool $responseCache = true): Collection
    {
        return $this->getData(
            self::ENDPOINT_PRODUCT_CATEGORIES,
            [],
            $responseCache
        );
    }

    /**
     * @param  string  $endpoint
     * @param  array  $filter
     * @param  bool  $cacheResponse
     * @param  bool  $paging
     *
     * @return Collection
     */
    public function getData(string $endpoint, array $filter = [], bool $cacheResponse = true, bool $paging = true): Collection
    {
        if ($paging) {
            $pageNumber = 1;
            $filter['per_page'] = self::DEFAULT_ITEMS_PER_PAGE;
        }

        $collection = new Collection();

        do {
            if ($paging) {
                $filter['page'] = $pageNumber++;
            }

            $cacheKey = $this->getRequestCacheKey(
                $endpoint,
                $filter,
                []
            );

            if (
                $cacheResponse &&
                $this->apiDataCacheService
                    ->fetchFromCache($cacheKey)
                    ->isNotEmpty()
            ) {
                $data = $this->apiDataCacheService->fetchFromCache($cacheKey);
            } else {
                $data = collect(
                    json_decode(
                        json_encode(
                            $this->get($endpoint, $filter)
                        ),
                        true
                    )
                );

                if ($cacheResponse) {
                    $this->apiDataCacheService->storeToCache($cacheKey, $data);
                }
            }

            $collection = $collection->merge($data);
        } while ($paging && $data->count() === self::DEFAULT_ITEMS_PER_PAGE);

        return $collection;
    }

    /**
     * @param  string  $apiEndpoint
     * @param  array  $filter
     * @param  array  $body
     *
     * @return string
     */
    private function getRequestCacheKey(string $apiEndpoint, array $filter = [], array $body = []): string
    {
        $hashData = json_encode(
            [
                $this->website->url,
                Options::VERSION,
                $apiEndpoint,
                $filter,
                $body,
            ]
        );

        return
            sprintf(
                'api_response_cache.%s',
                hash('sha256', $hashData)
            );
    }

    /**
     * @param  string  $attributeTermSlug
     * @param  string  $attributeName
     *
     * @return Collection
     */
    public function getAttributeTerm(string $attributeTermSlug, string $attributeName): Collection
    {
        $shopAttributes = $this->getAttributes();

        $attributesId = $shopAttributes
            ->pluck('id', 'name')
            ->get($attributeName);

        if ($attributesId === null) {
            return new Collection();
        }

        $shopAttributeTerms = $this->getData(
            sprintf('products/attributes/%u/terms', $attributesId),
            [
                'search' => $attributeTermSlug,
            ]
        );

        foreach ($shopAttributeTerms->toArray() as $attributeTerm) {
            if ($attributeTermSlug === $attributeTerm['slug']) {
                return collect($attributeTerm);
            }
        }

        return new Collection();
    }

    public function deleteProducts(array $productIds)
    {
        $deleteBatches = array_chunk($productIds, self::DEFAULT_ITEMS_PER_PAGE);
        foreach ($deleteBatches as $deleteBatch) {
            try {
                parent::post('products/batch', [self::BATCH_ACTION_DELETE=> $deleteBatch]);
            } catch (\Exception $exception) {
                parent::post('products/batch', [self::BATCH_ACTION_DELETE => $deleteBatch]);
            }
        }
    }

    /**
     * @param  array  $productsCollection
     * @param  array  $existingSku
     */
    public function handleProducts(array $productsCollection, array $existingSku): int
    {
        $newProducts = [];
        $updateProducts = [];

        $publishedProducts = 0;
        /**
         * @var $product WoocommerceProduct
         */
        foreach ($productsCollection as $product) {

            if(isset($this->processedSku[$product->getSku()])){
                continue;
            }

            $this->processedSku[$product->getSku()] =1;

            if (isset($existingSku[$product->sku])) {
                $updateProducts[] = array_filter([
                    'regular_price' => $product->price,
                    'sale_price'    => $product->salePrice,
                    'id'            => $existingSku[$product->sku],
                    'categories'    => $product->categories,
                    'attributes' => $product->attributes,
                ]);
            } else {
                $newProducts[] = $product->getExportData();
            }

            if (count($updateProducts) === self::DEFAULT_ITEMS_PER_PAGE) {
                $publishedProducts += $this->sendProductBatch($updateProducts, self::BATCH_ACTION_UPDATE);
                $updateProducts = [];
            }

            if (count($newProducts) === self::DEFAULT_ITEMS_PER_PAGE) {
                $publishedProducts += $this->sendProductBatch($newProducts, self::BATCH_ACTION_CREATE);
                $newProducts = [];
            }
        }

        $publishedProducts += $this->sendProductBatch($updateProducts, self::BATCH_ACTION_UPDATE);
        $publishedProducts += $this->sendProductBatch($newProducts, self::BATCH_ACTION_CREATE);

        return $publishedProducts;
    }

    /**
     * @param  array  $products
     * @param  string  $action
     * @param  false  $isRetry
     * @return int
     */
    private function sendProductBatch(array $products, string $action, $isRetry = false): int
    {
        if (empty($products)) {
            return 0;
        }

        try {
            $res = parent::post('products/batch', [$action => $products]);

            if (stripos(json_encode($res), 'error') !== false) {
                $hash = sha1(json_encode($res));
                $this->lastErrors[$hash] = $res;

                if (! isset($this->lastErrors[$hash])) {
                    Logger::error('ERROR_PUBLISHING_PRODUCT', ['error_response'=>$res, 'action'=>$action]);
                }

                return 0;
            }

            return count($products);
        } catch (\Exception $exception) {
            if ($isRetry) {
                Log::error('PRODUCT_PUBLISH_FAILED', ['product' => $res]);
            }
        }

        return 0;
    }

    /**
     * @param  int  $attributeId
     * @param  int  $attributeTermId
     * @param  array  $updateData
     */
    public function updateAttributeTerm(int $attributeId, int $attributeTermId, array $updateData): void
    {
        $endpoint = sprintf(
            '%s/%u/%s/%u',
            self::ENDPOINT_PRODUCT_ATTRIBUTES,
            $attributeId,
            'terms',
            $attributeTermId
        );

        $this->put($endpoint, $updateData);
    }
}
