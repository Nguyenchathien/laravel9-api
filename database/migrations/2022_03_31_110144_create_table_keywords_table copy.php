<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_x_keyword', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('keyword')->unsigned();
            $table->string('fpath', 1024)->nullable();
            $table->string('fname', 128)->nullable();
            $table->string('fdisk', 1024)->nullable();
            $table->string('vname', 1024)->nullable();
            $table->string('mime', 128)->nullable();
            $table->string('fext', 24)->nullable();
            $table->char('chg', 1)->default('Y');
            $table->string('new_by', 128);
            $table->dateTime('new_ts', $precision = 0)->useCurrent();
            $table->string('upd_by', 128)->default(null);
            $table->dateTime('upd_ts', $precision = 0)->default(null);

            $table->engine = 'InnoDB';
        });

        Schema::table('media_x_keyword', function($table) {
            $table->foreign('keyword')->references('id')->on('keywords');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_x_keyword');
    }
};
