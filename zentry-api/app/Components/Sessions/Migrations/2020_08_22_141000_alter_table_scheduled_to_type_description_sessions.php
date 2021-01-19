<?php

use App\Components\Sessions\Session\SessionReadonlyContract;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterTableScheduledToTypeDescriptionSessions
 */
class AlterTableScheduledToTypeDescriptionSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sessions',
            static function (Blueprint $table) {
                $table->dateTime('scheduled_to')->after('scheduled_on')->nullable();
                $table->string('type',50)->after('name')->default(SessionReadonlyContract::TYPE_DEFAULT);
                $table->mediumText('description')->after('type');
            }
        );
        Schema::table(
            'sessions_recorded',
            static function (Blueprint $table) {
                $table->string('type',50)->after('name')->default(SessionReadonlyContract::TYPE_DEFAULT);
                $table->mediumText('description')->after('type');
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
            'sessions',
            static function (Blueprint $table) {
                $table->dropColumn('scheduled_to');
                $table->dropColumn('type');
                $table->dropColumn('description');
            }
        );

        Schema::table(
            'sessions_recorded',
            static function (Blueprint $table) {
                $table->dropColumn('type');
                $table->dropColumn('description');
            }
        );
    }
}
