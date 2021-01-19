<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUsersSchoolsTable
 */
class CreateUsersSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_teams_schools',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('team_id');
                $table->string('name');
                $table->integer('available');
                $table->text('street_address')->nullable();
                $table->text('city')->nullable();
                $table->text('state')->nullable();
                $table->text('zip')->nullable();

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->foreign('team_id')->references('id')->on('users_teams')->onDelete('RESTRICT');

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
        Schema::dropIfExists('users_teams_schools');
    }
}
