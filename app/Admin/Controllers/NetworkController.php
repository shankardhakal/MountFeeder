<?php

namespace App\Admin\Controllers;

use App\Admin\Models\FieldMapping;
use App\Admin\Models\Network;
use App\Woocommerce\WoocommerceProduct;
use Doctrine\Common\Collections\Collection;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class NetworkController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Networks';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Network);

        $grid->column('name', __('Name'));
        $grid->actions(
            function (Grid\Displayers\DropdownActions $actions) {
                $actions->add(
                    new class() extends RowAction {
                        public function href()
                        {
                            echo '<a href="'.url()->current().'/'.$this->getKey().'/add-mapping'.'" class="btn btn-default">'.__('Modify mapping').'</a>';
                        }
                    }
                );
                // --
            }
        );

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
        $show = new Show(Network::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('mapping', __('Mapping'));
        $show->field('fields', __('Fields'));
        $show->field('feed_url', __('Feed url'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Network);

        $form->text('name', __('Name'));
        $form->url('sample_feed_url', __('Sample Feed URL'));

        return $form;
    }

    public function addMapping(Content $content, $networkId)
    {
        /**
         * @var Network $network
         */
        $network = Network::find($networkId);

        /**
         * @var Collection $fieldMapping
         */
        $fieldMapping = $network->fieldMapping;

        $mapped = [];
        $fieldMapping->map(
            function ($data) use (&$mapped) {
                $mapped[$data->woocommerce_field] = ['mappedTo' => $data->source_field, 'id' => $data->id];
            }
        );

        return $content->view(
            'mapping.network-fields',
            [
                'index'                    => 0,
                'network'                  => $network,
                'woocommerceProductFields' => WoocommerceProduct::getMappableFields(),
                'mapping'                  => $mapped,
                'networkFields'            => json_decode($network->fields, true),
            ]
        );
    }

    protected function putFieldMapping(Request $request)
    {
        $request->validate(
            [
                'woocommerce-field' => ['bail', 'required'],
                'id'                => 'bail|required',
                'network-id'        => 'bail|required',
            ]
        );

        $mappedTo = $request->input('mapped-to');
        $woocommerceField = $request->input('woocommerce-field');
        $fieldId = $request->input('id');
        $networkId = $request->input('network-id');

        if (0 < $fieldId) {
            $field = FieldMapping::find($fieldId);

            if (empty($mappedTo)) {
                $field->delete();
            } else {
                $field->source_field = $mappedTo;
                $field->save();
            }
        } else {
            $field = new FieldMapping(
                ['network_id' => $networkId, 'source_field' => $mappedTo, 'woocommerce_field' => $woocommerceField]
            );
            $field->save();
        }

        return response()->json(['message' => 'success']);
    }
}
