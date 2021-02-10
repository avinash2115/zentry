<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateParticipantsTherapiesTable
 */
class CreateParticipantsTherapiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_participants_therapies',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('participant_id');

                $table->text('diagnosis');
                $table->string('frequency', 10);
                $table->integer('sessions_amount_planned')->default(0);
                $table->integer('treatment_amount_planned')->default(0);
                $table->text('notes');
                $table->text('private_notes');

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
        Schema::dropIfExists('users_participants_therapies');
    }
}
