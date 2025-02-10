<?php

declare(strict_types=1);

namespace App\Repository;

use App\Admin\Models\Feed;
use App\Admin\Models\FieldMapping;
use App\Woocommerce\WoocommerceProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;

class FieldMappingRepository extends BaseRepository
{
    /**
     * @param  int  $feedId
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function findWoocommerceCategoriesByFeedId(int $feedId)
    {
        return $this->findWhere(
            [
                FieldMapping::FIELD_FEED_ID           => $feedId,
                FieldMapping::FIELD_WOOCOMMERCE_FIELD => WoocommerceProduct::CATEGORIES,
            ]
        );
    }

    /**
     * @param  int  $networkId
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function findWoocommerceCategoriesByNetworkId(int $networkId): ?FieldMapping
    {
        return $this->findWhere(
            [
                FieldMapping::FIELD_NETWORK_ID        => $networkId,
                FieldMapping::FIELD_WOOCOMMERCE_FIELD => WoocommerceProduct::CATEGORIES,
            ]
        )->first();
    }

    /**
     * @param  int  $networkId
     * @param  string  $woocommerceField
     *
     * @return FieldMapping|null
     */
    public function findMappingsByWoocommerceFieldAndNetworkId(int $networkId, string $woocommerceField): ?FieldMapping
    {
        return $this->findWhere(
            [
                FieldMapping::FIELD_NETWORK_ID        => $networkId,
                FieldMapping::FIELD_WOOCOMMERCE_FIELD => $woocommerceField,
            ]
        )->first();
    }

    /**
     * @param  int  $feedId
     * @param  string  $woocommerceField
     *
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function findMappingsByWoocommerceFieldAndFeedId(int $feedId, string $woocommerceField)
    {
        return $this->findWhere(
            [
                FieldMapping::FIELD_FEED_ID       => $feedId,
                FieldMapping::FIELD_WOOCOMMERCE_FIELD => $woocommerceField,
            ]
        );
    }

    /**
     * @return string
     */
    public function model(): string
    {
        return FieldMapping::class;
    }
}
