<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsNotesTable
 */
class CreateSessionsNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions_pois_participants',
            function (Blueprint $table) {
                $table->primary('id');
            }
        );

        Schema::create(
            'sessions_notes',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('session_id');
                $table->uuid('participant_id')->nullable();
                $table->uuid('poi_id')->nullable();
                $table->uuid('poi_participant_id')->nullable();

                $table->mediumText('text');
                $table->string('url')->nullable();

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
                $table->foreign('poi_id')->references('id')->on('sessions_pois')->onDelete('cascade');
                $table->foreign('poi_participant_id')->references('id')->on('sessions_pois_participants')->onDelete('cascade');

                $table->primary('id');
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
        Schema::dropIfExists('sessions_notes');

        Schema::table(
            'sessions_pois_participants',
            function (Blueprint $table) {
                $table->dropPrimary('id');
            }
        );
    }
}
