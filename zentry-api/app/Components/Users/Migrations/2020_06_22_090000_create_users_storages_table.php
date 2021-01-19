<?php

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersStoragesTable
 */
class CreateUsersStoragesTable extends Migration
{
    use UserServiceTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_storages',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('user_id');
                $table->json('config');
                $table->string('driver', 40);
                $table->string('name', 40);
                $table->boolean('enabled');
                $table->integer('used');
                $table->integer('capacity');

                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->unique(['user_id', 'driver']);

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->primary('id');
            }
        );

        DB::table('users')->get()->each(function(object $user) {
            DB::table('users_storages')->insert([
                'id' => IdentityGenerator::next()->toString(),
                'config' => json_encode([]),
                'name' => str_replace(StorageReadonlyContract::LABEL_PLACEHOLDER_APP_NAME, env('APP_NAME', 'Zentry'), StorageReadonlyContract::AVAILABLE_DRIVERS[StorageReadonlyContract::DRIVER_DEFAULT]),
                'driver' => StorageReadonlyContract::DRIVER_DEFAULT,
                'user_id' => $user->id,
                'used' => 0,
                'capacity' => 0,
                'enabled' => 1,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_storages');
    }
}
