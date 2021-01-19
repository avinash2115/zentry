<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateParticipantsIepsTable
 */
class CreateParticipantsIepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_participants_ieps',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('participant_id');

                $table->timestamp('date_actual')->useCurrent();
                $table->timestamp('date_reeval')->useCurrent();

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
        Schema::dropIfExists('users_participants_ieps');
    }
}
