<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;

class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
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
    public function __construct(Favorite $model)
    {
        $this->model = $model;
    }
    public function findLike($favoriteId)
    {
        return Favorite::where(['id' => $favoriteId])->first();
    }
    public function updateLike($recordId)
    {
        return Favorite::where(['id' => $recordId])->update(['chg' => CHG_VALID_VALUE]);
    }

    public function findLiked($recordId)
    {
        return Favorite::where(['record' => $recordId, 'user' => Auth::id(), 'chg' => CHG_VALID_VALUE])->first();
    }

    public function findLikeRecordShared($recordId, $from)
    {
        return Favorite::where(['record' => $recordId, 'user' => $from, 'chg' => CHG_VALID_VALUE])->first();
    }
}
