<?php

use App\Vendors\Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class UpdateByInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('managed_files', function (Blueprint $table) {
            $table->integer('created_by')->after('id')->unsigned()->nullable();

            $table->foreign('created_by')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('data_exports', function (Blueprint $table) {
            $table->integer('created_by')->after('id')->unsigned()->nullable();

            $table->foreign('created_by')->references('id')->on('users')
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
        Schema::table('managed_files', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });

        Schema::table('data_exports', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}
