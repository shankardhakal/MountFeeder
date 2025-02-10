<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Admin\Models\Feed;
use App\Import\Download\FileDownloader;
use App\Import\Dto\FileParseTo;
use App\Import\FeedFileHelper;
use App\Import\Parser\CsvFileParser;
use App\Repository\FeedRepository;
use App\Repository\FieldMappingRepository;
use App\Woocommerce\WoocommerceApi;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;

class FetchWoocommerceCategories
{
    protected CsvFileParser $csvFileParser;

    protected FileDownloader $downloader;

    protected FeedRepository $feedRepository;

    protected FieldMappingRepository $fieldMappingRepository;

    /**
     * FetchWoocommerceCategories constructor.
     * @param  CsvFileParser  $csvFileParser
     * @param  FileDownloader  $downloader
     * @param  FeedRepository  $feedRepository
     * @param  FieldMappingRepository  $fieldMappingRepository
     */
    public function __construct(
        CsvFileParser $csvFileParser,
        FileDownloader $downloader,
        FeedRepository $feedRepository,
        FieldMappingRepository $fieldMappingRepository
    ) {
        $this->csvFileParser = $csvFileParser;
        $this->downloader = $downloader;
        $this->feedRepository = $feedRepository;
        $this->fieldMappingRepository = $fieldMappingRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function handle($request, \Closure $next)
    {
        // TODO refactor - too much is happening here
        $feedId = (int) $request->segment(4);

        /** @var Feed $feed */
        $feed = $this->feedRepository->findById($feedId);

        $fieldMapping = $this->fieldMappingRepository
            ->findWoocommerceCategoriesByFeedId(
                $feed->get(Feed::FIELD_ID)
            )->first();

        if ($fieldMapping === null) {
            $fieldMapping = $this->fieldMappingRepository->findWoocommerceCategoriesByNetworkId(
                $feed->network->id
            );
        }

        $mappedTo = $fieldMapping->source_field;

        if (null === $mappedTo) {
            $request->merge(['no-network-mapping' => true]);

            return $next($request);
        }

        /** @var WoocommerceApi $woocommerceApi */
        $woocommerceApi = app()->make(WoocommerceApi::class, [$feed->website]);
        $categories = array_keys($woocommerceApi->getCategories()->pluck('id', 'slug')->toArray());

        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', '500');

        $fileSavePath = FeedFileHelper::getFeedDownloadLocation($feed);

        $parseFileTo = (new FileParseTo())
            ->setFileLocation($fileSavePath);

        $categoriesCollection = [];
        foreach ($this->csvFileParser->parseFile($parseFileTo) as $data) {
            $feedCategory = trim($data[$mappedTo] ?? '');

            if (! empty($feedCategory) && ! is_numeric($data[$mappedTo])) {
                $categoriesCollection[$data[$mappedTo]] = 1;
            }
        }

        if(empty($categoriesCollection)){
            foreach ($categories as $categorySlug){
                $categoriesCollection[$categorySlug]=1;
            }
        }

        unset($csvDataReader);

        $request->merge(
            [
                'websiteCategories' => $categories,
                'feed'              => $feed,
                'sourceCategories'  => array_keys($categoriesCollection),
            ]
        );

        return $next($request);
    }
}
