<?php

namespace App\Providers;

use App\Export\WoocommerceDataExport;
use App\Woocommerce\WoocommerceApi;
use Illuminate\Support\ServiceProvider;

class WoocommerceDataExportProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WoocommerceDataExport::class, function ($app, $arguments) {
            $woocommerceApi = $this->app->make(WoocommerceApi::class, $arguments);

            return new WoocommerceDataExport($woocommerceApi);
        });
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
