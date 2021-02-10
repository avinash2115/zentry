<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToParticipantsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users_participants',
            function (Blueprint $table) {
                $table->uuid('school_id')->nullable();

                $table->foreign('school_id')->references('id')->on('users_teams_schools')->onDelete('RESTRICT');
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
            'users_participants',
            function (Blueprint $table) {
                $table->dropForeign('users_participants_school_id_foreign');
                $table->dropColumn('school_id');
            }
        );
    }
}
