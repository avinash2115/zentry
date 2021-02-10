<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsParticipantsGoalsTable
 */
class CreateSessionsParticipantsGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'session_participants_goals',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('session_id');
                $table->uuid('participant_id');
                $table->uuid('goal_id');

                $table->timestamp('created_at')->useCurrent();

                $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
                $table->foreign('goal_id')->references('id')->on('users_participants_goals')->onDelete('cascade');
            }
        );

        Schema::create(
            'sessions_recorded_participants_goals',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('recorded_session_id');
                $table->uuid('participant_id');
                $table->uuid('goal_id');

                $table->timestamp('created_at')->useCurrent();

                $table->foreign('recorded_session_id')->references('id')->on('sessions_recorded')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
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
        Schema::dropIfExists('session_participants_goals');
        Schema::dropIfExists('sessions_recorded_participants_goals');
    }
}
