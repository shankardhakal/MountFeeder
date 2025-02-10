<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Feed;
use App\Admin\Models\Rules;
use App\Repository\FeedRepository;
use App\Service\FeedTitleRowFetcher;
use App\Woocommerce\WoocommerceProduct;
use Doctrine\Common\Collections\Collection;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class RulesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Feed rules';

    protected FeedTitleRowFetcher $feedTitleRowFetcher;

    protected FeedRepository $feedRepository;

    /**
     * RulesController constructor.
     * @param  FeedTitleRowFetcher  $feedTitleRowFetcher
     * @param  FeedRepository  $feedRepository
     */
    public function __construct(FeedTitleRowFetcher $feedTitleRowFetcher, FeedRepository $feedRepository)
    {
        $this->feedTitleRowFetcher = $feedTitleRowFetcher;
        $this->feedRepository = $feedRepository;
    }

    public function rulesListing(Content $content, $feedId)
    {
        $feed = $this->feedRepository->findById($feedId);

        $fieldMapping = $feed->getFieldMapping();

        $mapped = [];
        $fieldMapping->map(
            function ($data) use (&$mapped) {
                $mapped[$data->woocommerce_field] = ['mappedTo' => $data->source_field, 'id' => $data->id];
            }
        );

        return $content->view(
            'mapping.rules',
            [
                'index'                    => 0,
                'feed'                     => $feed,
                'woocommerceProductFields' => WoocommerceProduct::getMappableFields(),
                'mapping'                  => $mapped,
                'feedFields'               => $this->feedTitleRowFetcher->fetch($feedId),
                'feedMapping'              => json_decode($feed->feed_mapping ?? '{}', true),
            ]
        );
    }

    public function addRule(Request $request)
    {
        $syntax = $request->input('syntax');
        $feed_id = $request->input('feed_id');
        $description = $request->input('description');
        $raw_syntax = $request->input('rawRule');

        $rule = new Rules(compact('syntax', 'feed_id', 'description', 'raw_syntax'));
        $rule->save();

        return response()->json(['message' => 'success']);
    }

    public function removeRule(Request $request)
    {
        $id = $request->input('id');
        $feedId = $request->input('feed_id');
        $rule = Rules::where(['id' => $id, 'feed_id' => $feedId])->first();

        if (null !== $rule) {
            $rule->delete();
        }

        return response()->json(['message' => 'success']);
    }
}
