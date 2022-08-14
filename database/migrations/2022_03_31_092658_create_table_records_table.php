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
        Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 24);
            $table->dateTime('begin', $precision = 0);
            $table->dateTime('end', $precision = 0);
            $table->string('title', 128)->nullable()->default(null);
            $table->integer('hospital')->nullable()->default(null);
            $table->integer('people')->nullable()->default(null);
            $table->string('user', 128)->nullable()->default(null);
            $table->integer('folder')->nullable()->default(null);
            $table->integer('media')->nullable()->default(null);
            $table->char('visible', 1)->default('Y');
            $table->char('chg', 1)->default('Y');
            $table->string('new_by', 128);
            $table->dateTime('new_ts', $precision = 0)->useCurrent();
            $table->string('upd_by', 128)->default(null);
            $table->dateTime('upd_ts', $precision = 0)->default(null);

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
};
