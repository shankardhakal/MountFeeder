<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeginKeyConfigurationIdToWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
{
    Schema::table('websites', function (Blueprint $table) {
        // Add the column only if it does not already exist
        if (!Schema::hasColumn('websites', 'configuration_id')) {
            $table->bigInteger('configuration_id')->unsigned()->nullable();
        }

        // Add the foreign key constraint if necessary
        if (!Schema::hasTable('website_configurations')) {
            $table->foreign('configuration_id')->references('id')->on('website_configurations');
        }
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website', function (Blueprint $table) {
            //
        });
    }
}
