<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddTeamIdToParticipantsTable
 */
class AddTeamIdToParticipantsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function up()
    {
        Schema::table(
            'users_participants',
            function (Blueprint $table) {
                $table->uuid('team_id')->nullable();

                $table->foreign('team_id')->references('id')->on('users_teams')->onDelete('cascade');
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
                $table->dropForeign('users_participants_team_id_foreign');
                $table->dropColumn('team_id');
            }
        );
    }
}
