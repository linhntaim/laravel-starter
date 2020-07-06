<?php

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateManagedFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managed_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->rowFormat = 'DYNAMIC';

            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('size')->default('0');
            $table->string('type')->nullable();
            $table->string('local_disk')->nullable();
            $table->string('local_url')->nullable();
            $table->string('local_path')->nullable();
            $table->string('cloud_disk')->nullable();
            $table->string('cloud_url')->nullable();
            $table->string('cloud_path')->nullable();
            $table->longText('inline')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('managed_files');
    }
}
