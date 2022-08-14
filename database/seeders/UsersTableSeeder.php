<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
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
                'type' => 'email',
                'email'=>'hiennc@zen-s.com',
                'name'=>'Hien Nguyen',
                'temail' => 'hiennc@zen-s.com',
                'password'=>Hash::make('123456'),
                'key'=>'',
                'token'=>'',
                'gender'=>'nam',
                'plan'=>'FREE',
                'expired' => Carbon::now()->addDays(20),
                'status'=>'X52000',
                'progress' => '',
                'chg'=>'Y',
                'new_by'=>'Admin',
                'upd_by' => 'Admin',
                'upd_ts' => Carbon::now()
            )
        );

        DB::table('users')->insert($data);
    }
}
