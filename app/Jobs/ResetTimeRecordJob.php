<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResetTimeRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::where('chg', CHG_VALID_VALUE)->get();
        foreach ($users as $user) {
            if ($user->plan == VIP_PLAN_VALUE) {
                $user->time_record = TOTAL_TIME_RECORD_VIP_PLAN;
            } else {
                $user->time_record = TOTAL_TIME_RECORD_FREE_PLAN;
            }
            $user->save();
        }
    }
}
