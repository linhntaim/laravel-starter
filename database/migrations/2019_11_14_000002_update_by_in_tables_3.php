<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class UpdateByInTables3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('starter.activity_log_enabled')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->integer('user_id')->after('device_id')->unsigned()->nullable();

                $table->foreign('user_id')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('starter.activity_log_enabled')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropForeign('activity_logs_user_id_foreign');
                $table->dropColumn('created_by');
            });
        }
    }
}
