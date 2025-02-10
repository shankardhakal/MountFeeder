<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('woocommerce_field');
            $table->string('source_field')->nullable()->default(null);

            $table->unsignedBigInteger('network_id')
                ->nullable();

            $table->foreign('network_id')
                ->references('id')
                ->on('networks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('field_mappings');
    }
}
