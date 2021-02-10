<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIepIdToGoalsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users_participants_goals',
            function (Blueprint $table) {
                $table->uuid('iep_id')->nullable()->after('participant_id');

                $table->foreign('iep_id')->references('id')->on('users_participants_ieps')->onDelete('set null');
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
            'users_participants_goals',
            function (Blueprint $table) {
                $table->dropColumn('iep_id');
            }
        );
    }
}
