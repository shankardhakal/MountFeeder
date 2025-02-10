<?php

use App\Admin\Controllers\WebsiteConfigurationController;
use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::registerAuthRoutes();

Route::group(
    [
        'prefix'     => config('admin.route.prefix'),
        'namespace'  => config('admin.route.namespace'),
        'middleware' => config('admin.route.middleware'),
    ],
    function (Router $router) {
        // Home route for the admin panel
        $router->get('/', 'HomeController@index')->name('admin.home');
        
        // ========================
        // Feeds Routes (Non-Nested)
        // ========================
        $router->resource('/feeds', "\App\Admin\Controllers\FeedsController");
        
        // ====================
        // Websites Routes
        // ====================
        $router->resource('/websites', "\App\Admin\Controllers\WebsitesController");
        
        // ========================
        // Nested Feeds Routes 
        // (Under Websites - Unique Names)
        // ========================
        $router->resource('/websites/{websiteId}/feeds', "\App\Admin\Controllers\FeedsController")
            ->names([
                'index'   => 'websites.feeds.index',
                'create'  => 'websites.feeds.create',
                'store'   => 'websites.feeds.store',
                'show'    => 'websites.feeds.show',
                'edit'    => 'websites.feeds.edit',
                'update'  => 'websites.feeds.update',
                'destroy' => 'websites.feeds.destroy'
            ]);

        // ========================
        // Feed Operations
        // ========================
        $router->post('/feeds/wpcleanup', '\App\Admin\Controllers\FeedsController@wpCleanup');
        $router->post('/feeds/import-feed', "\App\Admin\Controllers\FeedsController@importFeed");

        // ========================
        // Network Routes
        // ========================
        $router->resource('networks', '\App\Admin\Controllers\NetworkController');
        $router->get('/networks/{networkId}/add-mapping', '\App\Admin\Controllers\NetworkController@addMapping');
        
        // ========================
        // Feed Mapping & Rules
        // ========================
        $router->get('/feeds/{feedId}/add-mapping', '\App\Admin\Controllers\FeedsController@showMapping');
        $router->get('/feeds/{feedId}/add-rules', '\App\Admin\Controllers\RulesController@rulesListing');

        // ========================
        // Category Mapping
        // ========================
        $router->get('/feeds/category-mappings/{feedId}', '\App\Admin\Controllers\CategoryMappingController@addMapping')
            ->middleware('fetch-woocommerce-categories');

        // ========================
        // Website Configurations
        // ========================
        $router->resource('website-configurations', WebsiteConfigurationController::class);

        // ========================
        // API Routes
        // ========================
        Route::group(
            ['prefix' => 'api'],
            function (Router $router) {
                $router->post('/add-network-field-mapping', '\App\Admin\Controllers\NetworkController@putFieldMapping')
                    ->name('add-network-field-mapping');
                $router->post(
                    '/add-category-mapping',
                    '\App\Admin\Controllers\CategoryMappingController@addCategoryMapping'
                )->name('add-category-mapping');
                $router->post('/add-feed-field-mapping', '\App\Admin\Controllers\FeedsController@addFieldMapping')
                    ->name('add-feed-field-mapping');
                $router->post('/add-feed-rules', '\App\Admin\Controllers\RulesController@addRule')
                    ->name('add-feed-rules');
                $router->delete('/remove-feed-rules', '\App\Admin\Controllers\RulesController@removeRule')
                    ->name('remove-feed-rules');
            }
        );

        // ========================
        // Log Viewer (Debugging)
        // ========================
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    }
);