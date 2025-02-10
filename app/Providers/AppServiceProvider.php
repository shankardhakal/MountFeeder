<?php

namespace App\Providers;

use Illuminate\Support\Str; // Add this line
use App\Admin\Controllers\AdminTerminalController;
use Encore\Admin\Helpers\Controllers\TerminalController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            TerminalController::class,
            AdminTerminalController::class
        );
    }
}
