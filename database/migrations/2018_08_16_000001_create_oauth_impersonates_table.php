<?php

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateOAuthImpersonatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('starter.impersonated_by_admin')) {
            Schema::create('oauth_impersonates', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->rowFormat = 'DYNAMIC';

                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('via_user_id')->unsigned();
                $table->string('impersonate_token')->unique();
                $table->string('access_token_id', 100)->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('access_token_id');
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
        Schema::dropIfExists('oauth_impersonates');
    }
}
