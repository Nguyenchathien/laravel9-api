<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShareFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_families', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from', 128);
            $table->string('to', 128)->nullable()->default(null);
            $table->string('mail', 255);
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
        Schema::dropIfExists('share_families');
    }
}
