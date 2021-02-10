<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsStreamsTable
 */
class CreateSessionsStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sessions_streams',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('session_id');
                $table->string('type', 20);
                $table->string('name');
                $table->text('url');

                $table->timestamp('created_at')->useCurrent();

                $table->primary('id');
                $table->unique(['session_id', 'type']);
                $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
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
        Schema::dropIfExists('sessions_streams');
    }
}
