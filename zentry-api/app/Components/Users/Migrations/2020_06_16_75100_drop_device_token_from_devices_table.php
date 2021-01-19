<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class DropDeviceTokenFromDevicesTable
 */
class DropDeviceTokenFromDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users_devices',
            function (Blueprint $table) {
                $table->dropForeign('users_devices_user_id_foreign');
                $table->dropUnique('users_devices_user_id_device_token_unique');
                $table->dropColumn('device_token');

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table(
            'users_devices',
            function (Blueprint $table) {
                $table->string('device_token');
                $table->unique(['user_id', 'device_token']);
            }
        );

        Schema::dropIfExists('users_devices');
    }
}
