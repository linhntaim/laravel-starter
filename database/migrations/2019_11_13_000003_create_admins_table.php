<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned()->nullable();
            $table->integer('avatar_id')->unsigned()->nullable();
            $table->string('display_name')->nullable();
            $table->string('email_verified_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('avatar_id')->references('id')->on('handled_files')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary('user_id');
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
        Schema::dropIfExists('admins');
    }
}
