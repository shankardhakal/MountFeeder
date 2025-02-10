<?php

declare(strict_types=1);

namespace App\Import;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class WebsiteApiDataCacheService.
 */
class WebsiteApiDataCacheService
{
    private const CACHE_TTL = 60 * 60 * 20; // 20 hours

    /**
     * @param  string  $cacheKey
     * @param  Collection  $entityData
     * @param  int  $cacheTtl
     */
    public function storeToCache(string $cacheKey, Collection $entityData, int $cacheTtl = self::CACHE_TTL): bool
    {
        return Cache::put($cacheKey, $entityData, $cacheTtl);
    }

    /**
     * @param  string  $cacheKey
     * @return Collection
     */
    public function fetchFromCache(string $cacheKey): Collection
    {
        return Cache::get(
            $cacheKey,
            new Collection()
        );
    }
}
