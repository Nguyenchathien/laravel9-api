<?php

namespace App\Repositories\Interfaces;
use App\Repositories\EloquentRepositoryInterface;

interface RecordItemRepositoryInterface extends EloquentRepositoryInterface
{
    public function getItem($date);
}