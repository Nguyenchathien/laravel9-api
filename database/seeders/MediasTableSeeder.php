<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;
use URL;
class MediasTableSeeder extends Seeder
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
                'record' => 1,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'180x180.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '180x180',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 1,
                'fpath'=> URL::to('/') . '/files/' . 'images/1200x200.png',
                'fname'=>'200x200.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x200',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 2,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'200x300.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x300',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 2,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'180x180.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '180x180',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 3,
                'fpath'=> URL::to('/') . '/files/' . 'images/1200x200.png',
                'fname'=>'200x200.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x200',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 3,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'200x300.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x300',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 4,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'180x180.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '180x180',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 4,
                'fpath'=> URL::to('/') . '/files/' . 'images/1200x200.png',
                'fname'=>'200x200.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x200',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 5,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'200x300.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x300',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 5,
                'fpath'=> URL::to('/') . '/files/' . 'images/180x180.png',
                'fname'=>'200x300.png',
                'fdisk' => URL::to('/') . '/files/' . 'images/',
                'name' => '200x300',
                'mime'=> 'IMAGE',
                'fext'=>'png',
                'chg'=>'Y',
                'new_by'=>'58',
                'new_ts' => Carbon::now(),
                'upd_by' => '58',
                'upd_ts' => Carbon::now()
            ),
// =============================================================================
            array(
                'record' => 1,
                'fpath'=> URL::to('/') . '/files/' . 'audios/Bunkei.wav',
                'fname'=>'Bunkei.png',
                'fdisk' => URL::to('/') . '/files/' . 'audios/',
                'name' => 'Bunkei',
                'mime'=> 'AUDIO',
                'fext'=>'wav',
                'chg'=>'Y',
                'new_by'=>'57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 2,
                'fpath'=> URL::to('/') . '/files/' . 'audios/Kaiwa.wav',
                'fname'=>'Kaiwa.wav',
                'fdisk' => URL::to('/') . '/files/' . 'audios/',
                'name' => 'Kaiwa',
                'mime'=> 'AUDIO',
                'fext'=>'wav',
                'chg'=>'Y',
                'new_by'=>'57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 3,
                'fpath'=> URL::to('/') . '/files/' . 'audios/Kotoba.wav',
                'fname'=>'Kotoba.wav',
                'fdisk' => URL::to('/') . '/files/' . 'audios/',
                'name' => 'Kotoba',
                'mime'=> 'AUDIO',
                'fext'=>'wav',
                'chg'=>'Y',
                'new_by'=>'57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 4,
                'fpath'=> URL::to('/') . '/files/' . 'audios/Mondai1.m4a',
                'fname'=>'Mondai1.m4a',
                'fdisk' => URL::to('/') . '/files/' . 'audios/',
                'name' => 'Bunkei',
                'mime'=> 'AUDIO',
                'fext'=>'m4a',
                'chg'=>'Y',
                'new_by'=>'57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),
            array(
                'record' => 5,
                'fpath'=> URL::to('/') . '/files/' . 'audios/Mondai2.m4a',
                'fname'=>'Mondai2.m4a',
                'fdisk' => URL::to('/') . '/files/' . 'audios/',
                'name' => 'Mondai2',
                'mime'=> 'AUDIO',
                'fext'=>'m4a',
                'chg'=>'Y',
                'new_by'=>'57',
                'new_ts' => Carbon::now(),
                'upd_by' => '57',
                'upd_ts' => Carbon::now()
            ),

        );
        

        DB::table('medias')->insert($data);
    }
}
