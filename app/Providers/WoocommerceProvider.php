<?php

namespace App\Providers;

use App\Admin\Models\Website;
use App\Import\WebsiteApiDataCacheService;
use App\Woocommerce\WoocommerceApi;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class WoocommerceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            WoocommerceApi::class,
            function (Application $application, $arguments) {

                /** @var Website $website */
                $website = $arguments[0];

                return new WoocommerceApi($website, new WebsiteApiDataCacheService());
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
