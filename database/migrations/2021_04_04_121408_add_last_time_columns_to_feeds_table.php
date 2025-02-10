<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastTimeColumnsToFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feeds', function (Blueprint $table) {
            if (!Schema::hasColumn('feeds', 'last_import_at')) {
                $table->dateTime('last_import_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::table('feeds', function (Blueprint $table) {
            if (Schema::hasColumn('feeds', 'last_import_at')) {
                $table->dropColumn('last_import_at');
            }
        });
    }
}
