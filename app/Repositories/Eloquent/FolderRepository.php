<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\FolderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepository;
use App\Models\Folder;

class FolderRepository extends BaseRepository implements FolderRepositoryInterface {
        /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Folder $model)
    {
        $this->model = $model;
    }
}