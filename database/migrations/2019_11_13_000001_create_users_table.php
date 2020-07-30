<?php

use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->increments('id');
            if (ConfigHelper::isSocialLoginEnabled()) {
                $table->string('email')->nullable();
            } else {
                $table->string('email');
            }
            $table->string('password')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('email');
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
