<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CorrectProgressTables
 */
class CorrectProgressTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sessions_recorded_progress');
        Schema::dropIfExists('sessions_recorded_participants_goals');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create(
            'sessions_recorded_progress',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('recorded_session_id');
                $table->uuid('poi_id')->nullable();
                $table->uuid('participant_id');
                $table->uuid('goal_id');
                $table->uuid('tracker_id');

                $table->timestamp('datetime')->useCurrent();

                $table->foreign('recorded_session_id')->references('id')->on('sessions_recorded')->onDelete('cascade');
                $table->foreign('poi_id')->references('id')->on('sessions_recorded_pois')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
                $table->foreign('goal_id')->references('id')->on('users_participants_goals')->onDelete('cascade');
                $table->foreign('tracker_id')->references('id')->on('users_participants_goals_trackers')->onDelete('cascade');
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
}
