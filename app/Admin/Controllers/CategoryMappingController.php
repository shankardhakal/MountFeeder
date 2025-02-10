<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Feed;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CategoryMappingController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Category mapping';

    /**
     * Make a grid builder.
     *
     * @return Content
     */
    protected function addMapping(Content $content, Request $request)
    {
        $feed = $request->get('feed');
        $categories = $request->get('websiteCategories');
        $sourceCategoryFields = $request->get('sourceCategories');

        $currentMapping = null;
        if ($feed !== null) {
            $currentMapping = json_decode($feed->category_mapping, true);
        }

        if (is_array($currentMapping)) {
            $currentMapping = array_unique($currentMapping);
        }

        return $content->view('mapping.feed-category', [

            'wocommerceCategories' => $categories,
            'csvCategoryFields' => $sourceCategoryFields,
            'feed' => $feed,
            'index' => 0,
            'currentMapping' => $currentMapping,
            'feedIdentification' => Crypt::encrypt($feed->id),

        ]);
    }

    protected function addCategoryMapping(Request $request)
    {
        $request->validate(
            [
                'category-mapping' => 'bail|required|array',
                'feed-identification' => 'bail|required|string',
            ]
        );

        $categoryMapping = array_filter($request->input('category-mapping'));
        $feedId = Crypt::decrypt($request->input('feed-identification'));

        $feed = Feed::find($feedId);
        $feed->category_mapping = json_encode($categoryMapping);
        $feed->save();

        return response()->json(['message' => 'success']);
    }
}
