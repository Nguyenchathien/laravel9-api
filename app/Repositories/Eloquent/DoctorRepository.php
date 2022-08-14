<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\DoctorRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\People;
use App\Repositories\BaseRepository;

class DoctorRepository extends BaseRepository implements DoctorRepositoryInterface
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
    public function __construct(People $model)
    {
        $this->model = $model;
    }

    public function findByIdDoctor($doctorId)
    {
        return People::where(['id' => $doctorId])->first();
    }
}
