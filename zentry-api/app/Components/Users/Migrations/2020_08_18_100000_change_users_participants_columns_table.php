<?php

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeUsersParticipantsColumnsTable
 */
class ChangeUsersParticipantsColumnsTable extends Migration
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
            'users_participants',
            function (Blueprint $table) {
                $table->string('gender', 7)->nullable()->after('avatar');
                $table->timestamp('dob')->nullable()->after('gender');
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
            'users_participants',
            function (Blueprint $table) {
                $table->dropColumn('gender');
                $table->dropColumn('dob')->change();
            }
        );
    }
}
