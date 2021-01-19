<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableScheduledOnSessions
 */
class AlterTableScheduledOnSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions',
            static function (Blueprint $table) {
                $table->dateTime('scheduled_on')->after('ended_at')->nullable();
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
            'sessions',
            static function (Blueprint $table) {
                $table->dropColumn('scheduled_on');
            }
        );
    }
}
