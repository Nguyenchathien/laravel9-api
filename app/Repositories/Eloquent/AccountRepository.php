<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AccountRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepository;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface {
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

    public function Search(Request $request)
    {
        $param = $request->q;

            $account =DB::table('users')
                ->orWhere(function ($query) use ($param) {
                    $query->where('name', 'like', '%'.$param. '%' )
                        ->where(['chg' => CHG_VALID_VALUE]);
                })
                ->orWhere(function ($query) use ($param) {
                    $query->where('email', 'like', '%'.$param. '%' )
                        ->where(['chg' => CHG_VALID_VALUE]);
                })
                ->get();

            return $account;
    }

    /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        return $this->findById($modelId)->delete();
    }

}
