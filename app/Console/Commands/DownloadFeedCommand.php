<?php

namespace App\Console\Commands;

use App\Admin\Models\Feed;
use App\Import\Download\FileDownloader;
use App\Import\Dto\FeedDownloadTo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repository\FeedRepository;

class DownloadFeedCommand extends Command
{
    protected $signature = 'feed:download {feed_slug}';
    protected $description = 'Download a feed';

    private FileDownloader $downloader;
    private FeedRepository $feedRepository;

    public function __construct(FileDownloader $downloader, FeedRepository $feedRepository)
    {
        $this->downloader = $downloader;
        $this->feedRepository = $feedRepository;
        parent::__construct();
    }

    public function handle()
    {
        $feedSlug = trim((string) $this->argument('feed_slug'));

        // Debugging output
        Log::info("Starting feed download for slug: $feedSlug");

        /** @var Feed|null $feed */
        $feed = $this->feedRepository->findWhere(
            [Feed::FIELD_SLUG => $feedSlug]
        )->first();

        if (!$feed) {
            Log::error('FEED_DOWNLOAD_COMMAND_FAILED', [
                'reason'    => 'non_existing_feed',
                'feed_slug' => $feedSlug,
            ]);
            $this->error("Feed not found: $feedSlug");
            return 0;
        }

        // Fix Logger context
        Log::withContext(['feed' => $feed]);

        Log::info('FEED_DOWNLOAD_COMMAND_START');
        $this->info('Downloading feed...');

        $downloadTo = FeedDownloadTo::buildFrom($feed);

        try {
            $this->downloader->download($downloadTo);
            Log::info('FEED_DOWNLOAD_SUCCESS', ['feed_slug' => $feedSlug]);
            $this->info('Feed downloaded successfully.');
        } catch (\Exception $exception) {
            Log::error('FEED_DOWNLOAD_COMMAND_FAILED', [
                'reason' => $exception->getMessage(),
            ]);
            $this->error('Download failed: ' . $exception->getMessage());
        }

        return 0;
    }
}
