<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsPoisTable
 */
class CreateSessionsPoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sessions_pois',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('session_id');
                $table->string('type', 20);
                $table->json('tags');
                $table->timestamp('started_at')->useCurrent();
                $table->timestamp('ended_at')->useCurrent();
                $table->timestamp('created_at')->useCurrent();

                $table->primary('id');
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
        Schema::dropIfExists('sessions_pois');
    }
}
