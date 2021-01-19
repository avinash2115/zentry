<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSessionsSoapsTable
 */
class CreateSessionsSoapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sessions_soaps',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('session_id');
                $table->uuid('participant_id');
                $table->uuid('goal_id')->nullable();

                $table->boolean('present');
                $table->string('rate');
                $table->text('activity');
                $table->text('note');
                $table->text('plan');

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
                $table->foreign('participant_id')->references('id')->on('users_participants')->onDelete('cascade');
                $table->foreign('goal_id')->references('id')->on('users_participants_goals')->onDelete('cascade');

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
        Schema::dropIfExists('sessions_soaps');
    }
}
