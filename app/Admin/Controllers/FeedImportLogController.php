<?php

namespace App\Admin\Controllers;

use App\Admin\Models\FeedImportLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;

class FeedImportLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Import Logs';

    public function index(Content $content)
    {
        $feedId = request()->segments()[3] ?? null;
        $content->header('Import logs');
        $content->description('Import logs for ');

        $feedImportLogs = FeedImportLog::where('feed_id', '=', $feedId)->latest()->take(20)->get();

        return $content->view('log-view', ['importLogs'=>$feedImportLogs]);
    }
}
