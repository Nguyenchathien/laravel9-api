<?php

namespace App\Repositories\Interfaces;

use App\Repositories\EloquentRepositoryInterface;

interface ScheduleRepositoryInterface extends EloquentRepositoryInterface
{
    public function getSchedule($date, $user);
}