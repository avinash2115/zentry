<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsRecordedStreamsTable
 */
class CreateSessionsRecordedStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sessions_recorded_streams',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('recorded_session_id');
                $table->string('type', 20);
                $table->string('name');
                $table->text('url');

                $table->timestamp('created_at')->useCurrent();

                $table->primary('id');
                $table->unique(['recorded_session_id', 'type']);
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
        Schema::dropIfExists('sessions_recorded_streams');
    }
}
