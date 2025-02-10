<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnDeleteToFeedImportLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feed_import_logs', function (Blueprint $table) {
            $table->dropForeign(['feed_id']);
            $table->foreign('feed_id')
                ->references('id')->on('feeds')
                ->onDelete('cascade')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feed_import_logs', function (Blueprint $table) {
            //
        });
    }
}
