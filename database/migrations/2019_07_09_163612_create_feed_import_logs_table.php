<?php

use App\Admin\Models\FeedImportLog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedImportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_import_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('log_type', FeedImportLog::$levels)->nullable(true)->default(null);
            $table->text('message')->default(null)->nullable();
            $table->integer('feed_id')
                ->nullable()
                ->unsigned();

            $table->foreign('feed_id')
                ->references('id')
                ->on('feeds');

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
        Schema::dropIfExists('feed_import_logs');
    }
}
