<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertDataMedicineColumnInRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $data = DB::table('records')->whereNotNull('medicine')->where(['chg' => CHG_VALID_VALUE])->get();
            foreach ($data as $item){
                $arr = [];
                $medicineCurrent = (int) $item->medicine;
                array_push($arr, $medicineCurrent);
                DB::table('records')->where('id', $item->id)->update(['medicine' => json_encode($arr)]);
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
        Schema::table('records', function (Blueprint $table) {
            //
        });
    }
}
