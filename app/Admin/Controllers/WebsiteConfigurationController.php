<?php

namespace App\Admin\Controllers;

use App\Admin\Models\WebsiteConfiguration;
use App\Import\WebsiteLocale;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WebsiteConfigurationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Website configurations';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WebsiteConfiguration());

        $grid->column('id', __('Id'));
        $grid->column('locale', __('Locale'));
        $grid->column('country', __('Country'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param  mixed  $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WebsiteConfiguration::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('locale', __('Locale'));
        $show->field('country', __('Country'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WebsiteConfiguration());

        $form->radio('locale', __('Locale'))->default('nl_NL')->options(
            [
                WebsiteLocale::LOCALE_nl_NL => 'NL',
                WebsiteLocale::LOCALE_en_US => 'USA',
                WebsiteLocale::LOCALE_de_DE => 'DE',
                WebsiteLocale::LOCALE_fi_FI=> 'FI',
            ]
        );
        $form->radio('country', __('Country'))
            ->options(
                [
                    'NL'  => 'The Netherlands',
                    'USA' => 'United States of America',
                    'DE'  => 'Germany',
                    'FI' => 'Finland',
                ]
            );

        return $form;
    }

    public function store()
    {
        parent::store();
    }
}
