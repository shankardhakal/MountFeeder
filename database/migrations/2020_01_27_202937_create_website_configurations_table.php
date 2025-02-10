<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('locale', ['nl_NL', 'en_US', 'de_De', 'fi_Fi'])->default('fi_Fi')->nullable(false);
            $table->string('country');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_configurations');
    }
}
