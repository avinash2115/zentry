<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableStreamSessionsRecordedPoi
 */
class AlterTableStreamSessionsRecordedPoi extends Migration
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
                $table->text('stream')->nullable()->after('thumbnail');
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
                $table->dropColumn('stream');
            }
        );
    }
}
