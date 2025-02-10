<?php

namespace App\Providers;

use App\Import\WebsiteApiDataCacheService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WebsiteApiApiDataCacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            WebsiteApiDataCacheService::class,
            function (Application $application, $arguments) {
                return new WebsiteApiDataCacheService();
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
