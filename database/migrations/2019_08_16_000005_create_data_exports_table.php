<?php

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateDataExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_exports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->increments('id');
            $table->integer('file_id')->unsigned()->nullable();
            $table->tinyInteger('state');
            $table->string('name');
            $table->longText('payload')->nullable();
            $table->timestamps();

            $table->foreign('file_id')->references('id')->on('handled_files')
                ->onUpdate('cascade')->onDelete('set null');

            $table->index('name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_exports');
    }
}
