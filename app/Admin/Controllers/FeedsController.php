<?php

namespace App\Admin\Controllers;

use App\Actions\FeedCustomActions;
use App\Admin\Models\Feed;
use App\Admin\Models\FieldMapping;
use App\Admin\Models\Network;
use App\Admin\Models\Website;
use App\Http\Controllers\Controller;
use App\Repository\FeedRepository;
use App\Service\FeedTitleRowFetcher;
use App\Woocommerce\WoocommerceProduct;
use Carbon\Carbon;
use Cocur\Slugify\Slugify;
use Cron\CronExpression;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FeedsController extends Controller
{
    use HasResourceActions;

    /**
     * @var FeedTitleRowFetcher
     */
    private FeedTitleRowFetcher $feedTitleRowFetcher;

    /**
     * @var FeedRepository
     */
    private FeedRepository $feedRepository;

    /**
     * FeedsController constructor.
     * @param  FeedTitleRowFetcher  $feedTitleRowFetcher
     * @param  FeedRepository  $feedRepository
     */
    public function __construct(
        FeedTitleRowFetcher $feedTitleRowFetcher,
        FeedRepository $feedRepository
    ) {
        $this->feedTitleRowFetcher = $feedTitleRowFetcher;
        $this->feedRepository = $feedRepository;
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(
            function (Content $content) {
                $content->header('Feed List');
                $content->description('Lists of all the feeds available');
                $grid = $this->grid()->actions(
                    function (Actions $actions) {
                    }
                );

                $grid->getFilter()->like('slug', 'Feed slug');

                $grid->column('id')->sortable();

                $grid->expandFilter();

                $content->body($grid);
            }
        );
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(
            function (Content $content) use ($id) {
                $editForm = $this->form()->edit($id);

                $content->header('Edit feed info - '.$editForm->model()->store_name);

                $content->body($editForm);
            }
        );
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(
            function (Content $content) {
                $content->header('Add Feeds');
                $content->description('Add new feed');

                $form = $this->form();
                $content->body($form);
            }
        );
    }

    public function show($id)
    {
        $segments = \request()->segments();

        if (count(
            $segments
        ) === 5 && $segments['0'] === 'admin' && $segments[1] === 'websites' && $segments[3] === 'feeds') {
            $id = $segments[4];
        }

        return $this->edit($id);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(
            Feed::class,
            function (Grid $grid) {
                $segments = \request()->segments();

                $websiteId = null;

                if (($segments[1] ?? '') === 'websites' && ($segments[3] ?? '') === 'feeds') {
                    $websiteId = $segments[2];
                    $grid->model()->where('website_id', '=', $websiteId);
                    $grid->setTitle('Feeds for website '.Website::findOrFail($websiteId)->name);
                }

                $grid->store_name('Shop Name')->display(
                    function () {
                        return '<a href="'.url()->current().'/'.$this->id.'">'.$this->store_name.'</i></a>';
                    }
                );

                $grid->column('run_at', 'Next Import ')->display(
                    function ($runAt) {
                        try {
                            return CronExpression::factory($runAt)->getNextRunDate()->format('l, d M Y H:i');
                        } catch (\Exception $exception) {
                        }

                        return $runAt;
                    }
                );

                $grid->column('clean_at', 'Next Clean')->display(
                    function ($cleanAt) {
                        try {
                            return CronExpression::factory($cleanAt)->getNextRunDate()->format('l, d M Y H:i');
                        } catch (\Exception $exception) {
                        }

                        return $cleanAt;
                    }
                );

                $grid->column('last_import_at', 'Last Import')->display(
                    function ($lastImport) {
                        if ('0000-00-00 00:00:00' !== $lastImport) {

                            /*
                             * @var Carbon
                             */
                            return Carbon::createFromTimeString(
                                $lastImport
                            )->format('l, d M Y H:i');
                        }

                        return null;
                    }
                )->sortable();

                $grid->column('time_since_last_import', 'Duration')
                    ->display(
                        function () {
                            if ('0000-00-00 00:00:00' !== $this->last_import_at) {
                                return Carbon::createFromTimeString(
                                $this->last_import_at
                            )->diffForHumans(now());
                            }

                            return null;
                        }
                    );

                if (null === $websiteId) {
                    $grid->column('website_id', 'Website')->display(
                        function ($websiteId) {
                            $website = '';
                            if (null !== $websiteId) {
                                $website = Website::find($websiteId)->name;
                            }

                            return '<a href="websites/'.$websiteId.'">'.$website.'</a>';
                        }
                    )->sortable();
                }
                $grid->column('is_active', 'Status')->display(
                    function ($isActive) {
                        return $isActive ? 'Active' : 'Inactive';
                    }
                );

                $grid->actions(
                    function ($action) {
                        $action->add(new FeedCustomActions(Feed::find($action->getKey())));
                    }
                );

                $grid->created_at()->sortable();
            }
        );
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id = null)
    {
        Admin::headerJs(asset('js/jqCron.js'));
        Admin::headerJs(asset('js/jqCron.en.js'));
        Admin::css(asset('css/jqCron.css'));
        Admin::headerJs(asset('js/custom.js'));

        return Admin::form(
            Feed::class,
            function (Form $form) use ($id) {
                Admin::script(
                    'if (undefined === window.loadCronJobScript){location.reload()}else {window.loadCronJobScript();};'
                );

                $runAt = '* * * * *';
                $cleanAt = '* * * * *';
                if ($id) {
                    $form->edit($id);
                    $runAt = $form->model()->run_at;
                    $cleanAt = $form->model()->clean_at;
                }

                $form->text(
                    'store_name',
                    'Webshop name'
                )->rules('required', ['required' => 'Store name is required.'])
                    ->required()
                    ->help('Name of the online store. This must match pa_verkoper attribute value');

                $websites = Website::all(['id', 'name'])->pluck('name', 'id')->toArray();

                $form->select('website_id', 'Website')
                    ->options($websites)
                    ->help('Website to import parsed data into');

                $form->text('slug')
                    ->help('Assigned automatically if not provided.')
                    ->value();

                $form->html('<div data-current-value="'.$runAt.'" class="run-at-cron-job"></div>', 'Run at')
                    ->required()
                    ->setWidth(10)
                    ->placeholder('Select date and time to import feed.');

                $form->html('<div data-current-value="'.$cleanAt.'" class="clean-at-cron-job"></div>', 'Clean at')
                    ->required()
                    ->setWidth(10)
                    ->placeholder('Select date and time to run cleanup.');

                $form->hidden('run_at', 'Run at')
                    ->setElementClass('run-at-cron-input')
                    ->value('');

                $form->hidden('clean_at', 'Clean at')
                    ->setElementClass('clean-at-cron-input')
                    ->value('');

                $form->number('cpc')
                    ->help('Cpc amount for this shop')
                    ->value(0.0);

                $networks = Network::all(['id', 'name'])->pluck('name', 'id')->toArray();

                $form->select('network_id', 'Network')
                    ->options($networks)
                    ->help('Network that feed belongs to');

                $form->select('is_active', 'Status')
                    ->options(['1' => 'Active', '0' => 'Inactive']);

                $form->url('feed_url', 'Feed Url')
                    ->rules(
                        'url|required',
                        ['url' => 'Must be a valid URL']
                    )
                    ->required()
                    ->help('Feed url to get the data.');
            }
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $website = Website::find($request['website_id']);
        $data['last_import_at'] = now();

        if ($data['slug'] === null) {
            $slugify = new Slugify();
            $data['slug'] = $slugify->slugify("{$website->name}-{$data['store_name']}");
        }

        $feed = new Feed($data);

        $feed->feed_url = filter_var($feed->feed_url, FILTER_SANITIZE_URL);

        $feed->save();

        return redirect('/admin/feeds/create');
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    protected function addFieldMapping(Request $request)
    {
        $request->validate(
            [
                'woocommerce-field' => ['bail', 'required'],
                'id'                => 'bail|required',
                'feed-id'           => 'bail|required',
            ]
        );

        $mappedTo = $request->input('mapped-to');
        $woocommerceField = $request->input('woocommerce-field');
        $fieldId = $request->input('id');
        $feedId = $request->input('feed-id');

        if (0 < $fieldId) {
            /**
             * @var Model $field
             */
            $field = FieldMapping::find($fieldId);

            if (empty($mappedTo)) {
                $field->delete();
            } else {
                $field->source_field = $mappedTo;
                $field->save();
            }
        } else {
            $field = new FieldMapping(
                [
                    'feed_id'           => $feedId,
                    'source_field'      => $mappedTo,
                    'woocommerce_field' => $woocommerceField,
                ]
            );
            $field->save();
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * @param  Content  $content
     * @param $feedId
     * @return Content
     */
    public function showMapping(Content $content, int $feedId)
    {
        $feed = $this->feedRepository->findById($feedId);

        $mapped = [];

        $feed->getFieldMapping()->map(
            function ($data) use (&$mapped) {
                $mapped[$data->woocommerce_field] = ['mappedTo' => $data->source_field, 'id' => $data->id];
            }
        );

        return $content->view(
            'mapping.csv-field-mapping',
            [
                'index'                    => 0,
                'feed'                     => $feed,
                'woocommerceProductFields' => WoocommerceProduct::getMappableFields(),
                'mapping'                  => $mapped,
                'feedFields'               => $this->feedTitleRowFetcher->fetch($feedId),
            ]
        );
    }
}
