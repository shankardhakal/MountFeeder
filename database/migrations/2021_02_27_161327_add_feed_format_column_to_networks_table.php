<?php

declare(strict_types=1);

use App\Import\Enum\FeedTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeedFormatColumnToNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'networks',
            function (Blueprint $table) {
                $table->enum(
                    'feed_format',
                    [
                        FeedTypeEnum::FEED_TYPE_CSV,
                        FeedTypeEnum::FEED_TYPE_RSS,
                        FeedTypeEnum::FEED_TYPE_XML,
                        FeedTypeEnum::FEED_TYPE_JSON,
                    ]
                )->default(FeedTypeEnum::FEED_TYPE_CSV);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'networks',
            function (Blueprint $table) {
                $table->dropColumn('feed_format');
            }
        );
    }
}
