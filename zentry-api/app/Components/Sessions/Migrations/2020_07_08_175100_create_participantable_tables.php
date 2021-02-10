<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateParticipantableTables
 */
class CreateParticipantableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sessions_recorded_participants',
            function (Blueprint $table) {
                $table->uuid('recorded_session_id');
                $table->uuid('participant_id');

                $table->unique(['recorded_session_id', 'participant_id'], 'recorded_participant_unique');

                $table->foreign('recorded_session_id')->references('id')->on('sessions_recorded')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
            }
        );

        Schema::create(
            'sessions_participants',
            function (Blueprint $table) {
                $table->uuid('session_id');
                $table->uuid('participant_id');

                $table->unique(['session_id', 'participant_id'], 'session_participant_unique');

                $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
            }
        );

        Schema::create(
            'sessions_recorded_pois_participants',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('poi_id');
                $table->uuid('participant_id');

                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('ended_at')->useCurrent();

                $table->foreign('poi_id')->references('id')->on('sessions_recorded_pois')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
            }
        );


        Schema::create(
            'sessions_pois_participants',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('poi_id');
                $table->uuid('participant_id');

                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('ended_at')->useCurrent();

                $table->foreign('poi_id')->references('id')->on('sessions_pois')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
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
        Schema::dropIfExists('sessions_recorded_participants');
        Schema::dropIfExists('sessions_recorded_pois_participants');
        Schema::dropIfExists('sessions_participants');
        Schema::dropIfExists('sessions_pois_participants');
    }
}
