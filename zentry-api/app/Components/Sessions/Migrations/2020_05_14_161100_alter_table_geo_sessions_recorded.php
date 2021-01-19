<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableGeoSessionsRecorded
 */
class AlterTableGeoSessionsRecorded extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions_recorded',
            static function (Blueprint $table) {
                $table->json('geo')->nullable()->change();
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
            'sessions_recorded',
            static function (Blueprint $table) {
                $table->string('geo', 40)->nullable()->change();
            }
        );
    }
}
