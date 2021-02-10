<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableThumbnailSessionsRecordedPoi
 */
class AlterTableThumbnailSessionsRecordedPoi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions_recorded_pois',
            static function (Blueprint $table) {
                $table->text('thumbnail')->nullable()->after('tags');
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
            'sessions_recorded_pois',
            static function (Blueprint $table) {
                $table->dropColumn('thumbnail');
            }
        );
    }
}
