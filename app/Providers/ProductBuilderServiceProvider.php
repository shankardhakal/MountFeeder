<?php

namespace App\Providers;

use App\Import\InspireupliftProductBuilder;
use App\Import\ProductBuilder;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ProductBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Log the argument to verify it's being passed correctly
        \Log::debug('Registering ProductBuilderServiceProvider');

        // Bind InspireupliftProductBuilder to ProductBuilder in singleton
        $this->app->singleton(ProductBuilder::class, function (Application $application, $arguments) {
            \Log::debug('ProductBuilder argument:', ['arguments' => $arguments]);  // Log the argument

            // Ensure that the argument[0] exists and is not null or empty
            if (isset($arguments[0])) {
                return new InspireupliftProductBuilder($arguments[0]);
            }

            // If the argument[0] doesn't exist, handle the error gracefully
            \Log::error('Invalid arguments passed to ProductBuilder constructor');
            throw new \InvalidArgumentException('Invalid arguments passed to ProductBuilder constructor');
        });

        // Bind InspireupliftProductBuilder directly (optional, if needed)
        $this->app->singleton(InspireupliftProductBuilder::class, function (Application $application, $arguments) {
            \Log::debug('InspireupliftProductBuilder argument:', ['arguments' => $arguments]);  // Log the argument

            if (isset($arguments[0])) {
                return new InspireupliftProductBuilder($arguments[0]);
            }

            \Log::error('Invalid arguments passed to InspireupliftProductBuilder constructor');
            throw new \InvalidArgumentException('Invalid arguments passed to InspireupliftProductBuilder constructor');
        });

        // Additional bindings can be added here if needed
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Optional: You can add boot logic here if required
    }
}
