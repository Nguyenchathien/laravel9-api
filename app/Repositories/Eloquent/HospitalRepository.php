<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Hospital;

class HospitalRepository extends BaseRepository implements HospitalRepositoryInterface
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
    public function __construct(Hospital $model)
    {
        $this->model = $model;
    }

    public function findById(
        int $modelId,
        array $columns = ['*'],
        array $relations = []
    ): ?Model {
        return $this->model->select($columns)->with($relations)->whereId($modelId)->Where(['chg' => CHG_VALID_VALUE])->first();
    }

    public function findByIdHospital($hospitalId)
    {
        return Hospital::where(['id' => $hospitalId])->first();
    }
}
