<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableNameSessionsPois
 */
class AlterTableNameSessionsPois extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions_pois',
            static function (Blueprint $table) {
                $table->string('name')->after('type')->nullable()->default(null);
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
            'sessions_pois',
            static function (Blueprint $table) {
                $table->dropColumn('name');
            }
        );
    }
}
