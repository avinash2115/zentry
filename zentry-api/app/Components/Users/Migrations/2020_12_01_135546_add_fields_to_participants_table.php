<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToParticipantsTable extends Migration
{

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
                $table->string('parent_email')->after('dob')->nullable();
                $table->string('parent_phone_number')->after('parent_email')->nullable();
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
                $table->dropColumn('parent_email');
                $table->dropColumn('parent_phone_number');
            }
        );
    }
}
