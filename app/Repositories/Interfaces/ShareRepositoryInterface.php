<?php

namespace App\Repositories\Interfaces;

use App\Repositories\EloquentRepositoryInterface;

interface ShareRepositoryInterface extends EloquentRepositoryInterface
{
    public function accepted($id, array $data);
    public function getListSharing($id);
    public function getListSharedRecords($fromId, $toId);
    public function getListReceivedRecords();
}