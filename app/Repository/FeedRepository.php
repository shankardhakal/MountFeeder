<?php

declare(strict_types=1);

namespace App\Repository;

use App\Admin\Models\Feed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;

class FeedRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Feed::class;
    }

    /**
     * @param $id
     * @param  array|string[]  $columns
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->findById((int) $id, $columns);
    }

    /**
     * @param  int  $id
     */
    public function findById(int $id, $columns = ['*']): ?Feed
    {
        return $this->findWhere(
            [
                Feed::FIELD_ID => $id,
            ],
            $columns
        )->first();
    }

    /**
     * Get feed from shopSlug.
     *
     * @param  string  $feedSlug
     * @throws ModelNotFoundException
     *
     * @return Feed
     */
    public function getFeedBySlug(string $feedSlug): Feed
    {
        $feed = $this->findWhere(
            [
                Feed::FIELD_SLUG => $feedSlug,
            ]
        )->first();

        if (! $feed instanceof Feed) {
            throw new ModelNotFoundException('FEED_NOT_FOUND');
        }

        return $feed;
    }
}
