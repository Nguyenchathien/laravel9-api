<?php

namespace App\Repositories\Interfaces;

use App\Repositories\EloquentRepositoryInterface;

interface KeywordRepositoryInterface extends EloquentRepositoryInterface 
{
    public function getKeywords($recordId);
}