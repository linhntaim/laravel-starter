<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('cache.default') === 'database') {
            Schema::create('sys_cache', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->rowFormat = 'DYNAMIC';

                $table->string('key')->unique();
                $table->text('value');
                $table->integer('expiration');
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
        Schema::dropIfExists('sys_cache');
    }
}
