<?php

namespace App\Repositories\Eloquent;

use App\Models\ShareFamily;
use App\Repositories\Interfaces\ShareFamilyRepositoryInterface;
use App\Repositories\BaseRepository;

class ShareFamilyRepository extends BaseRepository implements ShareFamilyRepositoryInterface
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
    public function __construct(ShareFamily $model)
    {
        $this->model = $model;
    }
}
