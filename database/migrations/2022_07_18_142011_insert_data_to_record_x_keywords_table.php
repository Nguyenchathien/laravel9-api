<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataToRecordXKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('record_x_keywords', function (Blueprint $table) {
            $data = DB::table('records')->whereNotNull('medicine')->where(['chg' => CHG_VALID_VALUE])->get();
            DB::table('records')->whereNotNull('medicine')->where(['chg' => CHG_VALID_VALUE, 'id' => 484])->delete();
            foreach ($data as $key => $item) {
                $medicines = json_decode($item->medicine);
                foreach ($medicines as $medicine) {
                    DB::table('record_x_keywords')->insert(
                        array(
                            'type' => MEDICINE_KEY_VALUE,
                            'record' => $item->id,
                            'keyword' => $medicine,
                            'chg' => $item->chg,
                            'new_by' => $item->new_by,
                            'new_ts' => $item->new_ts,
                            'upd_by' => $item->upd_by,
                            'upd_ts' => $item->upd_ts,
                        )
                    );
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('record_x_keywords', function (Blueprint $table) {
            //
        });
    }
}
