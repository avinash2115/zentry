<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateParticipantsGoalsTable
 */
class CreateParticipantsGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_participants_goals',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('participant_id');

                $table->string('name');
                $table->mediumText('description');
                $table->boolean('reached')->default(0);
                $table->json('meta');

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->primary('id');
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
        Schema::dropIfExists('users_participants_goals');
    }
}
