<?php

use App\Components\CRM\Source\SourceReadonlyContract;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDirectionColumnToCrmsSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crms_sources', function (Blueprint $table) {
            $table->string('direction', 50)->after('crm_id')->default(SourceReadonlyContract::DIRECTION_IN);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crms_sources', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
    }
}
