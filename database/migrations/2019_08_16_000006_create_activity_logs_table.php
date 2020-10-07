<?php

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('starter.activity_log_enabled')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->rowFormat = 'DYNAMIC';

                $table->bigIncrements('id')->unsigned();
                $table->integer('device_id')->unsigned();
                $table->string('client');
                $table->string('screen');
                $table->string('action');
                $table->longText('screens')->nullable();
                $table->longText('payload')->nullable();
                $table->timestamps();

                $table->foreign('device_id')->references('id')->on('devices')
                    ->onUpdate('cascade')->onDelete('cascade');

                $table->index('client');
                $table->index('screen');
                $table->index('action');
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
        Schema::dropIfExists('activity_logs');
    }
}
