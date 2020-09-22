<?php

use App\Utils\SocialLogin;
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
            if (SocialLogin::getInstance()->enabled()) {
                $table->string('email')->nullable();
            } else {
                $table->string('email');
            }
            $table->string('password')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('email');
            $table->index('password_changed_at');
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
