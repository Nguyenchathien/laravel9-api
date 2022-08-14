<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepository;
use App\Models\Tag;

class TagRepository extends BaseRepository implements TagRepositoryInterface {
        /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Tag $model)
    {
        $this->model = $model;
    }
}