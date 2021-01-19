<?php

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeUsersStoragesUsedCapacityColumnsTable
 */
class ChangeUsersStoragesUsedCapacityColumnsTable extends Migration
{
    use UserServiceTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users_storages',
            function (Blueprint $table) {
                $table->unsignedBigInteger('used')->change();
                $table->unsignedBigInteger('capacity')->change();
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
            'users_storages',
            function (Blueprint $table) {
                $table->integer('used')->change();
                $table->integer('capacity')->change();
            }
        );
    }
}
