<?php

declare(strict_types=1);

namespace App\Actions;

use App\Admin\Models\Feed;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Admin;

class FeedCustomActions extends RowAction
{
    private Feed $feed;

    private $networkId;

    public function __construct(Feed $feed)
    {
        parent::__construct();
        $this->feed = $feed;
        $this->networkId = $feed->network->id ?? '';
    }

    public function handle()
    {
        $this->render();
    }

    protected function script()
    {
        return <<<'SCRIPT'
var fired = false;
     $(document).ready(function(){
$('.feed-import-action').off().on('click', function() {

    var id = $(this).data('id');
    var action = $(this).data('action');
    return new Promise(function (resolve) {
        $.ajax({
            method: 'post',
            url: '/admin/helpers/terminal/artisan',
            data: {
                 c: action,
                _token: LA.token,
            },
            success: function (data) {
              toastr.success('Feed action '+action+' started.')
            }
        });
    });

})});

SCRIPT;
    }

    public function render()
    {
        Admin::script($this->script());

        return <<<HTML
<style>
.dropdown:hover>.dropdown-menu {
  display: block;
}
.box-body{
display: contents !important;
}
</style>
<div class="btn-group">
  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Feed actions
  </button>
  <div class="dropdown-menu">
<a style="padding: 5px; font-size: small" class="dropdown-item" href="/admin/feeds/{$this->feed->id}/add-mapping">Field Mapping</a><br>
<a style="padding: 5px; font-size: small" class="dropdown-item" href="/admin/feeds/{$this->feed->id}/add-rules"  >Rules</a><br>
<a style="padding: 5px; font-size: small" class="dropdown-item" data-id="{$this->feed->id}" href="/admin/feeds/category-mappings/{$this->feed->id}">Category Mapping</a><br>
<a style="padding: 5px; font-size: small" class="dropdown-item feed-import-action" href="#"  data-action="import:feed {$this->feed->slug}" >Import</a><br>
<a style="padding: 5px; font-size: small" class="dropdown-item feed-import-action" href="#"  data-action="feed:download {$this->feed->slug}" >Download</a><br>
<a style="padding: 5px; font-size: small" class="dropdown-item feed-import-action" href="#" data-action="clean {$this->feed->slug}" >Clean</a><br>
<a style="padding: 5px; font-size: small" class="dropdown-item feed-import-action" href="#" data-action="clean {$this->feed->slug} -D" >Clean Completely</a><br>
  </div>
</div>
HTML;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
