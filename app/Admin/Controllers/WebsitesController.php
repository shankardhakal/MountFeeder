<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Website;
use App\Admin\Models\WebsiteConfiguration;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Symfony\Component\HttpFoundation\Request;

class WebsitesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Websites')
            ->description('All websites')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $websiteId
     * @param Content $content
     * @return Content
     */
    public function show($websiteId, Content $content)
    {
        return $content
            ->header('View website info')
            ->body($this->detail($websiteId));
    }

    /**
     * Edit interface.
     *
     * @param mixed $websiteId
     * @param Content $content
     * @return Content
     */
    public function edit($websiteId, Content $content)
    {
        $form = $this->form();

        return $content
            ->header('Edit')
            ->description('Edit website data.')
            ->withInfo('API Key and API Secret are encrypted.')
            ->body($form->edit($websiteId));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Add new website')
            ->description('Add website details')
            ->withInfo('API Key and API Secret are encrypted.')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Website);

        $grid->column('name', 'Website name')->display(function ($url) {
            return '<a href="'.url()->current().'/'.$this->id.'">'.$url.'</a>';
        });

        $grid->column('configuration.country', 'Country');

        $grid->column('view_feeds', 'Feeds')->display(function () {
            return
                sprintf(
                    '<a href="%s/%u/feeds">View Feeds</a>',
                    url()->current(),
                    $this->id
                );
        });

        $grid->column('feeds_count', 'Feed count')->display(function ($feeds) {
            return count($this->feeds);
        });

        $grid->column('url', 'Website URL')->display(function (string $url) {
            return sprintf('<a href="%s" target="_blank">%s</a>', $url, $url);
        });
        $grid->column('status')->display(function ($status) {
            return $status ? 'Active' : 'Inactive';
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $websiteId
     * @return Show
     */
    protected function detail($websiteId)
    {
        $website = Website::findOrFail($websiteId);

        unset($website->api_key, $website->api_secret);

        $show = new Show($website);

        return $show;
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $website = new Website($data);
        $website->url = filter_var($website->url, FILTER_SANITIZE_URL);

        $website->save();
        redirect('/admin/websites');
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Website);

        $form->text('name', 'Website name');
        $form->url('url', 'Website url')
            ->help('Enter website url');

        $form->text('api_key', 'WC Consumer Key')
            ->help('WC consumer key');
        $form->text('api_secret', 'WC Consumer Secret')
            ->help('WC consumer secret');

        $form->select('status', 'Status')
            ->options(['1' => 'Active', '0' => 'Inactive']);

        $configurations = WebsiteConfiguration::all(['id', 'country'])->pluck('country', 'id')->toArray();

        $form->select('configuration_id', 'Configuration')
            ->options($configurations)
            ->required()
            ->help('Website configuration for regional, language setting based on country.');

        return $form;
    }
}
