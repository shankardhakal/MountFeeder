<?php

declare(strict_types=1);

namespace App\Providers;

use App\Admin\Models\FeedTypeAwareInterface;
use App\Import\Enum\FeedTypeEnum;
use App\Import\FeedService\CsvFeedService;
use App\Import\FeedService\FeedServiceInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FeedServiceProvider extends ServiceProvider
{
    protected const FORMAT_TO_SERVICE_MAP = [
        FeedTypeEnum::FEED_TYPE_CSV => CsvFeedService::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            FeedServiceInterface::class,
            function (Application $application, array $arguments) {
                $feedTypeAware = $arguments[0];

                if ($feedTypeAware instanceof FeedTypeAwareInterface &&
                    isset(self::FORMAT_TO_SERVICE_MAP[$feedTypeAware->getFeedType()])) {
                    return $application->make(
                        self::FORMAT_TO_SERVICE_MAP[$feedTypeAware->getFeedType()]
                    );
                }

                throw new BindingResolutionException('UNABLE_TO_RESOLVE_FEED_SERVICE');
            }
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
