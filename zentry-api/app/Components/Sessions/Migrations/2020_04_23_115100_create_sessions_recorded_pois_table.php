<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsRecordedPoisTable
 */
class CreateSessionsRecordedPoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sessions_recorded_pois',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('recorded_session_id');
                $table->string('type', 20);
                $table->json('tags');
                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('ended_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent();

                $table->primary('id');
                $table->foreign('recorded_session_id')->references('id')->on('sessions_recorded')->onDelete('cascade');
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
        Schema::dropIfExists('sessions_recorded_pois');
    }
}
