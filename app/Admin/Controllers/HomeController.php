<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Website;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $content->header('Dashboard');
        $content->description('Shop information');

        $content->row(
            function (Row $row) {
                $box = new Box('Statistics', '<span><i class="fa list-group"></i> </span>');

                $box->style('success');
                $box->solid();

                $box->content(view('website-stats')->with('websites', Website::all()));
                $row->column(12, $box);
            }
        );

        return $content;
    }
}
