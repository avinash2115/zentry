<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableUpdatedAtGeoSessions
 */
class AlterTableUpdatedAtGeoSessions extends Migration
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
                $table->timestamp('updated_at')->useCurrent();
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
            'sessions',
            static function (Blueprint $table) {
                $table->dropColumn('updated_at');
                $table->string('geo', 40)->nullable()->change();
            }
        );
    }
}
