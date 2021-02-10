<?php

use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddEligibilityToParticipantsTherapyTable
 */
class AddEligibilityToParticipantsTherapyTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users_participants_therapies',
            function (Blueprint $table) {
                $table->string('eligibility', 10)->after('frequency')->default(TherapyReadonlyContract::ELIGIBILITY_TYPE_ONE_TIME);
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
            'users_participants_therapies',
            function (Blueprint $table) {
                $table->dropColumn('eligibility');
            }
        );
    }
}
