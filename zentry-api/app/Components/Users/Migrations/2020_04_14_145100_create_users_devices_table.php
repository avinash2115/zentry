<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersDevicesTable
 */
class CreateUsersDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_devices',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('user_id');
                $table->string('type');
                $table->string('model');
                $table->string('reference');
                $table->string('device_token');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->primary('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unique(['user_id', 'device_token']);
                $table->unique('reference');
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
        Schema::dropIfExists('users_devices');
    }
}
