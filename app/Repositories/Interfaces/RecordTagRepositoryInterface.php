<?php

namespace App\Repositories\Interfaces;

use App\Repositories\EloquentRepositoryInterface;

interface RecordTagRepositoryInterface extends EloquentRepositoryInterface 
{
    public function getRecordtags($recordItemId);
}