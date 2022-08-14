<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\MediaKeywordRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\MediaKeyword;

class MediaKeywordRepository extends BaseRepository implements MediaKeywordRepositoryInterface
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
    public function __construct(MediaKeyword $model)
    {
        $this->model = $model;
    }

    public function findBy(
        array $condition,
        array $columns = ['*'],
        array $relations = []
    ): ?Model {
        $this->applyConditions($condition);

        return $this->model->with($relations)->get($columns)->first();
    }
}
