<?php

namespace App\Providers;

use App\Admin\Models\Feed;
use App\Admin\Models\Network;
use App\Events\FeedFileDownloadComplete;
use App\Events\ImportCompleted;
use App\Events\PrepareImport;
use App\Import\Consolidator\FeedConsolidator;
use App\Listeners\DownloadFeed;
use App\Listeners\ExtractDownloadedFile;
use App\Listeners\UpdateImportCompleted;
use App\Observers\FeedObserver;
use App\Observers\NetworkObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        PrepareImport::class => [
            DownloadFeed::class,
        ],
        FeedFileDownloadComplete::class =>[
            ExtractDownloadedFile::class,
            FeedConsolidator::class,
            ],
        ImportCompleted::class => [UpdateImportCompleted::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Feed::observe(FeedObserver::class);
        Network::observe(NetworkObserver::class);
    }
}
