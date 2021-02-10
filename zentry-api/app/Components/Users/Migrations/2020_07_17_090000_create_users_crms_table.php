<?php

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersCRMSTable
 */
class CreateUsersCRMSTable extends Migration
{
    use UserServiceTrait;

    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function up()
    {
        Schema::create(
            'users_crms',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('user_id');
                $table->json('config');
                $table->string('driver', 40);
                $table->boolean('active');
                $table->boolean('notified');
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
        Schema::dropIfExists('users_crms');
    }
}
