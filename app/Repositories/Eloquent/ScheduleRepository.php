<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class ScheduleRepository extends BaseRepository implements ScheduleRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Schedule $model)
    {
        $this->model = $model;
    }

    public function getSchedule($date, $user)
    {
        return Schedule::whereDate('date', '>=',  $date)->where(['chg' => CHG_VALID_VALUE, 'user' => $user])->get();
    }
}
