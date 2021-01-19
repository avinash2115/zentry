<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableConvertProgressSessionsRecordedStreams
 */
class AlterTableConvertProgressSessionsRecordedStreams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions_recorded_streams',
            static function (Blueprint $table) {
                $table->tinyInteger('convert_progress')->default(0)->after('url');
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
            'sessions_recorded_streams',
            static function (Blueprint $table) {
                $table->dropColumn('convert_progress');
            }
        );
    }
}
