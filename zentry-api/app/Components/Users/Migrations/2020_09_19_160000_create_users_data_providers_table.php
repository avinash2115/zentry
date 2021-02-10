<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersDataProvidersTable
 */
class CreateUsersDataProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_data_providers',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('user_id');
                $table->string('driver', 40);
                $table->json('config');
                $table->smallInteger('status');

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->unique(['user_id', 'driver']);

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('users_data_providers');
    }
}
