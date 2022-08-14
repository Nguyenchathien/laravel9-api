<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class RecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=array(
            array(
                'type' => 'X3633',
                'begin' => '2022-05-05 04:41:06',
                'end' => '2022-05-05 04:41:26',
                'title'=>'Record 1',
                'hospital' => 1,
                'people' => 1,
                'user' => 57,
                'folder' => 1,
                'media' => 11,
                'visible' => 'Y',
                'chg'=>'Y',
                'new_by'=>'57',
                'new_ts' => Carbon::now(),
                'upd_by' => '47',
                'upd_ts' => Carbon::now()
            ),
            array(
                'type' => 'X3633',
                'begin' => '2022-05-05 04:41:06',
                'end' => '2022-05-05 04:41:26',
                'title'=>'Record 1',
                'hospital' => 1,
                'people' => 1,
                'user' => '57',
                'folder' => 1,
                'media' => 12,
                'visible' => 'Y',
                'chg'=>'Y',
                'new_by'=> '57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),
            array(
                'type' => 'X3633',
                'begin' => '2022-05-05 04:41:06',
                'end' => '2022-05-05 04:41:26',
                'title'=>'Record 1',
                'hospital' => 1,
                'people' => 1,
                'user' => '57',
                'folder' => 1,
                'media' => 13,
                'visible' => 'Y',
                'chg'=>'Y',
                'new_by'=> '57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),
            array(
                'type' => 'X3633',
                'begin' => '2022-05-05 04:41:06',
                'end' => '2022-05-05 04:41:26',
                'title'=>'Record 1',
                'hospital' => 1,
                'people' => 1,
                'user' => '58',
                'folder' => 1,
                'media' => 14,
                'visible' => 'Y',
                'chg'=>'Y',
                'new_by'=> '58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'type' => 'X3633',
                'begin' => '2022-05-05 04:41:06',
                'end' => '2022-05-05 04:41:26',
                'title'=>'Record 1',
                'hospital' => 1,
                'people' => 1,
                'user' => '58',
                'folder' => 1,
                'media' => 15,
                'visible' => 'Y',
                'chg'=>'Y',
                'new_by'=> '58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
        );

        DB::table('records')->insert($data);
    }
}
