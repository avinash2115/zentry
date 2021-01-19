<?php

use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddColumnsTrackers
 */
class AddColumnsTrackers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users_participants_goals_trackers',
            function (Blueprint $table) {
                $table->string('type', 10)->after('name')->default(TrackerReadonlyContract::TYPE_NEUTRAL);
                $table->string('color', 10)->after('icon')->default('#ffc107');
            }
        );

        DB::table('users_participants_goals_trackers')->get()->each(
            function (object $item) {
                DB::table('users_participants_goals_trackers')->where('id', '=', $item->id)->update(
                    [
                        'type' => str_replace(
                            ['check-circle', 'times-circle', 'life-ring'],
                            [
                                TrackerReadonlyContract::TYPE_POSITIVE,
                                TrackerReadonlyContract::TYPE_NEGATIVE,
                                TrackerReadonlyContract::TYPE_NEUTRAL,
                            ],
                            $item->icon
                        ),
                        'color' => str_replace(
                            ['check-circle', 'times-circle', 'life-ring'],
                            ['#28a745', '#dc3545', '#ffc107'],
                            $item->icon
                        ),
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
        Schema::table(
            'users_participants_goals_trackers',
            function (Blueprint $table) {
                $table->dropColumn('type');
                $table->dropColumn('color');
            }
        );
    }
}
