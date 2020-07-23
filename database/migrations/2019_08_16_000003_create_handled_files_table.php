<?php

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateHandledFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handled_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('mime')->nullable();
            $table->string('size')->default('0');
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('handled_file_stores', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->increments('id');
            $table->string('handled_file_id')->nullable();
            $table->string('store');
            $table->longText('data');

            $table->foreign('handled_file_id')->references('id')->on('handled_files')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('handled_file_stores');
        Schema::dropIfExists('handled_files');
    }
}
