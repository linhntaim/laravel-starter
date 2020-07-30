<?php

use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateUserSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (ConfigHelper::isSocialLoginEnabled()) {
            Schema::create('user_socials', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->rowFormat = 'DYNAMIC';

                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->string('provider');
                $table->string('provider_id');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->unique(['user_id', 'provider', 'provider_id']);
                $table->index('user_id');
                $table->index('created_at');
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
        Schema::dropIfExists('user_socials');
    }
}
