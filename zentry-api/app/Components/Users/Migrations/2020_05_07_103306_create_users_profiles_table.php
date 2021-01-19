<?php

use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersProfilesTable
 */
class CreateUsersProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users_profiles',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('user_id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('phone_code', 7)->nullable();
                $table->string('phone_number')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();

                $table->primary('id');

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        );

        DB::table('users')->get()->each(
            function (object $user) {
                DB::table('users_profiles')->insert(
                    [
                        'id' => IdentityGenerator::next()->toString(),
                        'user_id' => $user->id,
                        'first_name' => $user->email,
                        'last_name' => $user->email,
                    ]
                );
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
        Schema::dropIfExists('users_profiles');
    }
}
