<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableReferenceSessions
 */
class AlterTableReferenceSessions extends Migration
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
                $table->string('reference')->after('description')->nullable();
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
                $table->dropColumn('reference');
            }
        );
    }
}
