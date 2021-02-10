<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersParticipantsGoalsTrackersTable
 */
class CreateUsersParticipantsGoalsTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_participants_goals_trackers',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('goal_id');

                $table->string('name');
                $table->string('icon');

                $table->timestamp('created_at')->useCurrent();

                $table->primary('id');
                $table->foreign('goal_id')->references('id')->on('users_participants_goals')->onDelete('cascade');
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
        Schema::dropIfExists('users_participants_goals_trackers');
    }
}
