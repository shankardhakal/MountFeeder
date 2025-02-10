<?php

declare(strict_types=1);

namespace App\Admin\Models;

interface FeedTypeAwareInterface
{
    /**
     * @return string
     */
    public function getFeedType(): string;
}
