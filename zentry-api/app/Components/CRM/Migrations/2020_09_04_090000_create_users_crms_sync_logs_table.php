<?php

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersCRMSSourcesTable
 */
class CreateUsersCRMSSyncLogsTable extends Migration
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
            'crms_sync_logs',
            function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('crm_id');
                $table->string('type', 40);

                $table->timestamp('created_at')->useCurrent();

                $table->foreign('crm_id')->references('id')->on('users_crms')->onDelete('cascade');

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
        Schema::dropIfExists('crms_sync_logs');
    }
}
