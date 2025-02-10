<?php

declare(strict_types=1);

namespace App\Import\Download;

use App\Events\FeedFileDownloadComplete;
use App\Exceptions\FeedDownloadException;
use App\Import\Dto\FeedDownloadTo;
use App\Logger\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class FileDownloader
{
    private Client $client;

    /**
     * Downloader constructor.
     * @param  Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param  FeedDownloadTo  $downloadTo
     * @return bool
     *
     * @throws FeedDownloadException
     */
    public function download(FeedDownloadTo $downloadTo): bool
    {
        if (file_exists($downloadTo->getDownloadLocationTo())) {
            return true;
        }

        try {
            $response = $this->client->get(
                $downloadTo->getUrl(),
                [
                    RequestOptions::SINK => $downloadTo->getDownloadLocationTo(),
                    RequestOptions::HEADERS => ['Accept-Content' => 'application/zip'],
                ]
            );

            Logger::info(
                'FEED_DOWNLOAD_SUCCESS',
                [
                    'status_code' => $response->getStatusCode(),
                ]
            );

            FeedFileDownloadComplete::dispatch($downloadTo);

            return true;
        } catch (GuzzleException $exception) {
            Log::error(
                'FEED_DOWNLOAD_FAILURE',
                [
                    'error_message' => $exception->getMessage(),
                    'url' => $downloadTo->getUrl(),
                ]
            );

            throw new FeedDownloadException($exception->getMessage());
        }
    }
}
