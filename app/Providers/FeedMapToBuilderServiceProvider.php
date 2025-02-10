<?php

namespace App\Providers;

use App\Import\FeedMapToBuilderService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FeedMapToBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FeedMapToBuilderService::class, function (Application $application, $argument) {
            return new FeedMapToBuilderService();
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
