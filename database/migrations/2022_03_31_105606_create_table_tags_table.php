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
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 24);
            $table->string('name', 128);
            $table->string('color', 6);
            $table->string('user', 128)->nullable()->default(null);
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
        Schema::dropIfExists('tags');
    }
};
