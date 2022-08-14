<?php

namespace App\Repositories\Interfaces;

use App\Repositories\EloquentRepositoryInterface;

interface FavoriteRepositoryInterface extends EloquentRepositoryInterface 
{
    public function findLike($favoriteId);

    public function updateLike($recordId);

    public function findLiked($recordId);

}