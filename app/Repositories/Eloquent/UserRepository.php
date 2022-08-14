<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
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
    public function __construct(User $model)
    {
        $this->model = $model;
    }
    public function findByEmail($email)
    {
        return User::where(['email' => $email, 'chg' => CHG_VALID_VALUE])->get();
    }

    public function findByMail($email)
    {
        return User::where(['email' => $email, 'chg' => CHG_VALID_VALUE])->first();
    }

    public function findById(int $modelId, array $columns = ['*'], array $relations = []): ?Model
    {
        return User::select($columns)->with($relations)->whereId($modelId)->Where(['chg' => CHG_VALID_VALUE])->first();
    }

    public function findByAppleId($appleId)
    {
        return User::where(['apple_id' => $appleId, 'chg' => CHG_VALID_VALUE])->first();
    }
}
