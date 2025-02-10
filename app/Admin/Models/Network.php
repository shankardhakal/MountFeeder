<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Network extends Model implements FeedTypeAwareInterface
{
    public const FIELD_SAMPLE_FEED_URL = 'sample_feed_url';
    public const FIELD_FIELDS = 'fields';
    public const FIELD_FEED_FORMAT = 'feed_format';

    /**
     * @return HasMany
     */
    public function fieldMapping()
    {
        return $this->hasMany(FieldMapping::class);
    }

    /**
     * @return HasMany
     */
    public function feeds()
    {
        return $this->hasMany(Feed::class, 'network_id', 'id');
    }

    /**
     * @return string
     */
    public function getFeedType(): string
    {
        return 'csv';

        return $this->getAttribute(self::FIELD_FEED_FORMAT);
    }
}
