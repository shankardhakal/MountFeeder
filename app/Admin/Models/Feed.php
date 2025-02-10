<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * Class Feed.
 */
class Feed extends Model implements FeedTypeAwareInterface
{
    use Notifiable;

    public const FIELD_ID = 'id';
    public const FIELD_SLUG = 'slug';
    public const FIELD_FEED_URL = 'feed_url';
    public const FIELD_CATEGORY_MAPPING = 'category_mapping';
    public const FIELD_WEBSITE_ID = 'website_id';
    public const FIELD_STORE_NAME = 'store_name';
    public const FIELD_CLEAN_AT = 'clean_at';
    public const FIELD_RUN_AT = 'run_at';
    public const FIELD_NETWORK_ID = 'network_id';
    public const FIELD_IS_ACTIVE = 'is_active';
    public const FIELD_TYPE = 'type';
    public const FIELD_LAST_IMPORT_AT = 'last_import_at';

    protected $fillable = [
        self::FIELD_WEBSITE_ID,
        self::FIELD_STORE_NAME,
        self::FIELD_FEED_URL,
        self::FIELD_CLEAN_AT,
        self::FIELD_RUN_AT,
        self::FIELD_SLUG,
        self::FIELD_CATEGORY_MAPPING,
        self::FIELD_NETWORK_ID,
        self::FIELD_IS_ACTIVE,
        self::FIELD_TYPE,
        self::FIELD_LAST_IMPORT_AT,
    ];

    /**
     * @return Collection
     */
    public function getFieldMapping(): Collection
    {
        return $this->fieldMapping;
    }

    /**
     * @return BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class, 'website_id', 'id');
    }

    /**
     * @return Collection
     */
    public function getCategoryMapping(): Collection
    {
        $categories = $this->get(self::FIELD_CATEGORY_MAPPING);
        $array = json_decode($categories, true);

        if (empty($array)) {
            return collect([]);
        }

        return collect($array);
    }

    /**
     * @param  string  $field
     * @return mixed
     */
    public function get(string $field)
    {
        return $this->$field;
    }

    /**
     * @return Website|null
     */
    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    /**
     * @return BelongsTo
     */
    public function network()
    {
        return $this->belongsTo(Network::class, 'network_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function fieldMapping()
    {
        return $this->hasMany(FieldMapping::class, 'feed_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function rules()
    {
        return $this->hasMany(Rules::class, 'feed_id', 'id');
    }

    /**
     * @return string
     */
    public function getSlug():string
    {
        return $this->get(self::FIELD_SLUG);
    }

    /**
     * @return string
     */
    public function getFeedType(): string
    {
        return $this->get(self::FIELD_TYPE);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->get(self::FIELD_ID);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->get(self::FIELD_STORE_NAME);
    }

    /**
     * @param  \DateTime  $now
     */
    public function setFeedImportedAt(\DateTime $now): self
    {
        $this->setAttribute(self::FIELD_LAST_IMPORT_AT, $now);

        return $this;
    }
}
