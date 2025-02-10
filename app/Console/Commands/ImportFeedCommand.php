<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Admin\Models\Feed;
use App\Events\ImportCompleted;
use App\Events\PrepareImport;
use App\Exceptions\FeedDownloadException;
use App\Export\FeedExportTo;
use App\Export\WoocommerceDataExport;
use App\Import\Dto\FileParseTo;
use App\Import\FeedFileHelper;
use App\Import\FeedMapToBuilderService;
use App\Import\Parser\ParserContext;
use App\Import\ProductTransformer;
use App\Logger\Logger;
use App\Repository\FeedRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImportFeedCommand extends Command
{
    const DEFAULT_ITEMS_TO_IMPORT = 'all';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:feed {feed_slug} {--limit=9223372036854775807}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import feed';

    private FeedRepository $feedRepository;

    /**
     * Create a new command instance.
     *
     * @param  FeedRepository  $feedRepository
     */
    public function __construct(FeedRepository $feedRepository)
    {
        parent::__construct();
        $this->feedRepository = $feedRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $feedSlug = $this->argument('feed_slug');
        $limit = (int) $this->option('limit');

        try {
            $feed = $this->feedRepository->getFeedBySlug($feedSlug);

            Logger::withContext(['feed_slug' => $feedSlug, 'website' => $feed->website->name]);

            event(new PrepareImport($feed));

            $context = new ParserContext();

            $fileParseTo = (new FileParseTo())
                ->setFileLocation(FeedFileHelper::getFeedDownloadLocation($feed))
                ->setParseLimit($limit)
                ->setFeedType($feed->get(Feed::FIELD_TYPE));

            $rawDataBatch = $context->parse([$fileParseTo]);

            $feedMapTo = FeedMapToBuilderService::build($feed);

            $productTransformer = new ProductTransformer($feedMapTo);

            /** @var WoocommerceDataExport $exporter */
            $exporter = app(WoocommerceDataExport::class, [$feed->website]);

            $exportTo = (new FeedExportTo())
                ->setFeed($feed);

            Logger::info('FEED_PARSE_START');

            $totalExportedProducts = 0;

            foreach ($rawDataBatch as $dataBatch) {
                $transformedBatch = $productTransformer->transform($dataBatch);

                $exportTo->setData($transformedBatch);

                $batchPublishCount = $exporter->export($exportTo);

                $totalExportedProducts += $batchPublishCount;
            }

            Logger::info('FEED_EXPORT_COMPLETE', ['total_exported_products' => $totalExportedProducts]);

            ImportCompleted::dispatch($feed, $totalExportedProducts);

            $this->info('Imported products : '.$batchPublishCount);
        } catch (ModelNotFoundException $exception) {
            $this->error('FEED_NOT_FOUND');

            Logger::error('FEED_NOT_FOUND', ['feed_slug' => $feedSlug]);
            $this->error('ERROR_IMPORT: '.$exception->getMessage());
        } catch (FeedDownloadException $exception) {
            $this->error('FEED_DOWNLOAD_ERROR');

            Logger::error('FEED_DOWNLOAD_ERROR', ['exception' => $exception->getMessage()]);
            $this->error('ERROR_IMPORT: '.$exception->getMessage());
        } catch (\Throwable $exception) {
            $this->error(
                sprintf(
                    '%s%s%s%s%s%s',
                    $exception->getMessage(),
                    PHP_EOL,
                    $exception->getFile(),
                    PHP_EOL,
                    $exception->getLine(),
                    PHP_EOL
                )
            );
            Logger::error('FEED_IMPORT_COMMAND_ERROR', ['exception' => $exception->getMessage()]);

            $this->error('ERROR_IMPORT: '.$exception->getMessage());
        }

        $this->info('Import completed');

        return 1;
    }
}
